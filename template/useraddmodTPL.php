<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * addmod_profile.content.php
 *
 * administration interface
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
 * @category   Admin interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: useraddmodTPL.php,v 1.39.2.4 2006/01/16 17:00:52 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$myTemplate->assign('VALIDATE', $lang['VALIDATE']);
$myTemplate->assign('USER_LOGIN', $lang['USER_LOGIN']);
$myTemplate->assign('USER_LOGIN_VALUE', $currentUser->name[$myVar]);
$myTemplate->assign('USER_LOGIN_EXPLANATION', $lang['USER_LOGIN_EXPLANATION']);
$myTemplate->assign('USER_PASSWORD', $lang['USER_PASSWORD']);
$myTemplate->assign('USER_PASSWORD_EXPLANATION', $lang['USER_PASSWORD_EXPLANATION']);
$myTemplate->assign('USER_MEMBER_NUM', $lang['USER_MEMBER_NUM']);
$myTemplate->assign('USER_MEMBER_NUM_VALUE', $currentUser->memberNum[$myVar]);
$myTemplate->assign('USER_MEMBER_NUM_EXPLANATION', $lang['USER_MEMBER_NUM_EXPLANATION']);
$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
// USER TYPE CONTENT
$myTemplate->assign('USER_TYPE_EXPLANATION', $lang['USER_TYPE_EXPLANATION']);
$myTemplate->assign('USER_TYPE_MEMBER', $lang['USER_TYPE_MEMBER']);
if ($currentUser->isMember()) {
	$myTemplate->assign('MEMBER_CHECKED', 'checked="checked"'); 
} else {
	$myTemplate->assign('MEMBER_CHECKED', '');
}
$myTemplate->assign('USER_TYPE_INSTRUCTOR', $lang['USER_TYPE_INSTRUCTOR']);
if ($currentUser->isInstructor()) {
	$myTemplate->assign('INSTRUCTOR_CHECKED', 'checked="checked"'); 
} else {
	$myTemplate->assign('INSTRUCTOR_CHECKED', ''); 
}
$myTemplate->assign('USER_TRIGRAM', $lang['USER_TRIGRAM']);
if ($currentUser->isInstructor()) {
	$myTemplate->assign('USER_TRIGRAM_VALUE', $currentUser->getInstructor());
	$myTemplate->assign('DISABLED', '');
} else {
$myTemplate->assign('USER_TRIGRAM_VALUE', '');
	$myTemplate->assign('DISABLED', 'disabled');
}
$myTemplate->assign('USER_TRIGRAM_EXPLANATION', $lang['USER_TRIGRAM_EXPLANATION']);
$myTemplate->assign('USER_FIRST_NAME', $lang['USER_FIRST_NAME']);
$myTemplate->assign('USER_FIRST_NAME_VALUE', $currentUser->firstName[$myVar]);
$myTemplate->assign('USER_LAST_NAME', $lang['USER_LAST_NAME']);
$myTemplate->assign('USER_LAST_NAME_VALUE', $currentUser->lastName[$myVar]);
$myTemplate->assign('USER_PROFILE', $lang['USER_PROFILE']);
$myTemplate->assign('USER_PROFILE_CHOICE', $choixProfile); 
$myTemplate->assign('USER_PROFILE_EXPLANATION', $lang['USER_PROFILE_EXPLANATION']);
$myTemplate->assign('USER_LANGUAGE', $lang['USER_LANGUAGE']);
$myTemplate->assign('USER_LANGUAGE_SELECT', $currentUser->listLanguage($myVar));
$myTemplate->assign('USER_LANGUAGE_EXPLANATION', $lang['USER_LANGUAGE_EXPLANATION']);
$myTemplate->assign('USER_TIME_ZONE', $lang['USER_TIMEZONE']);
$myTemplate->assign('USER_TIMEZONE_EXPLANATION', $lang['USER_TIMEZONE_EXPLANATION']);
$myTemplate->assign('TIMEZONE_VALUE', displayTimeZone($currentUser->timezone[$myVar]));
$myTemplate->assign('USER_PERSONAL_DATA', $lang['USER_PERSONAL_DATA']);
$myTemplate->assign('USER_PERSONAL_DATA_EXPLANATION', $lang['USER_PERSONAL_DATA_EXPLANATION']);
$myTemplate->assign('USER_MAIL_DATA', $lang['USER_MAIL_DATA']);
$myTemplate->assign('USER_ADDRESS_EXPLANATION',$lang['USER_ADDRESS_EXPLANATION']);
$myTemplate->assign('USER_ADDRESS_VALUE', $currentUser->address[$myVar]);
$myTemplate->assign('USER_ZIPCODE', $lang['USER_ZIPCODE']);
$myTemplate->assign('USER_ZIPCODE_VALUE', $currentUser->zipcode[$myVar]);
$myTemplate->assign('USER_CITY', $lang['USER_CITY']);
$myTemplate->assign('USER_CITY_VALUE', $currentUser->city[$myVar]);
$myTemplate->assign('USER_STATE', $lang['USER_STATE']);
$myTemplate->assign('USER_STATE_VALUE', $currentUser->state[$myVar]);
$myTemplate->assign('USER_COUNTRY', $lang['USER_COUNTRY']);
$myTemplate->assign('USER_COUNTRY_VALUE', $currentUser->country[$myVar]);
$myTemplate->assign('USER_HOME_PHONE', $lang['USER_HOME_PHONE']);
$myTemplate->assign('USER_HOME_PHONE_VALUE', $currentUser->homephone[$myVar]);
if (isPublicHomePhone($currentUser->viewType[$myVar])) { 
	$myTemplate->assign('USER_CHECKED_1', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_1', ''); 
}
$myTemplate->assign('USER_HOME_PHONE_EXPLANATION', $lang['USER_HOME_PHONE_EXPLANATION']);
$myTemplate->assign('USER_WORK_PHONE', $lang['USER_WORK_PHONE']);
$myTemplate->assign('USER_WORK_PHONE_VALUE', $currentUser->workphone[$myVar]);
if (isPublicWorkPhone($currentUser->viewType[$myVar])) { 
	$myTemplate->assign('USER_CHECKED_2', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_2', ''); 
}
$myTemplate->assign('USER_WORK_PHONE_EXPLANATION', $lang['USER_WORK_PHONE_EXPLANATION']);
$myTemplate->assign('USER_CELL_PHONE', $lang['USER_CELL_PHONE']);
$myTemplate->assign('USER_CELL_PHONE_VALUE', $currentUser->cellphone[$myVar]);
if (isPublicCellPhone($currentUser->viewType[$myVar])) { 
	$myTemplate->assign('USER_CHECKED_3', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_3', ''); 
}
$myTemplate->assign('USER_CELL_PHONE_EXPLANATION', $lang['USER_CELL_PHONE_EXPLANATION']);
$myTemplate->assign('USER_NOTIFY_MAIL', $lang['USER_NOTIFY_MAIL']);
if ($currentUser->notifyMail($myVar)) { 
	$myTemplate->assign('USER_NOTIFY_1', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_NOTIFY_1', ''); 
}
$myTemplate->assign('USER_NOTIFY_MAIL_EXPLANATION', $lang['USER_NOTIFY_MAIL_EXPLANATION']);
$myTemplate->assign('USER_NOTIFY_SMS', $lang['USER_NOTIFY_SMS']);
if ($currentUser->notifySMS($myVar)) { 
	$myTemplate->assign('USER_NOTIFY_2', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_NOTIFY_2', ''); 
}
$myTemplate->assign('USER_NOTIFY_SMS_EXPLANATION', $lang['USER_NOTIFY_SMS_EXPLANATION']);
$myTemplate->assign('USER_VIEW_MODE', $lang['USER_VIEW_MODE']);
$myTemplate->assign('USER_VIEW_AIRCRAFT', $lang['USER_VIEW_AIRCRAFT']);
$myTemplate->assign('USER_VIEW_AIRCRAFT_EXPLANATION', $lang['USER_VIEW_AIRCRAFT_EXPLANATION']);
if (!$user_show_aircraft) {
	$myTemplate->assign('USER_CHECKED_4', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_4', ''); 
}
$myTemplate->assign('USER_VIEW_INSTRUCTOR', $lang['USER_VIEW_INSTRUCTOR']);
$myTemplate->assign('USER_VIEW_INSTRUCTOR_EXPLANATION', $lang['USER_VIEW_INSTRUCTOR_EXPLANATION']);
if (!$user_show_inst) { 
	$myTemplate->assign('USER_CHECKED_5', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_5', ''); 
}
$myTemplate->assign('USER_POPUP_LEGEND', $lang['USER_POPUP_LEGEND']);
$myTemplate->assign('USER_POPUP_LEGEND_EXPLANATION', $lang['USER_POPUP_LEGEND_EXPLANATION']);
if ($user_color) { 
	$myTemplate->assign('USER_CHECKED_6', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_6', ''); 
}
$myTemplate->assign('USER_DATE_FORMAT', $lang['USER_DATE_FORMAT']);
$myTemplate->assign('USER_DATE_FORMAT_EXPLANATION', $lang['USER_DATE_FORMAT_EXPLANATION']);
if ($user_date) {
	$myTemplate->assign('USER_CHECKED_7', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_7', ''); 
}
$myTemplate->assign('USER_EMAIL', $lang['USER_EMAIL']);
if (isPublicEmail($currentUser->viewType[$myVar])) {
	$myTemplate->assign('USER_CHECKED_8', 'checked="checked" '); 
} else {
	$myTemplate->assign('USER_CHECKED_8', ''); 
}
$myTemplate->assign('USER_EMAIL_VALUE', $currentUser->email[$myVar]);
$myTemplate->assign('USER_EMAIL_EXPLANATION', $lang['USER_EMAIL_EXPLANATION']);
$myTemplate->assign('USER_REFERENCE_NUM', $currentUser->reference);
$myTemplate->assign('BACK', $lang['BACK']);
if(isset($mailing_list) && (strlen($mailing_list) > 0)) {
	$myTemplate->assign('USER_MAILING_LIST', $lang['USER_MAILING_LIST']);
	$myTemplate->assign('USER_MAILING_LIST_SIGN', $lang['USER_MAILING_LIST_SIGN']);
	$myTemplate->assign('USER_MAILING_LIST_SIGNOFF', $lang['USER_MAILING_LIST_SIGNOFF']);
	$myTemplate->assign('USER_MAILING_LIST_EXPLANATION', $lang['USER_MAILING_LIST_EXPLANATION']);
	$myTemplate->assign('MAILING_LIST', $mailing_list);
	$myTemplate->display('./template/userAddModML.tpl');
} else {
	$myTemplate->display('./template/userAddMod.tpl');
}
?>