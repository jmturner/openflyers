<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * parameter.class.php
 *
 * get optional modules informations
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
 * @category   right management
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: parameter.class.php,v 1.7.2.6 2007/03/19 16:35:41 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 13 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

/*
* parameter class
*
* Manage parameter table and options described in the Database_comments.html file
*/
class parameter
{
    // private variables
    var $db;            // DBAccessor object
    var $enabled;       // array of boolean (parameter.ENABLED)
    var $intValue;     // array of integer (parameter.INT_VALUE)
    var $charValue;    // array of string (parameter.CHAR_VALUE)

    /**
     * get parameters from database $db
     *
     * @access public
     * @param DBAccessor object $db
     * @return null
     */
    function getFromDatabase($db)
    {
        $this->db=$db;
        $db->query('select * from parameter');
        $row=$db->fetch();
        while ($row)
        {
            $code=$row->CODE;
            $this->enabled[$code]=$row->ENABLED;
            $this->intValue[$code]=$row->INT_VALUE;
            $this->charValue[$code]=$row->CHAR_VALUE;
            $row=$db->fetch();
        }
        $db->free();
    }


    /**
     * Say if we have not to check open time club hours for booking
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isNoOpenTimeLimitation()
    {
        if (isset($this->enabled['NO_OPENTIME_LIMIT']))
        {
            return $this->enabled['NO_OPENTIME_LIMIT'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Say if we have to refresh visitor acces
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isNoVisitorRefresh()
    {
        if (isset($this->enabled['NO_VISIT_REFRESH']))
        {
            return $this->enabled['NO_VISIT_REFRESH'];
        }
        else
        {
            return false;
        }
    }

    function setNoVisitorRefresh($flag) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('NO_VISIT_REFRESH', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1' WHERE code='NO_VISIT_REFRESH'";
            $this->enabled['NO_VISIT_REFRESH'] = 1;
        } else {
            $query = "UPDATE parameter SET enabled='0' WHERE code='NO_VISIT_REFRESH'";
            $this->enabled['NO_VISIT_REFRESH'] = 0;
        }
        $res = $this->db->query($query);
    }

    /**
     * Say if we have not to display callsign in the aircraft line
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isNoCallsignDisplay()
    {
        if (isset($this->enabled['NO_CALLSIGN_DISPLAY']))
        {
            return $this->enabled['NO_CALLSIGN_DISPLAY'];
        }
        else
        {
            return false;
        }
    }

    function setNoCallsignDisplay($flag) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('NO_CALLSIGN_DISPLAY', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1' WHERE code='NO_CALLSIGN_DISPLAY'";
            $this->enabled['NO_CALLSIGN_DISPLAY'] = 1;
        } else {
            $query = "UPDATE parameter SET enabled='0' WHERE code='NO_CALLSIGN_DISPLAY'";
            $this->enabled['NO_CALLSIGN_DISPLAY'] = 0;
        }
        $res = $this->db->query($query);
    }

    /**
     * Say if we have to take care about book date limitation
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isBookDateLimitation()
    {
        if (isset($this->enabled['BOOK_DATE_LIMITATION']))
        {
            return $this->enabled['BOOK_DATE_LIMITATION'];
        }
        else
        {
            return false;
        }
    }

    /**
     * get number of weeks while booking is allowed (or return false)
     *
     * @access public
     * @param null
     * @return false or integer number of weeks while booking is allowed
     */
    function getBookDateLimitation()
    {
        if (isset($this->intValue['BOOK_DATE_LIMITATION']))
        {
            return $this->intValue['BOOK_DATE_LIMITATION'];
        }
        else
        {
            return false;
        }
    }

