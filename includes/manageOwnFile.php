<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageOwnFile.php
 *
 * Manage prefs and personnal datas
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
 * @category   html engine
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: manageOwnFile.php,v 1.11.2.4 2006/06/02 12:38:22 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Feb 19 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// We assume that $userSession and $database are well defined

$userSession->setOldMenus($menu,$sub_menu);

$frenchDisplay=$userSession->isFrenchDateDisplay();
$timezone=$userSession->getTimeZone();
$tzID=$timezone->getID();
$currentLang=$userSession->getLang();


// get all lang file names
$languages=array();
$langDir='./lang/';
if (is_dir($langDir))
{
    if ($langDirPtr = opendir($langDir)) 
    {
        $i=0;
        while (($fileName = readdir($langDirPtr)) !== false)
        {
            if (substr($fileName,strlen($fileName)-4,4)=='.php')
            {
                $languages[$i]=substr($fileName,0,strlen($fileName)-4);
                $i++;
            }
        }
    closedir($langDirPtr);
    }
}


///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
require_once('./includes/header.php');
?>
	</head>
<body>
	<script type="text/javascript" src="javascript/manageOwnFile.js"></script>
<?php
require_once('./includes/menus.php');
?>
<div class="mainRow">
<ul class="shortDesc"><?php
echo('<li>'.$userSession->getClubName().'</li><li>'.$lang['CONNECTED'].'&nbsp;:&nbsp;'.stripslashes($userSession->getFirstName()).' '.stripslashes($userSession->getLastName()).'</li><li>('.stripslashes($userSession->get_profile_name()).')</li>'); ?>
</ul>
</div>
<?php
$javaFunc='';
if (!$userSession->isNothingAllowed())
{
    $javaFunc='onsubmit="return check_mail(this.email.value)"';
}
$requestForm=new requestForm($lang['OWN_FILE_DISPLAY_PREFS'],$javaFunc,'Large');
$requestForm->addHidden('menu',4);
$requestForm->addHidden('sub_menu',10);

// Make format date display option
$formatDateList=array(array(0,$lang['YEAR'].'/'.$lang['MONTH'].'/'.$lang['DAY']),array(1,$lang['DAY'].'/'.$lang['MONTH'].'/'.$lang['YEAR']));
$currentFormatDate=0;
if ($frenchDisplay)
{
    $currentFormatDate=1;
}
$requestForm->addRadioBox($lang['USER_DATE_FORMAT'],'format_date',$formatDateList,$currentFormatDate);

// Make default books display option
$viewCheckBoxList=array();
$viewCheckBoxList[]=array('inst_display','',$userSession->isInstOnOneDay(),$lang['OWN_FILE_INSTRUCTORS']);
$viewCheckBoxList[]=array('aircraft_display','',$userSession->isAircraftOnOneDay(),$lang['OWN_FILE_AIRCRAFTS'],'check_one_day(\'aircraft\')');
$requestForm->addCheckBoxList($lang['OWN_FILE_DEFAULT_BOOKING'],$viewCheckBoxList,'onclick="check_one_day(this.name)"');

// Make legend Popup display option
$requestForm->addCheckBox('','legendPopup','',$userSession->isLegendPopup(),$lang['OWN_FILE_LEGEND_DISPLAY']);

// Make timezone display option
$tzList=array();
while($element=each($_DATE_TIMEZONE_DATA))
{
    $tzList[]=array($element['key'],floor($element['value']['offset']/3600000).' '.$element['key']);
}
$requestForm->addCombo($lang['TIMEZONE'],'timezone',$tzList,$tzID);

// Make langage display option
$languagesList=array();
while($element=each($languages))
{
    $languagesList[]=array($element['value'],$element['value']);
}
$requestForm->addCombo($lang['LANGUAGE'],'language',$languagesList,$currentLang);

// Make books size display option
$requestForm->addInput($lang['WIDTH'].'&nbsp;(&nbsp;'.$lang['OWN_FILE_VALUE_ADVISED'].'&nbsp;:&nbsp;12)','view_width',false,2,$userSession->getViewWidth());
$requestForm->addInput($lang['HEIGHT'].'&nbsp;(&nbsp;'.$lang['OWN_FILE_VALUE_ADVISED'].'&nbsp;:&nbsp;30)','view_height',false,2,$userSession->getViewHeight());

