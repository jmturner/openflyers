<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Date.class.php
 *
 * override PEAR Date class and Date_Span class
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
 * @category   date
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: Date.class.php,v 1.21.2.3 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon May 31 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// Pear Date class
require_once('Date.php');

/*
* interval class
*
* record a gap of time : begin, end, amplitude
* used in bookView to test if a time is or not within this interval
*/

class interval
{
    var $begin;     // ofDateSpan begin of the interval
    var $end;       // ofDateSpan end of the interval
    var $amplitude; // ofDateSpan amplitude time between $begin and $end

   /**
     * Constructor
     *
     * Creates a new time interval
     *
     * @access public
     * @param $begin ofDateSpan begin time of the interval
     * @param $end ofDateSpan end time of the interval
     * @return null
     */
    function interval($begin,$end)
    {
        $this->begin=$begin;
        $this->end=$end;
        if($end->lowerEqual($begin))
        {
            $this->end->day=1;
        }

        $this->amplitude=$this->end;
        $this->amplitude->subtract($this->begin);
        if($this->amplitude->toSeconds()==0)
        {
            $this->amplitude->setFromHours(24);
        }
    }

   /**
     * Test if the $current time is in the interval defined by this object
     *
     * @access public
     * @param $current ofDateSpan time to check
     * @return boolean true = is in the interval
     */
    function isIn($current)
    {
        $current->day=0;
        if (($current->lowerEqual($this->end))and($current->greaterEqual($this->begin)))
        {
            return true;
        }
        else
        {
            $current->day=1;
            if (($current->lowerEqual($this->end))and($current->greaterEqual($this->begin)))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }
}

/*
* ofDate class
*
* extends the PEAR::Date class
* add lots of functions
* and correct initialization bug in the Date class
*/

class ofDate extends Date 
{
    /**
     * Constructor
     *
     * Creates a new Date Object initialized to the current date/time in the
     * system-default timezone by default.  A date optionally
     * passed in may be in the ISO 8601, TIMESTAMP or UNIXTIME format,
     * or another Date object.  If no date is passed, the current date/time
     * is used.
     *
     * @access public
     * @see setDate()
     * @param mixed $date optional - date/time to initialize
     * @return object Date the new Date object
     */
    function ofDate($date=null)
    {
        if(($date=='')or($date=='00'))
        {
            $this->Date(date("Y-m-d H:i:sO"));  // we add timezone offset, so PEAR date class set UTC datetime
        }
        else
        {
            $this->Date($date);
        }
    }
    
	/**
	* Get Date in the timestamp format (ie: YYYYMMDDHHMMSS)
    * @access public
    * @param null
	* @return string
	*/
	function getTS()
	{
		return($this->getDate(DATE_FORMAT_TIMESTAMP));
	}

	/**
	* Get only the Date in the ISO format (ie: YYYY-MM-DD)
    * @access public
    * @param null
	* @return string
	*/
	function getOnlyDate()
	{
		return(substr($this->getDate(DATE_FORMAT_TIMESTAMP),0,8));
	}

	/**
	* Get clock time in minutes (ie: hour*60+minute)
    * @access public
    * @param null
	* @return int
	*/
	function getClock()
	{
		return($this->getHour()*60+$this->getMinute());
	}

	/**
	* Get clock time in quarter of hours
    * @access public
    * @param null
	* @return int
	*/
	function getClockQuarter()
	{
		return(floor(($this->getHour()*60+$this->getMinute())/15));
	}

	/**
	* Set clock time (hour, minute, second) at 00:00:00
    * @access public
    * @param null
	* @return null
	*/
    function clearClock()
    {
        $this->setSecond(0);
        $this->setMinute(0);
        $this->setHour(0);
    }

	/**
	* Set clock time (hour, minute, second)
    * @access public
    * @param ofDateSpan
	* @return null
	*/
	function setClock($time)
	{
        $this->setSecond($time->second);
        $this->setMinute($time->minute);
        $this->setHour($time->hour);
	}

