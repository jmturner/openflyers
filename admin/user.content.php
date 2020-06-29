<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * user.content.php
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
 * @version    CVS: $Id: user.content.php,v 1.7.2.2 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// get current date in order to warn user if fee expiration date is close or not (if set to on)
$today = date('Ymd');
$myTemplate->assign('LAST_NAME', stripslashes($currentUser->lastName['init']));
$myTemplate->assign('FIRST_NAME', stripslashes($currentUser->firstName['init']));
$myTemplate->assign('LOGIN', $currentUser->name['init']);
$organisation_status ='';
// status displayed (member and/or instructor)
if ($currentUser->isInstructor()) {
	$organisation_status .= $lang['USER_TYPE_INSTRUCTOR'].'<br />';
}
if ($currentUser->isMember()) {
	$organisation_status .= $lang['USER_TYPE_MEMBER'];
}
$myTemplate->assign('STATUS', $organisation_status);
if (isset($choixProfile)) {
	$myTemplate->assign('PROFILE_SELECT', $choixProfile);
	} else {
	$myTemplate->assign('PROFILE_SELECT', '');
	}
$myTemplate->assign('EMAIL', $currentUser->email['init']);
if ($currentConfig->subscription_enabled) {
	// enhanced html to show cell if annual fee is enabled
	$myTemplate->assign('CLASS_SPECIAL', '');
	if ($currentUser->isMember()) 	{
		$userexpiry = $currentUser->getOutOfDate();
		if ($today > intval($userexpiry[0].$userexpiry[1].$userexpiry[2].$userexpiry[3].$userexpiry[5].$userexpiry[6].$userexpiry[8].$userexpiry[9])) {	
			$myTemplate->assign('CLASS_SPECIAL', 'style="background: red;"');
		} else {
			$myTemplate->assign('CLASS_SPECIAL', 'class="fee_expected"');
		}
		$myTemplate->assign('COTISE_OR_NOT', showDateTxt($userexpiry, $userSession->isFrenchDateDisplay()).'<input type="checkbox" name="list_of_update[]" value="'.$currentUser->reference.'" />');
	} else {
		$myTemplate->assign('COTISE_OR_NOT', 'n/a');
	}
	//*******************************************
}
if ($show_password) { 	
	$myTemplate->assign('MODIFY', $lang['MODIFY']);
	$myTemplate->assign('DELETE', $lang['DELETE']);
	$myTemplate->assign('LICENSE_MOD', $lang['LICENSE_MOD']);
	$myTemplate->assign('REFERENCE', $currentUser->reference);
	if ($currentConfig->subscription_enabled) {
		$myTemplate->display('./template/userTableFeeMod.tpl');
	} else {
		$myTemplate->display('./template/userTableNoFeeMod.tpl');
	}
} else { 	
	$myTemplate->assign('CANNOT_MODIFY', $lang['CANNOT_BE_MODIFIED']);
	if ($currentConfig->subscription_enabled)
	{
		$myTemplate->display('./template/userTableFeeNoMod.tpl');
	} else {
		$myTemplate->display('./template/userTableNoFeeNoMod.tpl');
	}
}
?>