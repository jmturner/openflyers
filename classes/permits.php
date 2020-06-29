<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * permits.php
 *
 * check the permits according bit position rules
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
 * @version    CVS: $Id: permits.php,v 1.37.2.6 2006/07/01 06:32:59 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 13 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./pool/functions.php');

//**************** functions getting informations according authentication.PROFILE permits ******************//

/**
* Say if there is a permission or not
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isNothingAllowed($permits)
{
	if($permits)
	{
		return(0);
	}
	else
	{
		return(1);
	}
}

/**
* Say if is book alone allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isAnytimeBookAllowed($permits)
{
	return (($permits&1)>>0);
}

/**
* Say if is book alone allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isAnydurationBookAllowed($permits)
{
	return (($permits&8388608)>>23);
}

/**
* Say if is no auto logout
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isNoAutoLogout($permits)
{
	return (($permits&16777216)>>24);
}

/**
* Say if is book alone allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isAloneBookAllowed($permits)
{
	return(($permits&2)>>1);
}

/**
* Say if is book with an instructor allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isInstructorBookAllowed($permits)
{
	return(($permits&4)>>2);
}

/**
* Say if is freezing an aircraft allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isFreezeAircraftAllowed($permits)
{
	return(($permits&8)>>3);
}

/**
* Say if is managing instructors availibilies allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isFreezeInstructorAllowed($permits)
{
	return(($permits&16)>>4);
}

/**
* Say if is allowed to overtake unavailibities of an instructor for a booking
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isBookUnfreeInstAllowed($permits)
{
	return(($permits&32)>>5);
}

/**
* Say if is managing pilot files allowed
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isSetPilotsAllowed($permits)
{
	return(($permits&64)>>6);
}

/**
* Say if is allowed to manage owns qualifs
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isSetOwnQualifAllowed($permits)
{
	return(($permits&128)>>7);
}

/**
* Say if is allowed to manage club parameters
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isSetClubAllowed($permits)
{
	return(($permits&256)>>8);
}

/**
* Say if is allowed to manage aircrafts parameters
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isSetAircraftsAllowed($permits)
{
	return(($permits&512)>>9);
}

/**
* Say if is allowed to manage owns qualifs limitations
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isSetOwnLimitsAllowed($permits)
{
	return(($permits&1024)>>10);
}

/**
* Say if is allowed to book for anyone
* @access public
* @param $permits integer (from authentication.PROFILE->PERMITS)
* @return boolean
*/
function isAnybodyBookAllowed($permits)
{
	return(($permits&2048)>>11);
}

/**
* Take back all permits of all profiles of one person
* @access public
* @param $db DBAccessor object database to access
* @param $login string login
* @param $password string password
* @param &$member integer member.NUM saved there
* @return integer (from authentication.PROFILE->PERMITS addition)
*/
function getAllPermits($db,$login,$password,&$member)
{
	$db->query('select authentication.NUM, profiles.PERMITS from authentication
	left join profiles on (authentication.PROFILE&profiles.NUM)=profiles.NUM
	where authentication.NAME=\''.$login.'\' and authentication.PASSWORD=\''.passwordCrypt($password).'\'');
	$permits=0;
	$member=0;
	while($row=$db->fetch())
	{
		$member=$row->NUM;
		$permits=$permits|$row->PERMITS;
	}
	$db->free();
	return $permits;
}


/**
* Say if he's allowed to do what he wants to do
* @access public
* @param $userSession userSession object
* @param &$permits integer permits saved there
* @param $login string login used in case of nothing allowed profile
* @param $password string password used in case of nothing allowed profile
* @return integer
* values :
* 0 : OK
* 5 : bad Ident, unauthorized
*/
function getPermits($userSession,&$memberNum,&$subscription,&$permits,$login,$password)
{
    // $member = person for whom is the book (0 if mecanic)
    // $memberNum = person connected
    $memberNum=$userSession->getAuthNum();
    $permits=$userSession->getPermits();
    $subscription=new ofDate('9999-12-31T23:59:59');
    $parameter=$userSession->parameter;
    $db=$userSession->db;
    // we check if we have a visitor or not
    if($userSession->isNothingAllowed())
    {
        // if visitor connexion we have to take back permits according the login and password
        $permits=getAllPermits($db,$login,$password,$memberNum);
    }

    if ($memberNum==0)
    {
        return 5;
    }

    // Now we check the subscription validity of the connected guy
    $value=$db->query_and_fetch_single('select members.SUBSCRIPTION from members where members.NUM=\''.$memberNum.'\'');
    if($value)
    {
        $subscription=new ofDate($value);
        $subscription->setHour(23);
        $subscription->setMinute(59);
        $subscription->setSecond(59);
        if (($subscription->isPast())and($parameter->isUseSubscription()==2))
        {
            $permits=$parameter->getOutdateSubscriptionPermits();
        }
    }
    return 0;
}

//**************** functions getting informations according presence or not of an entry in some tables ******************//

/**
* Return flag saying if he is an instructor or not
* @access public
* @param $num integer to test
* @return boolean
*/
function isInstructor($num)
{
	global $database;
	$row=$database->query_and_fetch_single('select count(*) from instructors where INST_NUM=\''.$num.'\'');
	if($row!=0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
* Return flag saying if he is a member or not
* @access public
* @param $num integer to test
* @return boolean
*/
function isMember($num)
{
	global $database;
	$row=$database->query_and_fetch_single('select count(*) from members where NUM=\''.$num.'\'');
	if($row!=0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

//**************** functions getting informations according clubs.FLAGS ******************//

/**
* Return flag saying if we have to display "sameDay" box in the booking form
* @access public
* @param $permits integer to test
* @return boolean
*/
function isSameDayBox($permits)
{
	return(($permits&1)>>0);
}

/**
* Return flag saying if we have to display "comment" box in the booking form
* @access public
* @param $permits integer to test
* @return boolean
*/
function isBookComment($permits)
{
	return(($permits&2)>>1);
}

//**************** functions getting informations according authentication.VIEW_TYPE permits ******************//

/**
* Return flag saying if the bit of $value for public home phone is set
* @access public
* @param $value int to test
* @return boolean
*/
function isPublicHomePhone($value)
{
	return(($value&64)>>6);
}

/**
* Return flag saying if the bit of $value for public work phone is set
* @access public
* @param $value int to test
* @return boolean
*/
function isPublicWorkPhone($value)
{
	return(($value&128)>>7);
}

/**
* Return flag saying if the bit of $value for public cell phone is set
* @access public
* @param $value int to test
* @return boolean
*/
function isPublicCellPhone($value)
{
	return(($value&256)>>8);
}

/**
* Return flag saying if the bit of $value for public email is set
* @access public
* @param $value int to test
* @return boolean
*/
function isPublicEmail($value)
{
	return(($value&512)>>9);
}

//**************** functions getting informations according authentication.NOTIFICATION ******************//

/**
* Return flag saying if the bit of $value for mail notification is set
* @access public
* @param $value int to test (NOTIFICATION field of authentication table)
* @return boolean
*/
function isMailNotify($value)
{
    return ($value&1);
}
?>