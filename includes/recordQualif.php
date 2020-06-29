<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * recordQualif.php
 *
 * save qualif changes
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
 * @category   database management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: recordQualif.php,v 1.9.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Sep 22 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./displayClasses/requestForm.class.php');
// We assume that $userSession and $database are well defined

function displayMessage($title)
{
    global $lang;
	require_once('./includes/header.php');
?></head><body><?php
	$request=new requestForm($title);
	$request->addHidden('menu',4);
	$request->addHidden('sub_menu',1);
	$request->close($lang['BACK_BUTTON']);
	require_once('./includes/footer.php');
}

////////////////////////////// Main code start here /////////////////////////////////
$isSetOwnQualif=$userSession->isSetOwnQualifAllowed();
$isSetOwnLimitations=$userSession->isSetOwnLimitationsAllowed();
$query='';

if((!define_global('action'))OR((!define_global('qualifID'))AND(!define_global('delay'))))
{
    $action='';     // escape value used to have the error message on the next 'if' test below
}

if (($action=='destroy')or($action=='add'))
{
    if ($isSetOwnQualif)
    {
        if ($action=='destroy')
        {
            $query='delete from member_qualif where QUALIFID=\''.$qualifID.'\' and MEMBERNUM=\''.$userSession->getAuthNum().'\'';
        }
        else
        {
            $date=new ofDate();
            $query='insert into member_qualif (MEMBERNUM,QUALIFID,EXPIREDATE,NOALERT) values (\''.$userSession->getAuthNum().'\',\''.$qualifID.'\',\''.$date->getOnlyDate().'\',\'0\')';
        }
    }
    else
    {
	   displayMessage($lang['QUALIF_NO_ALLOWED_MOD']);
    }
}
elseif ($action=='modDelay')
{
    $query='update members set QUALIF_ALERT_DELAY=\''.$delay.'\' where NUM=\''.$userSession->getAuthNum().'\'';
}
elseif ($action=='update')
{
    if (($isSetOwnQualif)or($isSetOwnLimitations))
    {
        if ((define_global('day'))AND(define_global('month'))AND(define_global('year')))
        {
            $date=new ofDate($year.$month.$day.'235959');
            $query='update member_qualif set EXPIREDATE=\''.$date->getOnlyDate().'\', NOALERT=\'0\' where QUALIFID=\''.$qualifID.'\' and MEMBERNUM=\''.$userSession->getAuthNum().'\'';
        }
    }
    else
    {
	   displayMessage($lang['QUALIF_NO_ALLOWED_UPDATE']);
    }
}

if ($query)
{
    $test=$database->query($query);
    if ($test)
    {
        header('Location: index.php?menu=4&sub_menu=1');
    }
    else
    {
        displayMessage($lang['ERROR_TRANSMIT_DATA']);
    }
}
else
{
	displayMessage($lang['ERROR_TRANSMIT_DATA']);
}
?>
