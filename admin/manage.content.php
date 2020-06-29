<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manage.content.php
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
 * @version    CVS: $Id: manage.content.php,v 1.13.2.4 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

include('./template/home_admin.php');

// *******************************************
$myTemplate->assign('TITLE_SUBSCRIPTION',$lang['SUBSCRIPTION_TITLE']);
$myTemplate->assign('PROFILES_SUBSCRIPTION', $lang['DEFAULT_PROFILE_IF_SUBSCRIPTION_EXPIRED']);
$myTemplate->assign('EXPIRY_SUBSCRIPTION', $lang['SUBSCRIPTION_EXPIRED_DATE']);
$myTemplate->assign('FEE_OPTION_1', $lang['FEE_OPTION_1']);
$myTemplate->assign('FEE_OPTION_2', $lang['FEE_OPTION_2']);
$myTemplate->assign('FEE_OPTION_3', $lang['FEE_OPTION_3']);
$myTemplate->assign('FEE_OPTION_EXPLANATION', $lang['FEE_OPTION_EXPLANATION']);
$myTemplate->assign('FEE_SPEECH', $lang['FEE_SPEECH']);
if ($currentConfig->subscription_enabled) {
	$myTemplate->assign('CHECKED_3', '');	
	if ($currentConfig->subscription_required) {
		$myTemplate->assign('CHECKED_1', 'checked');
		$myTemplate->assign('CHECKED_2', '');
	} else {
		$myTemplate->assign('CHECKED_1', '');
		$myTemplate->assign('CHECKED_2', 'checked');
	}
} else {
	$myTemplate->assign('CHECKED_1', '');
	$myTemplate->assign('CHECKED_2', '');
	$myTemplate->assign('CHECKED_3', 'checked');
}
$myTemplate->assign('LIST_OF_PROFILES', showProfiles($currentConfig->subscription_default_profile, $database, $userSession, $lang['NO_DEFAULT_PROFILE']));
$myTemplate->assign('VALIDATE', $lang['VALIDATE_SUB_ALL']);
$myTemplate->assign('VALIDATE_SUB_UPDATE', $lang['VALIDATE_SUB_UPDATE']);
$myTemplate->assign('SUBSCRIPTION_DATE', $currentConfig->showSubscriptionDate()); //remplacer avec showdate
$myTemplate->assign('SUBMIT_PROFILES', $lang['SUBMIT_PROFILES']);
$myTemplate->display('./template/subscription.tpl');
?>