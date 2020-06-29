<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageProfile.php
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
 * @version    CVS: $Id: manageProfile.php,v 1.1.2.3 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

require_once("./classes/profile.class.php");
$myTemplate = new basicTemplate();
$ref = define_variable("ref",-1);
$currentProfile = new profile($database);
include('./admin/headers.content.php');
require_once('./admin/menu.php');
switch ($ope) {
case "manage" :
	include('./admin/profile.manage.content.php');
	$list_of_profile = $currentProfile->getAllProfile();
	for ($i = 0; $i < count($list_of_profile); $i++) {
		$currentProfile->getProfileFromDatabase($list_of_profile[$i]);
		include("./template/profileTableTPL.php");
	}
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "modify" :	
	$title_profile = 'MOD_PROFILE_TITLE';
	$currentProfile->getProfileFromDatabase($ref);
	$myVar = 'init';
	include('./admin/addmod_profile.content.php');
	require_once("./template/profileaddmodTPL.php");
	break;
case "add" :
	$title_profile = 'ADD_PROFILE_TITLE';
	$currentProfile->createBlankProfile();
	$myVar = 'init';
	include('./admin/addmod_profile.content.php');
	require_once("./template/profileaddmodTPL.php");
	break;
case "destroy" :
	$currentProfile->reference = $ref;
	$currentProfile->deleteProfile();	
	$myResultArray = $currentProfile->resultTab;
	//$myResultString = 'Le profil a &eacute;t&eacute; supprim&eacute;.';	
	include('./admin/results.content.php');
	include('./admin/profile.manage.content.php');
	$list_of_profile = $currentProfile->getAllProfile();
	for ($i = 0; $i < count($list_of_profile); $i++) {
		$currentProfile->getProfileFromDatabase($list_of_profile[$i]);
		include("./template/profileTableTPL.php");
	}
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "db" :
	if ($ref < 0) {
		$currentProfile->createBlankProfile();
		$currentProfile->getProfileFromForm();
	} else {
		$currentProfile->getProfileFromDatabase($ref);
		$currentProfile->getProfileFromForm();
	}
	$currentProfile->saveProfile();
	$myResultString = join($currentProfile->resultTab, '<br />');
		include('./admin/results.content.php');
	if ($currentProfile->error) {
		$myVar = 'form';
		include('./admin/headers.content.php');
		require_once('./admin/menu.php');
		$title_profile = 'ADD_PROFILE_TITLE';
		include('./admin/addmod_profile.content.php');
		require_once("./template/profileaddmodTPL.php");
	} else {
		include('./admin/profile.manage.content.php');
		$list_of_profile = $currentProfile->getAllProfile();
		for ($i = 0; $i < count($list_of_profile); $i++) {
			$currentProfile->getProfileFromDatabase($list_of_profile[$i]);
			include("./template/profileTableTPL.php");
		}
		$myTemplate->display('./template/tableEnd.tpl');
	}
	break;
}
$myTemplate->display('./template/footers.tpl');
?>