	/**
	* Extract clock time and week day from the date (ie : 2 15:36:42)
	* (Sunday=0)
    * @access public
    * @param null
	* @return ofDateSpan
	*/
	function getDateSpan()
	{
		$time = new ofDateSpan($this->getDayOfWeek().' '.$this->getHour().':'.$this->getMinute().':'.$this->getSecond());
		return($time);
	}

	/**
	* Extract clock time from the date (ie : 15:36:42)
    * @access public
    * @param null
	* @return ofDateSpan
	*/
	function getClockSpan()
	{
		$time = new ofDateSpan($this->getHour().':'.$this->getMinute().':'.$this->getSecond());
		return($time);
	}

    /**
     * Get a ofDate object for the day after this one
     *
     * Get a ofDate object for the day after this one.
     * The time of the returned ofDate object is the same as this time.
     *
     * @access public
     * @return object ofDate Date representing the next day
     */
    function getNextDay()
    {
        $day = Date_Calc::nextDay($this->day, $this->month, $this->year, "%Y-%m-%d");
        $date = sprintf("%s %02d:%02d:%02d", $day, $this->hour, $this->minute, $this->second);
        $newDate = new ofDate();
        $newDate->setDate($date);
        return $newDate;
    }

    /**
     * Get a ofDate object for the day before this one
     *
     * Get a ofDate object for the day before this one.
     * The time of the returned ofDate object is the same as this time.
     *
     * @access public
     * @return object ofDate Date representing the previous day
     */
    function getPrevDay()
    {
        $day = Date_Calc::prevDay($this->day, $this->month, $this->year, "%Y-%m-%d");
        $date = sprintf("%s %02d:%02d:%02d", $day, $this->hour, $this->minute, $this->second);
        $newDate = new ofDate();
        $newDate->setDate($date);
        return $newDate;
    }
	
	/**
	* should be delete
	* Extract weekday in literal (ie : dimanche)
    * @access public
    * @param null
	* @return string
	*/
    function getWeekDay()
    {
        global $lang;
        $weekDays=array($lang['SUNDAY'],$lang['MONDAY'],$lang['TUESDAY'],$lang['WEDNESDAY'],$lang['THURSDAY'],$lang['FRIDAY'],$lang['SATURDAY']);
        return $weekDays[$this->getDayOfWeek()];
    }

	/**
	* should be delete
	* Extract day and month from the date (ie : 21/12)
    * @access public
    * @param null
	* @return string
	*/
    function getDayMonth()
    {
	   return $this->format('%d/%m');
    }

	/**
	* return hour for the $TZ timezone 
    * @access public
    * @param $tz Date_TimeZone
	* @return integer hour
	*/
    function getTZHour($tz)
    {
        $this->convertTZ($tz);
        $hour=$this->getHour();
        $this->convertTZbyID('UTC');
        return $hour;
    }

	/**
	* Format date to be displayed (ie : lundi 7 juin 2004)
    * @access public
    * @param $tz timezone
	* @return string
	*/
    function displaySentenceDate($tz)
    {
        global $lang;
        $months=array($lang['JANUARY'],$lang['FEBRUARY'],$lang['MARCH'],$lang['APRIL'],$lang['MAY'],$lang['JUN'],$lang['JULY'],$lang['AUGUST'],$lang['SEPTEMBER'],$lang['OCTOBER'],$lang['NOVEMBER'],$lang['DECEMBER']);

        $this->convertTZ($tz);

        $year=$this->getYear();
        $month=$this->getMonth();
        $day=$this->getDay();
        if($day==1)
        {
            $result=$this->getWeekDay().' 1er';
        }
        else 
        {
            $result=$this->getWeekDay().' '.intval($day);
        }
        $result=$result.' '.$months[intval($month)-1];
        $result=$result.' '.$year;
        $this->convertTZbyID('UTC');

        return $result;
    }