// Make default aircrafts displayed option
$aircraftCheckBoxList=array();
for($i=0;$i<$aircraftsClass->get_complete_size();$i++)
{
    $item=$aircraftsClass->get_complete_value($i);
    $aircraftCheckBoxList[]=array('ac'.$i,'',$aircraftsClass->is_viewed($item->NUM),stripslashes($item->CALLSIGN).'&nbsp;('.stripslashes($item->TYPE).')');
}
$requestForm->addCheckBoxList($lang['OWN_FILE_VISIBLE_AIRCRAFTS'],$aircraftCheckBoxList);

// Make default instructors displayed option
$instCheckBoxList=array();
for($i=0;$i<$instructorsClass->get_complete_size();$i++)
{
	$item=$instructorsClass->get_complete_value($i);
    $instCheckBoxList[]=array('inst'.$i,'',$instructorsClass->is_viewed($item->NUM),stripslashes($item->FIRST_NAME).'&nbsp;'.stripslashes($item->LAST_NAME).'&nbsp;('.stripslashes($item->SIGN).')');
}
$requestForm->addCheckBoxList($lang['OWN_FILE_VISIBLE_INSTRUCTORS'],$instCheckBoxList);

if(!$userSession->isNothingAllowed())
{
    $requestForm->addBreakForm($lang['OWN_FILE_PRIVATE_FILE'],'Large');
    $requestForm->addInput($lang['EMAIL'],'email',false,255,$userSession->getEmail());
    $requestForm->addCheckBox('','emailpublic','',$userSession->isPublicEmail(),$lang['OWN_FILE_VISIBLE_FROM_OTHERS']);
    $requestForm->addCheckBox('','mailack','',isMailNotify($userSession->getNotification()),$lang['OWN_FILE_MAIL_ACK']);
    if($mailing_list=$userSession->getMailingListName())
	{
        $mailButtonList=array();
        $mailButtonList[]=array('8',$lang['OWN_FILE_SUBSCRIBE']);
        $mailButtonList[]=array('9',$lang['OWN_FILE_UNSUBSCRIBE']);
	    $requestForm->addButtonList($lang['MAILING_LIST'].'&nbsp;('.$mailing_list.')',$mailButtonList);
	}

	$requestForm->addInput($lang['HOME_PHONE'],'homephone',false,14,$userSession->getHomePhone());
    $requestForm->addCheckBox('','homephonepublic','',$userSession->isPublicHomePhone(),$lang['OWN_FILE_VISIBLE_FROM_OTHERS']);

    $requestForm->addInput($lang['WORK_PHONE'],'workphone',false,14,$userSession->getWorkPhone());
    $requestForm->addCheckBox('','workphonepublic','',$userSession->isPublicWorkPhone(),$lang['OWN_FILE_VISIBLE_FROM_OTHERS']);
	
    $requestForm->addInput($lang['CELL_PHONE'],'cellphone',false,14,$userSession->getCellPhone());
    $requestForm->addCheckBox('','cellphonepublic','',$userSession->isPublicCellPhone(),$lang['OWN_FILE_VISIBLE_FROM_OTHERS']);

    $requestForm->addTextArea($lang['ADDRESS'],'address',stripslashes($userSession->getAddress()));
    $requestForm->addInput($lang['ZIPCODE'],'zipcode',false,8,$userSession->getZipcode());
    $requestForm->addInput($lang['CITY'],'city',false,35,stripslashes($userSession->getCity()));
    $requestForm->addInput($lang['STATE'],'state',false,35,stripslashes($userSession->getState()));
    $requestForm->addInput($lang['COUNTRY'],'country',false,35,stripslashes($userSession->getCountry()));
}

$requestForm->addBreakForm($lang['OWN_FILE_CHANGE_PWD']);
$requestForm->addInput($lang['OWN_FILE_OLD_PWD'],'old_password',true,10);
$requestForm->addInput($lang['OWN_FILE_NEW_FIRST_PWD'],'new1_password',true,10);
$requestForm->addInput($lang['OWN_FILE_NEW_SECOND_PWD'],'new2_password',true,10);

if($userSession->isNothingAllowed())
{
    $requestForm->addBreakForm($lang['OWN_FILE_PUBLIC_ACCESS_MOD']);
    $requestForm->addInput($lang['ASK_LOGIN'],'admin_login',false,10);
    $requestForm->addInput($lang['ASK_PWD'],'admin_password',true,10);
}

$requestForm->addBreakForm();
$requestForm->close($lang['SAVE']);
require_once('./includes/footer.php');
?>