    function setBookDateLimitation($flag, $int) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('BOOK_DATE_LIMITATION', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1', int_value='$int' WHERE code='BOOK_DATE_LIMITATION'";
            $this->enabled['BOOK_DATE_LIMITATION'] = 1;
            $this->intValue['BOOK_DATE_LIMITATION'] = $int;
        } else {
            $query = "UPDATE parameter SET enabled='0', int_value='$int' WHERE code='BOOK_DATE_LIMITATION'";
            $this->enabled['BOOK_DATE_LIMITATION'] = 0;
            $this->intValue['BOOK_DATE_LIMITATION'] = $int;
        }
        $res = $this->db->query($query);
    }

    /**
     * Say if we have to take care about book date limitation
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isBookInstructionMinTime()
    {
        if (isset($this->enabled['BOOK_INSTRUCTION_MIN_TIME']))
        {
            return $this->enabled['BOOK_INSTRUCTION_MIN_TIME'];
        }
        else
        {
            return false;
        }
    }

    /**
     * get number of weeks while booking is allowed (or return false)
     *
     * @access public
     * @param null
     * @return false or integer number of weeks while booking is allowed
     */
    function getBookInstructionMinTime()
    {
        if (isset($this->intValue['BOOK_INSTRUCTION_MIN_TIME']))
        {
            return $this->intValue['BOOK_INSTRUCTION_MIN_TIME'];
        }
        else
        {
            return false;
        }
    }

    function setBookInstructionMinTime($flag, $int) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('BOOK_INSTRUCTION_MIN_TIME', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1', int_value='$int' WHERE code='BOOK_INSTRUCTION_MIN_TIME'";
            $this->enabled['BOOK_INSTRUCTION_MIN_TIME'] = 1;
            $this->intValue['BOOK_INSTRUCTION_MIN_TIME'] = $int;
        } else {
            $query = "UPDATE parameter SET enabled='0', int_value='$int' WHERE code='BOOK_INSTRUCTION_MIN_TIME'";
            $this->enabled['BOOK_INSTRUCTION_MIN_TIME'] = 0;
            $this->intValue['BOOK_INSTRUCTION_MIN_TIME'] = $int;
        }
        $res = $this->db->query($query);
    }

    /**
     * Say if we have to take care about book duration limitation
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isBookDurationLimitation()
    {
        if (isset($this->enabled['BOOK_DURATION_LIMITATION']))
        {
            return $this->enabled['BOOK_DURATION_LIMITATION'];
        }
        else
        {
            return false;
        }
    }

    /**
     * get number of max hours booking duration is allowed (or return false)
     *
     * @access public
     * @param null
     * @return false or integer number of max hours booking duration is allowed
     */
    function getBookDurationLimitation()
    {
        if (isset($this->intValue['BOOK_DURATION_LIMITATION']))
        {
            return $this->intValue['BOOK_DURATION_LIMITATION'];
        }
        else
        {
            return false;
        }
    }

    function setBookDurationLimitation($flag, $int) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('BOOK_DURATION_LIMITATION', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1', int_value='$int' WHERE code='BOOK_DURATION_LIMITATION'";
            $this->enabled['BOOK_DURATION_LIMITATION'] = 1;
            $this->intValue['BOOK_DURATION_LIMITATION'] = $int;
        } else {
            $query = "UPDATE parameter SET enabled='0', int_value='$int' WHERE code='BOOK_DURATION_LIMITATION'";
            $this->enabled['BOOK_DURATION_LIMITATION'] = 0;
            $this->intValue['BOOK_DURATION_LIMITATION'] = $int;
        }
        $res = $this->db->query($query);
    }

    /**
     * Say if we use qualification
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isUseQualif()
    {
        if (isset($this->enabled['QUALIF']))
        {
            return $this->enabled['QUALIF'];
        }
        else
        {
            return false;
        }
    }

    /**
     * Say if we use restriction in cas of outdate or no qualif
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isRestrictiveQualif()
    {
        if (isset($this->intValue['QUALIF'])and($this->isUseQualif()))
        {
            return $this->intValue['QUALIF'];
        }
        else
        {
            return false;
        }
    }

    /**
     * return book allocator rule
     *
     * @access public
     * @param null
     * @return integer
     */
    function getBookAllocatorRule()
    {
        $result=0;
        if (isset($this->enabled['BOOK_ALLOCATING_RULE']))
        {
            if (($this->enabled['BOOK_ALLOCATING_RULE']==1)and(isset($this->intValue['BOOK_ALLOCATING_RULE'])))
            {
                $result=$this->intValue['BOOK_ALLOCATING_RULE'];
            }
        }
        return $result;
    }

    function setBookAllocatorRule($flag, $int) {
    	$query = "INSERT IGNORE INTO parameter(code, enabled, int_value, char_value) values('BOOK_ALLOCATING_RULE', '0', '', '')";
    	$res = $this->db->query($query);
        if ($flag) {
            $query = "UPDATE parameter SET enabled='1', int_value='$int' WHERE code='BOOK_ALLOCATING_RULE'";
            $this->enabled['BOOK_ALLOCATING_RULE'] = 1;
            $this->intValue['BOOK_ALLOCATING_RULE'] = $int;
        } else {
            $query = "UPDATE parameter SET enabled='0', int_value='$int' WHERE code='BOOK_ALLOCATING_RULE'";
            $this->enabled['BOOK_ALLOCATING_RULE'] = 0;
            $this->intValue['BOOK_ALLOCATING_RULE'] = $int;
        }
        $res = $this->db->query($query);
    }
    
    /**
     * Say if we use subscription date and which level
     *
     * @access public
     * @param null
     * @return false/0 or subscription level (1: we use it, but we do not change profile in case of outdate subscription, 2: we use and change profile)
     */
    function isUseSubscription()
    {
        if (isset($this->enabled['SUBSCRIPTION']))
        {
            return $this->enabled['SUBSCRIPTION'];
        }
        else
        {
            return false;
        }
    }

    /**
     * In case of subscription level 2 management, return outdate profile
     *
     * @access public
     * @param null
     * @return integer (profile) or false if we are not subscription level 2 management
     */
    function getOutdateSubscriptionProfile()
    {
        if ((isset($this->enabled['SUBSCRIPTION']))and($this->enabled['SUBSCRIPTION']==2))
        {
            return $this->intValue['SUBSCRIPTION'];
        }
        else
        {
            return false;
        }
    }

    /**
     * In case of subscription level 2 management, return outdate permits
     *
     * @access public
     * @param null
     * @return integer (permits) or false if we are not subscription level 2 management
     */
    function getOutdateSubscriptionPermits()
    {
        $profile = $this->getOutdateSubscriptionProfile();
        if ($profile)
        {
            return $this->db->query_and_fetch_single('select PERMITS from profiles where NUM="'.$profile.'"');
        }
        else
        {
            return false;
        }
    }

    /**
     * In case of subscription level 1 or 2 management, return default subscription date
     *
     * @access public
     * @param null
     * @return ofDate (default subscription date) or false if we are not subscription level 1 or 2 management
     */
    function getDefaultSubscriptionDate()
    {
        if ((isset($this->enabled['SUBSCRIPTION']))and($this->enabled['SUBSCRIPTION']>0))
        {
            return new ofDate($this->charValue['SUBSCRIPTION']);
        }
        else
        {
            return false;
        }
    }

    /**
     * get Flight process type
     *
     * @access public
     * @param null
     * @return integer if flight process activated, return 0 (complete process) or 1 (close flight only) otherwise return -1
     */
    function getFlightProcess()
    {
        $result=-1;
        if (isset($this->enabled['FLIGHT']))
        {
            if (($this->enabled['FLIGHT']==1)and(isset($this->intValue['FLIGHT'])))
            {
                $result=$this->intValue['FLIGHT'];
            }
        }
        return $result;
    }
}
?>