	/**
    * Format 2 Dates display as they are the limits of a slot
    * @access public
    * @param $secondDate ofDate object
    * @param $tz timezone object
    * @param $frenchDisplay boolean specifying if how to display date
    * @param $EOLType integer 0(=' ') or 1(='<br />') separating option
	* @return string
	*/
    function displayDateGap($secondDate,$tz,$frenchDisplay=true,$EOLType=0)
    {
        global $lang;
        $EOLarray=array(' ','<br />');

        $this->convertTZ($tz);
        $secondDate->convertTZ($tz);

        if($frenchDisplay)
        {
            $initDate=$this->format('%d/%m/%Y');
            $endDate=$secondDate->format('%d/%m/%Y');
        }
        else
        {
            $initDate=$this->format('%Y/%m/%d');
            $endDate=$secondDate->format('%Y/%m/%d');
        }

        if($initDate==$endDate)
        {
            $result=$lang['DATE_ON'].' '.$initDate.' '.$lang['DATE_FROM'].' '.$this->format('%HH%M').' '.$lang['DATE_TIL_HOUR'].' '.$secondDate->format('%HH%M').$EOLarray[$EOLType];
        }
        else
        {
            $result=$lang['DATE_ON'].' '.$initDate.' '.$lang['DATE_AT_TIME'].' '.$this->format('%HH%M').$EOLarray[$EOLType].$lang['DATE_TIL_DATE'].' '.$endDate.' '.$lang['DATE_AT_TIME'].' '.$secondDate->format('%HH%M');
        }

        $this->convertTZbyID('UTC');
        return $result;
    }

	/**
	* Format date to be displayed in local time (ie : 21/12/2004 or 2004/12/12)
    * @access public
    * @param $tz timezone
    * @param $frenchDisplay boolean specifying if how to display date
	* @return string
	*/
    function displayDate($tz,$frenchDisplay=true)
    {
        $this->convertTZ($tz);
        if($frenchDisplay)
        {
            $result=$this->format('%d/%m/%Y');
        }
        else
        {
            $result=$this->format('%Y/%m/%d');
        }
        $this->convertTZbyID('UTC');
        return $result;
    }

	/**
	* Format date and time to be displayed in local time (ie : 21/12/2004 à 12H25 or 2004/12/12 à 12H25)
    * @access public
    * @param $tz timezone
    * @param $frenchDisplay boolean specifying if how to display date
	* @return string
	*/
    function displayDatetime($tz,$frenchDisplay=true)
    {
        $this->convertTZ($tz);
        if($frenchDisplay)
        {
            $result=$this->format('%d/%m/%Y à %HH%M');
        }
        else
        {
            $result=$this->format('%Y/%m/%d à %HH%M');
        }
        $this->convertTZbyID('UTC');
        return $result;
    }

	/**
	* Format time to be displayed in local time (ie : 12H25)
    * @access public
    * @param $tz timezone
	* @return string
	*/
    function displayTime($tz)
    {
        $this->convertTZ($tz);
        $result=$this->format('%HH%M');
        $this->convertTZbyID('UTC');
        return $result;
    }

	/**
	* Test if the date of the class and the date parameter
	* are the same day
    * @access public
    * @param object Date $date the date to test against
	* @return boolean
	*/
	function isSameDay($date)
	{
		if(($this->getYear()==$date->getYear())and($this->getMonth()==$date->getMonth())and($this->getDay()==$date->getDay()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

class ofDateSpan extends Date_Span
{
	/**
	* Extract HH:MM:SS from the ofDateSpan (ie : 15:36:54)
    * @access public
    * @param null
	* @return string
	*/
    function getClock()
    {
        return $this->format('%T');
    }

	/**
	* get time in quarter of hours
    * @access public
    * @param null
	* @return integer
	*/
    function getQuarter()
    {
        return floor($this->toMinutes()/15);
    }

	/**
	* should be delete
	* Extract weekday in literal (ie : dimanche)
    * @access public
    * @param null
	* @return string
	*/
    function getWeekDay()
    {
        global $lang;
        $weekDays=array($lang['SUNDAY'],$lang['MONDAY'],$lang['TUESDAY'],$lang['WEDNESDAY'],$lang['THURSDAY'],$lang['FRIDAY'],$lang['SATURDAY']);
        return $weekDays[$this->day];
    }

	/**
	* Extract D HH:MM from the ofDateSpan (ie : 2,15:36)
    * @access public
    * @param null
	* @return string
	*/
    function getNNSV()
    {
        return $this->format('%D,%H:%M');
    }
}

?>