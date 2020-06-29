<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * srssManager.class.php
 *
 * Get Sunrise & Sunset times for a day
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
 * @category   time computing
 * @author     Patrice Godard <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: srssManager.class.php,v 1.4.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Tue Mar 11 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

/**@#+
 * Include supporting classes
 */
require_once('./classes/coordinates.class.php');
require_once('./classes/Date.class.php');
require_once('./classes/srss/'.SRSS_SOURCE.'.class.php');


/*
* ephemeris object
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*/
class ephemeris
{
    // public variables
    var $sunrise;           // ofDateSpan sunrise
    var $sunset;            // ofDateSpan sunset
    var $aeroDay;           // ofDateSpan aeronautic sunrise
    var $aeroNight;         // ofDateSpan aeronautic sunset

    // private variables
    var $sunriseQuarter;    // integer sunrise in quarter of hours
    var $sunsetQuarter;     // integer sunset in quarter of hours
    var $aeroDayQuarter;    // integer aeronautic sunrise in quarter of hours
    var $aeroNightQuarter;  // integer aeronautic sunset in quarter of hours

    /**
     * Constructor
     *
     * Creates a new ephemeris
     *
     * @access public
     * @param $date ofDate ephemeris are computed for this date
     * @param $icao string icao code ephemeris are computed for this location.
     * @return null
     */
    function ephemeris($date,$icao)
    {
        $srssMgr=new srssManager();
        $srssMgr->getSRSS($date,$icao,$this->sunrise,$this->sunset,$this->aeroDay,$this->aeroNight);
        $this->sunriseQuarter=$this->sunrise->getClockQuarter();
        $this->sunsetQuarter=$this->sunset->getClockQuarter();
        $this->aeroDayQuarter=$this->aeroDay->getClockQuarter();
        $this->aeroNightQuarter=$this->aeroNight->getClockQuarter();
    }

    /**
     * return the part of the day according $current value
     * TODO : change the %96 because it's wrong to consider that tomorrow values are the same as today
     *
     * @access public
     * @param $current integer time in quarter of hours
     * @return char 'd' (day), 'n' (night) or 't' (twilight)
     */
    function whichLight($current)
    {
        $current=$current%96;   // we restrict comparaison to today values (that simpler but wrong)

        if($this->sunsetQuarter>$this->sunriseQuarter)  // we consider 2 situations of sunrise and sunset positions
        {
            if (($current<$this->sunsetQuarter)and($current>$this->sunriseQuarter))
            {
                return 'd';
            }
            elseif (($current<$this->aeroNightQuarter)and($current>$this->aeroDayQuarter))
            {
                return 't';
            }
            else
            {
                return 'n';
            }
        }
        else
        {
            if (($current<$this->aeroDayQuarter)and($current>$this->aeroNightQuarter))
            {
                return 'n';
            }
            elseif (($current<$this->sunriseQuarter)and($current>$this->sunsetQuarter))
            {
                return 't';
            }
            else
            {
                return 'd';
            }
        }
    }
}

class srssManager
{
	/**
	* use data provider to calculate SunRise, SunSet, Dawn & Twilight
	* This method must be overriden in derived classes
    * @access public
    * @param $date ofDate (day)
    * @param $icao string (ICAO airfield label)
    * @param &$sr ofDate SunRise
    * @param &$ss ofDate SunSet
    * @param &$dawn ofDate Dawn
    * @param &$twilight ofDate Twilight
	* @return boolean (compute ok or not)
	*/
	function getSRSS($date,$icao, &$sr, &$ss, &$dawn, &$twilight)
	{
		$srssSource=srssSource_getInstance(SRSS_SOURCE);

		global $database;
			
		//get coords
		$database->query('select LON, LAT from icao where ICAO=\''.$icao.'\'');
		$row=$database->fetch();
		$database->free();
		if($row)
		{
		    $coords=new coordinates($row->LAT,$row->LON);
			$srssSource->getSRSS($date,$coords,$sr,$ss,$dawn,$twilight);
			//get TZ offset from GMT time
//			$offset=date('O',strtotime($date));
//			$offset=new Date_Span('02:00');

//			$sr->add($offset);
//			$ss->add($offset);
//			$dawn->add($offset);
//			$twilight->add($offset);
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>