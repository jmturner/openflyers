<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * inst_availability.class.php
 *
 * check instructor availibility
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program (file license.txt);
 * if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * @category   booking management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: inst_availability.class.php,v 1.15.2.6 2006/10/10 12:38:19 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

class instAvailibility
{
    var $timeOffset;
    var $isAvail;
    var $startQuarter;
    var $endQuarter;

    // Get instructor disponibility tables and compute availibity for each quarter of hour between $startDate and $endDate
    function instAvailibility($instructor, $database, $startDate, $endDate, $timeOffset=0)
    {
        $this->timeOffset   = $timeOffset;
        // init the range
        $startQuarter   = $this->getQuarterFromDate($startDate);
        $endQuarter     = $this->getQuarterFromDate($endDate);
        if ($startQuarter >= $endQuarter) {
            $endQuarter += 672; // 672 = 24 hours * 7 days * 4 quarters
        }
        $this->startQuarter = $startQuarter;
        $this->endQuarter   = $endQuarter;
        $this->isAvail = array_fill($startQuarter, $endQuarter, false);

        // check with regular availability first : if avail in the period, we set the according quarter TRUE, but after we check exceptionnal by-pass
        $database->query('select * from regular_presence_inst_dates where INST_NUM=\''.$instructor.'\'');
        while ($row = $database->fetch()) {
            $firstTime      = new ofDateSpan($row->START_DAY.' '.$row->START_HOUR);
            $lastTime       = new ofDateSpan($row->END_DAY.' '.$row->END_HOUR);
            $firstQuarter   = $firstTime->getQuarter();
            $lastQuarter    = $lastTime->getQuarter();
            $this->fillIsAvail($firstQuarter, $lastQuarter, true);
            if ($firstQuarter < $this->startQuarter) {
                $this->fillIsAvail($firstQuarter+672, $lastQuarter+672, true);
            }
            if (($firstQuarter > $lastQuarter) and ($firstQuarter > $this->startQuarter)) {
                $this->fillIsAvail($firstQuarter-672, $lastQuarter-672, true);
            }
        }
        $database->free();

        // now check with exceptionnal availibities
        $database->query('select * from exceptionnal_inst_dates where START_DATE<\''.$endDate->getDate().'\' and END_DATE>\''.$startDate->getDate().'\' and INST_NUM=\''.$instructor.'\' and PRESENCE=1');
        while ($row = $database->fetch()) {
            $firstDate  = new ofDate($row->START_DATE);
            $lastDate   = new ofDate($row->END_DATE);
            $firstI     = $firstDate->before($startDate)    ? $startQuarter : $this->getQuarterFromDate($firstDate);
            $lastI      = $lastDate->after($endDate)        ? $endQuarter   : $this->getQuarterFromDate($lastDate);
            if ($firstI < $startQuarter) {
                $firstI +=  672;
                $lastI +=   672;
            }
            $this->fillIsAvail($firstI, $lastI, true);
		}
		$database->free();

        // now check with exceptionnal unavailibities
        $database->query('select * from exceptionnal_inst_dates where START_DATE<\''.$endDate->getDate().'\' and END_DATE>\''.$startDate->getDate().'\' and INST_NUM=\''.$instructor.'\' and PRESENCE=0');
        while ($row = $database->fetch()) {
            $firstDate  = new ofDate($row->START_DATE);
            $lastDate   = new ofDate($row->END_DATE);
            $firstI     = $firstDate->before($startDate)    ? $startQuarter : $this->getQuarterFromDate($firstDate);
            $lastI      = $lastDate->after($endDate)        ? $endQuarter   : $this->getQuarterFromDate($lastDate);
            if ($firstI < $startQuarter) {
                $firstI +=  672;
                $lastI +=   672;
            }
            $this->fillIsAvail($firstI, $lastI, false);
		}
		$database->free();
    }

	/**
	* Say if the instructor is avaible or not at the $date
    * @access private
    * @param $date ofDate
	* @return quarter of hour from sunday 00:00
	*/
	function getQuarterFromDate($date)
	{
        $dummyDate      = $date;
        $dummyDate->addSeconds($this->timeOffset*60);
        $dummyTime      = $dummyDate->getDateSpan();
        return $dummyTime->getQuarter();
	}
	
    /**
    * fill isAvail array
    * @access private
    * @param $firstKey first key
    * @param $lastKey last key
    * @param $value value to fill with
    */
    function fillIsAvail($firstKey, $lastKey, $value)
    {
        if ($firstKey > $lastKey) {
            $lastKey += 672; // 672 = 24 hours * 7 days * 4 quarters
        }
        $firstI = ($firstKey > $this->startQuarter) ? $firstKey : $this->startQuarter;
        $lastI  = ($lastKey > $this->endQuarter) ? $this->endQuarter : $lastKey;
        for ($i = $firstI; $i < $lastI; $i++) {
            $this->isAvail[$i] = $value;
        }
    }
	
	/**
	* Say if the instructor is avaible or not at the $date
    * @access public
    * @param $date ofDate
    * @param $time_offset integer
	* @return boolean
	*/
    function isAvailInPeriod()
    {
        return in_array(true, $this->isAvail);
    }

    /**
	* Say if the instructor is avaible or not at the $date
    * @access public
    * @param $date ofDate
	* @return boolean
	*/
    function isAvailable($date)
    {
        $quarter = $this->getQuarterFromDate($date);
        if ($quarter < $this->startQuarter) {
            $quarter += 672; // 672 = 24 hours * 7 days * 4 quarters
        }
        return $this->isAvail[$quarter];
    }
}
?>