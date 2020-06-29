<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * qualification.class.php
 *
 * check if one member is allowed (according to his qualifications) to book an aircraft
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
 * @version    CVS: $Id: qualification.class.php,v 1.4.2.6 2006/08/20 13:37:54 claratte Exp $
 * @link       http://openflyers.org
 * @since      Fri Sep 24 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

/*
* qualifChecker class
*
*/

class qualifChecker
{
    // private variables
    var $db;            // database object
    var $tz;            // timezone object
    var $frenchDisplay; // boolean for date display type

   /**
     * Constructor
     *
     * Creates a new qualifChecker
     *
     * @access public
     * @param $database database object
     * @param $tz timezone
     * @param $frenchDisplay boolean specifying if how to display date
     * @return null
     */
    function qualifChecker($database,$timezone,$frenchDisplay)
    {
        $this->db=$database;
        $this->tz=$timezone;
        $this->frenchDisplay=$frenchDisplay;
    }

   /**
     * Check if $member is allowed to book this $aircraft
     *
     * Check if $member is allowed to book this $aircraft and with this $bookType book
     *
     * @access public
     * @param $member integer num of member in the database
     * @param $aircraft integer num of aircraft in the database
     * @param $booktype integer type of booking (0: alone, 1: instruction, 2: unable the aircraft)
     * @param $date ofDate object (end) date of the book
     * @param &$answer array of strings indicating the reason(s) why boolean=false (or empty array)
     * @return boolean
     */
    function isAllowed($member,$aircraft,$bookType,$date,&$answer)
    {
        global $lang;
        $answer=array();

        // if the aircraft is not booked alone we can use it (TODO : change according to instructors)
        if ($bookType!=0)
        {
            return true;
        }

        // we query all qualifs needed to be allowed to use this aircraft
        $this->db->query('select aircraft_qualif.CHECKNUM, aircraft_qualif.QUALIFID, 
                                 qualification.NAME, qualification.TIME_LIMITATION 
                          from aircraft_qualif
                          left join qualification on qualification.ID=aircraft_qualif.QUALIFID
                          where aircraft_qualif.AIRCRAFTNUM=\''.$aircraft.'\' 
                          order by CHECKNUM, QUALIFID');
        if ($this->db->numRows())
        {
            $checkQty=array();
            $checkQualifID=array();
            $checkName=array();
            $checkTL=array();
            $result=$this->db->fetch();
            for ($i=0;$result;$i++)
            {
                $checkLevel=$result->CHECKNUM;
                if (isset($checkQty[$checkLevel]))
                {
                    $checkQty[$checkLevel]=$checkQty[$checkLevel]+1;
                }
                else
                {
                    $checkQty[$checkLevel]=1;
                }
                $checkQualifID[$checkLevel][$checkQty[$checkLevel]]=$result->QUALIFID;
                $checkName[$checkLevel][$checkQty[$checkLevel]]=$result->NAME;
                $checkTL[$checkLevel][$checkQty[$checkLevel]]=$result->TIME_LIMITATION;
                $result=$this->db->fetch();
            }
            $maxLevel=$checkLevel;
        }
        else
        {
            return true;
        }
        $this->db->free();

        // we query all qualifs that the member have
        $ownQualifID=array();
        $ownExpireDate=array();
        $this->db->query('select QUALIFID, EXPIREDATE 
                    from member_qualif 
                    where MEMBERNUM=\''.$member.'\'
                    order by QUALIFID');
        $result=$this->db->fetch();
        for ($i=0;$result;$i++)
        {
            $ownQualifID[$i]=$result->QUALIFID;
            if ($result->EXPIREDATE!='0000-00-00')
            {
                $ownExpireDate[$i]=new ofDate($result->EXPIREDATE.'T23:59:59');
            }
            else
            {
                $ownExpireDate[$i]=new ofDate();
            }
            $result=$this->db->fetch();
        }
        $this->db->free();

        $result=true;
        foreach ($checkQty as $level => $maxQty)
        {
            $checkResult[$level] = false;
            $j = 0;
            for ($i=1;$i<=$maxQty;$i++)
            {
                while (($j<sizeof($ownQualifID))and($checkQualifID[$level][$i]>$ownQualifID[$j]))
                {
                    $j++;
                }
                if (($j<sizeof($ownQualifID))
                    and($checkQualifID[$level][$i]==$ownQualifID[$j])
                    and((!$checkTL[$level][$i])or(Date::compare($date,$ownExpireDate[$j])<=0)))
                {
                    $checkResult[$level]=true;
                }
            }
            if (!$checkResult[$level])
            {
                $answerLevel=sizeof($answer);
                $answer[$answerLevel]=$lang['QUALIF_MISSING'].'&nbsp;:&nbsp;';
                for ($i=1;$i<=$checkQty[$level];$i++)
                {
                    if ($i!=1)
                    {
                        $answer[$answerLevel]=$answer[$answerLevel].' '.$lang['QUALIF_OR'].' ';
                    }
                    $answer[$answerLevel]=$answer[$answerLevel].$checkName[$level][$i];
                    if ($checkTL[$level][$i])
                    {
                        $answer[$answerLevel]=$answer[$answerLevel].' '.$lang['QUALIF_NOT_OUT'];
                    }
                }
                $result=false;
            }
        }
        return $result;
    }

   /**
     * Check qualification expiration time
     *
     * Check if $member has qualifications that have expired or that will expire whithin $alertDelay period
     * and is has not selected no more remind flag
     *
     * @access public
     * @param $member integer num of member in the database
     * @param $delay integer number of weeks before expiration time
     * @return string array list of qualifications outdated or near to be
     */
    function nearLTList($member,$delay)
    {
        global $lang;
        $answer=array();

        $today=new ofDate();
        $maxDate=$today;
        $maxDate->addSeconds($delay*604800);    // 604800 = 60*60*24*7

        $this->db->query('select qualification.ID, qualification.NAME, member_qualif.EXPIREDATE 
                    from member_qualif
                    left join qualification on member_qualif.QUALIFID=qualification.ID
                    where member_qualif.MEMBERNUM=\''.$member.'\' and member_qualif.NOALERT=0 
                      and member_qualif.EXPIREDATE<=\''.$maxDate->getDate().'\' 
                      and member_qualif.NOALERT=0 
                      and qualification.TIME_LIMITATION=1
                    order by member_qualif.QUALIFID');
        $result=$this->db->fetch();
        while ($result)
        {
            $date=new ofDate($result->EXPIREDATE);
            if ($date->before($today))
            {
                $answer[$result->ID]=$result->NAME.' '.$lang['QUALIF_HAS_EXPIRED'].' '.$date->displayDate($this->tz,$this->frenchDisplay);
            }
            else
            {
                $answer[$result->ID]=$result->NAME.' '.$lang['QUALIF_WILL_EXPIRE'].' '.$date->displayDate($this->tz,$this->frenchDisplay);
            }
            $result=$this->db->fetch();
        }
        return $answer;
    }
}
?>