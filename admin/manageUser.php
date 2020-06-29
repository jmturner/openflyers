<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageUser.php
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
 * @version    CVS: $Id: manageUser.php,v 1.1.2.7 2005/11/30 21:20:59 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

require_once('./classes/mailing_list.class.php');
require_once('./classes/user.class.php');
require_once('./classes/profile.class.php');
require_once('./classes/license.class.php');
// *********** Function ***********
function test_mail_list($email)
{
	global $subscribe;
	global $unsubscribe;

	$mail_class=new mailing_list();

	if(isset($subscribe)) {
		$mail_class->add_email($email);
	}
	elseif(isset($unsubscribe)) {
		$mail_class->remove_email($email);
	}
}
$currentLicense = new license(-1,$database);
// *******************************
$ref = define_variable('ref',-1);
// ****
if (isset($_GET['tri'])) {
	$tri = $_GET['tri'];
} else {
	$userSession->define('tri');
	if ($tri == null) {
		$tri = 'LAST_NAME';
	}
}
$userSession->add('tri');
// ****
$currentProfile = new profile($database);
$currentUser = new user($database);
$currentUser->reference = $ref;
$currentConfig->getSubscriptionConfig();
include('./admin/headers.content.php');
require_once('./admin/menu.php');
if ($currentConfig->subscription_enabled) {
	$validationDate = $currentConfig->subscriptionDate;
	$template_suffix = '';
} else {
	$validationDate = "2037-12-31";
	$template_suffix = 'NoFee';
}
switch ($ope) {
case "rank"	: 	
	$listOfInstructors = $currentUser->getAllInstructors();
	$content_display = '';
	$instructorStyle = '';
	$leftTargetStyle = '';
	$rightTargetStyle = '';
	$fullTargetStyle = '';
	$formContent ='';
	$javascript_DHTML = 'SET_DHTML(CURSOR_MOVE';
	for ($boucle = 0; $boucle < count($listOfInstructors); $boucle++) {
		$currentUser->reference = $listOfInstructors[$boucle];
		$currentUser->getUserFromDatabase('init');
		$yDIV = 150+60*$boucle;
		$content_display .= '<div id="instructor'.$boucle.'">'.$currentUser->lastName['init'].' '.$currentUser->firstName['init'].'</div>'."\n";
		$instructorStyle .= '#instructor'.$boucle.' { position: absolute; cursor: pointer; width: 150px; background: #ffffff; color: #000000; font-size: 1em; top: '.($yDIV+5).'px; z-index: 3; padding: 10px; text-align: center; left: 55px; }'."\n";
		$instructorStyle .= '#instructor'.$boucle.':hover { position: absolute; cursor:pointer; width: 150px; background: #000000; color: #ffffff; font-size: 1em; top: '.($yDIV+5).'px; z-index: 3; padding: 10px; text-align: center; left: 55px; }'."\n";
		$content_display .= '<div id="targetleft'.$boucle.'">'.($boucle+1).'</div>'."\n"; 
		$leftTargetStyle .= '#targetleft'.$boucle.'{ position: absolute; width: 50px; left: 0px; background: #0000cc; font-size: 3em; top: '.$yDIV.'px; height: 50px; z-index: 2; color: #ffffff; text-align: center; vertical-align: middle; font-weight: bolder; }'."\n";
		$content_display .= '<div id="targetright'.$boucle.'"></div>'."\n";
		$rightTargetStyle .= '#targetright'.$boucle.' { position: absolute; width: 620px; left: 50px; background: #0000aa; font-size: 1em; top: '.$yDIV.'px; height: 50px; z-index: 1; color: #ffffff; text-align: right; }'."\n";
		$content_display .= '<div id="fulltarget'.$boucle.'">'.$lang['USER_INSTRUCTOR_ORDER'].'. </div>'."\n";
		$fullTargetStyle .= '#fulltarget'.$boucle.'{ position: absolute; width: 570px; left: 90px; background: #ffff00; font-size: 1em; top: '.($yDIV+5).'px; height: 40px; z-index: 2; color: #000000; text-align: right; }'."\n";
		$formContent .= '<input type="hidden" name="instructor_tab['.$boucle.']" value="'.$currentUser->reference.'" />';
		$formContent .= '<input type="hidden" name="instructor_y_tab['.$boucle.']" value="" />';
		$javascript_DHTML .= ', "instructor'.$boucle.'"';
	}
	$javascript_DHTML .= ');';
	$leftTargetStyle .= $rightTargetStyle;
	$leftTargetStyle .= $fullTargetStyle;
	$leftTargetStyle .= $instructorStyle;
	$myTemplate->assign("FORMCONTENT", $formContent);
	$myTemplate->assign("MAXINST", count($listOfInstructors));
	$myTemplate->assign("STYLE", $leftTargetStyle);
	$myTemplate->assign("JAVASCRIPT", $javascript_DHTML);
	$myTemplate->assign("FILECONTENT", $content_display);
	$myTemplate->assign("VALIDATE", $lang['VALIDATE']);
	$myTemplate->display('template/ordonnerInstructorTPL.html');
	break; 
case "ranking"	: 	
	$instructor_tab = $_POST['instructor_tab']; // get the IDs tab
	$instructor_y_tab = $_POST['instructor_y_tab']; // get the y coordinates (keys are same for IDs)
	for ($boucle_instructor = 0; $boucle_instructor < count($instructor_tab); $boucle_instructor++) {
		$instructor_order[$instructor_tab[$boucle_instructor]] = $instructor_y_tab[$boucle_instructor];
		//echo($instructor_tab[$boucle_instructor].'---'.$instructor_y_tab[$boucle_instructor]."<br />");
	}
	asort($instructor_order, SORT_NUMERIC); // sort instructors ids according to their y - keys, now, can help to determine new order
	$top_rank = $currentUser->getMaxRank();
	$top_rank++;
	if (count($instructor_order) == count(array_unique($instructor_order))) {//check for duplicate (2 instructors in the same box)
		$boucle_final = 0 + $top_rank;
		foreach ($instructor_order as $instructor_key => $instructor_y) {
			// lors de l'injection en base de données, le changement d'ordre provoque une collision (duplicate) due à un doublon ORDER_NUM
			$database->query("UPDATE instructors SET ORDER_NUM='".$boucle_final."' WHERE INST_NUM='".$instructor_key."'");
			$boucle_final++;
		}
	}
	// seconde injection pour réinitialisation
	if (count($instructor_order) == count(array_unique($instructor_order))) {//check for duplicate (2 instructors in the same box)
		$boucle_final = 0;
		foreach ($instructor_order as $instructor_key => $instructor_y) {
			// lors de l'injection en base de données, le changement d'ordre provoque une collision (duplicate) due à un doublon ORDER_NUM
			$database->query("UPDATE instructors SET ORDER_NUM='".$boucle_final."' WHERE INST_NUM='".$instructor_key."'");
			$boucle_final++;
		}
	}
	// fin de la remise en ordre
	$myResultArray[] ='PROCESSED';
	include('./admin/results.content.php');
	$tampon_date = $validationDate[8].$validationDate[9].$validationDate[7].$validationDate[5].$validationDate[6].$validationDate[4].$validationDate[0].$validationDate[1].$validationDate[2].$validationDate[3];
	$validationDate = $tampon_date;
	$club_profiles_listing = array();
	$club_profiles_listing = $currentProfile->getAllProfileName();
	unset($currentProfile);
	$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']);
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('LOGIN', $lang['LOGIN']);
	$myTemplate->assign('EMAIL', $lang['EMAIL']);
	$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
	$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
	$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
	$myTemplate->assign('PROFILE', $lang['PROFILE']);
	$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
	$myTemplate->assign('LICENSE', $lang['LICENSE']);
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
	$listOfUsersID = $currentUser->getAllUser($tri);
	for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
		$choixProfile="";
		$currentUser->reference = $listOfUsersID[$boucle];
		$currentUser->getUserFromDatabase('init');
		$listUserProfiles = $currentUser->getUserProfiles();
		$initProfile = true;
		for ($loop = 0; $loop < count($listUserProfiles); $loop ++) {
			if ($initProfile) {
				$choixProfile .= $club_profiles_listing[$listUserProfiles[$loop]];
				$initProfile = false;
			} else {
				$choixProfile .= "<br />".$club_profiles_listing[$listUserProfiles[$loop]];
			}
		}
		$show_password = $currentUser->isHigherRank($userSession->get_rank());
		$show_password = !$show_password;
		include("./admin/user.content.php");
	}
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableFooter'.$template_suffix.'.tpl');
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "manage" :	
	if ($userSession->isFrenchDateDisplay()) {
		$tampon_date = $validationDate[8].$validationDate[9].$validationDate[7].$validationDate[5].$validationDate[6].$validationDate[4].$validationDate[0].$validationDate[1].$validationDate[2].$validationDate[3];
		$validationDate = $tampon_date;
	}
	$club_profiles_listing = array();
	$club_profiles_listing = $currentProfile->getAllProfileName();
	unset($currentProfile);
	$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']);
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('LOGIN', $lang['LOGIN']);
	$myTemplate->assign('EMAIL', $lang['EMAIL']);
	$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
	$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
	$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
	$myTemplate->assign('PROFILE', $lang['PROFILE']);
	$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
	$myTemplate->assign('LICENSE', $lang['LICENSE']);
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
	$listOfUsersID = $currentUser->getAllUser($tri);
	for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
		$choixProfile="";
		$currentUser->reference = $listOfUsersID[$boucle];
		$currentUser->getUserFromDatabase('init');
		$listUserProfiles = $currentUser->getUserProfiles();
		$initProfile = true;
		for ($loop = 0; $loop < count($listUserProfiles); $loop ++) {
			if ($initProfile) {
				$choixProfile .= $club_profiles_listing[$listUserProfiles[$loop]];
				$initProfile = false;
			} else {
				$choixProfile .= "<br />".$club_profiles_listing[$listUserProfiles[$loop]];
			}
		}
		$show_password = $currentUser->isHigherRank($userSession->get_rank());
		$show_password = !$show_password;
		include("./admin/user.content.php");
	}
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableFooter'.$template_suffix.'.tpl');
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "admin":	
	$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']); // display all OF admin and Club admins.
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('LOGIN', $lang['LOGIN']);
	$myTemplate->assign('EMAIL', $lang['EMAIL']);
	$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
	$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
	$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
	$myTemplate->assign('PROFILE', $lang['PROFILE']);
	$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
	$myTemplate->assign('LICENSE', $lang['LICENSE']);
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
	$listOfUsersID = $currentUser->getAllUser('LAST_NAME');
	for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
		$choixProfile="";
		$currentUser->reference = $listOfUsersID[$boucle];
		$currentUser->getUserFromDatabase('init');
		if (isSetClubAllowed($currentUser->profiles)) {
			include("./admin/user.content.php");
		}
	}
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableFooter'.$template_suffix.'.tpl');
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "modify" :	
	$myVar = 'init';
	$title_user = 'MOD_USER_TITLE';
	$mailing_list=$database->query_and_fetch_single('SELECT MAILING_LIST_NAME FROM clubs WHERE NUM=\'1\'');
	$currentUser->getUserFromDatabase('init');
	include("./admin/profiling.php");
	$user_color = ($currentUser->viewType[$myVar]&4)>>2;
	$user_date = ($currentUser->viewType[$myVar]&8)>>3;
	$user_show_inst = ($currentUser->viewType[$myVar]&16)>>4;
	$user_show_aircraft = ($currentUser->viewType[$myVar]&32)>>5;
	include('./admin/user.addmod.content.php');
	include("./template/useraddmodTPL.php");
	break;
case "update" :	
	$currentUser->updateMembersSubscriptionDate($_POST['list_of_update']);
	$club_profiles_listing = array();
	$club_profiles_listing = $currentProfile->getAllProfileName();
	unset($currentProfile);
	include('./admin/results.content.php');
	$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']);
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('LOGIN', $lang['LOGIN']);
	$myTemplate->assign('EMAIL', $lang['EMAIL']);
	$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
	$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
	$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
	$myTemplate->assign('PROFILE', $lang['PROFILE']);
	$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
	$myTemplate->assign('LICENSE', $lang['LICENSE']);
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
	$listOfUsersID = $currentUser->getAllUser($tri);
	for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
		$choixProfile="";
		$currentUser->reference = $listOfUsersID[$boucle];
		$currentUser->getUserFromDatabase('init');
		$listUserProfiles = $currentUser->getUserProfiles();
		$initProfile = true;
		for ($loop = 0; $loop < count($listUserProfiles); $loop ++) {
			if ($initProfile) {
				$choixProfile .= $club_profiles_listing[$listUserProfiles[$loop]];
				$initProfile = false;
			} else {
				$choixProfile .= "<br />".$club_profiles_listing[$listUserProfiles[$loop]];
			}
		}
		$show_password = $currentUser->isHigherRank($userSession->get_rank());
		$show_password = !$show_password;
		include("./admin/user.content.php");
	}
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableFooter'.$template_suffix.'.tpl');
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "add" :
	$myVar = 'init';
	$title_user = 'ADD_USER_TITLE';
	$mailing_list = $database->query_and_fetch_single('SELECT MAILING_LIST_NAME FROM clubs WHERE NUM=\'1\'');
	$currentUser->createBlankUser($myVar);
	include("./admin/profiling.php");
	$user_color = ($currentUser->viewType[$myVar]&4)>>2;
	$user_date = ($currentUser->viewType[$myVar]&8)>>3;
	$user_show_inst = ($currentUser->viewType[$myVar]&16)>>4;
	$user_show_aircraft = ($currentUser->viewType[$myVar]&32)>>5;
	include('./admin/user.addmod.content.php');
	include("./template/useraddmodTPL.php");
	break;
case "db" :	
	$currentUser->initUser();
	$currentUser->saveUser();
	$myResultArray = $currentUser->resultTab;
	$myVar = 'form';
	include('./admin/results.content.php');
	if ($currentUser->error) {
		include("./admin/profiling.php");
		$user_color = ($currentUser->viewType[$myVar]&4)>>2;
		$user_date = ($currentUser->viewType[$myVar]&8)>>3;
		$user_show_inst = ($currentUser->viewType[$myVar]&16)>>4;
		$user_show_aircraft = ($currentUser->viewType[$myVar]&32)>>5;
		$title_user = 'ADD_USER_TITLE';
		include('./admin/user.addmod.content.php');
		include("./template/useraddmodTPL.php");
	} else {
		$club_profiles_listing = array();
		$club_profiles_listing = $currentProfile->getAllProfileName();
		unset($currentProfile);
		$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']);
		$myTemplate->assign('MODIFY',$lang['MODIFY']);
		$myTemplate->assign('DELETE',$lang['DELETE']);
		$myTemplate->assign('LOGIN', $lang['LOGIN']);
		$myTemplate->assign('EMAIL', $lang['EMAIL']);
		$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
		$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
		$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
		$myTemplate->assign('PROFILE', $lang['PROFILE']);
		$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
		$myTemplate->assign('LICENSE', $lang['LICENSE']);
		$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
		$myTemplate->assign('EXPIRY_DATE', $validationDate);
		$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
		$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
		$listOfUsersID = $currentUser->getAllUser($tri);
		for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
			$choixProfile="";
			$currentUser->reference = $listOfUsersID[$boucle];
			$currentUser->getUserFromDatabase('init');
			$listUserProfiles = $currentUser->getUserProfiles();
			$initProfile = true;
			for ($loop = 0; $loop < count($listUserProfiles); $loop ++) {
				if ($initProfile) {
					$choixProfile .= $club_profiles_listing[$listUserProfiles[$loop]];
					$initProfile = false;
				} else {
					$choixProfile .= "<br />".$club_profiles_listing[$listUserProfiles[$loop]];
				}
			}
			$show_password = $currentUser->isHigherRank($userSession->get_rank());
			$show_password = !$show_password;
			include("./admin/user.content.php");
		}
		$myTemplate->display('./template/footers.tpl');
		$myTemplate->display('./template/tableEnd.tpl');
	}
	break;
case "destroy" :
	$mailing_list=$database->query_and_fetch_single('SELECT MAILING_LIST_NAME FROM clubs WHERE NUM=\'1\'');	
	$currentUser->getUserFromDatabase('init');	
	$mail_class=new mailing_list($database);
	if (isset($mailing_list) && ($mailing_list != "")) {
		$mail_class->remove_email($currentUser->email['init']);	
	}
	$my_result = $currentUser->deleteUser(true, $userSession);
	$currentUser->deleteMember();
	$currentUser->deleteInstructor();
	$myResultArray = $currentUser->resultTab;
	include('./admin/results.content.php');
	if ($currentConfig->qualif_enabled) {
		$validationDate = $currentConfig->subscriptionDate;
	} else {
		$validationDate = "2037-12-31";
	}
	$club_profiles_listing = array();
	$club_profiles_listing = $currentProfile->getAllProfileName();
	unset($currentProfile);
	$myTemplate->assign('ADMIN_SUBTITLE',$lang['LIST_USER']);
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('LOGIN', $lang['LOGIN']);
	$myTemplate->assign('EMAIL', $lang['EMAIL']);
	$myTemplate->assign('LAST_NAME', $lang['LASTNAME']);
	$myTemplate->assign('FIRST_NAME', $lang['FIRSTNAME']);
	$myTemplate->assign('PROFILE', $lang['PROFILE']);
	$myTemplate->assign('USER_TYPE', $lang['USER_TYPE']);
	$myTemplate->assign('LICENSE', $lang['LICENSE']);
	$myTemplate->assign('ANNUAL_FEE', $lang['ANNUAL_FEE']);
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableHeader'.$template_suffix.'.tpl');
	$listOfUsersID = $currentUser->getAllUser($tri);
	for ($boucle = 0; $boucle < count($listOfUsersID); $boucle++) {
		$choixProfile="";
		$currentUser->reference = $listOfUsersID[$boucle];
		$currentUser->getUserFromDatabase('init');
		$listUserProfiles = $currentUser->getUserProfiles();
		$initProfile = true;
		for ($loop = 0; $loop < count($listUserProfiles); $loop ++) {
			if ($initProfile) {
				$choixProfile .= $club_profiles_listing[$listUserProfiles[$loop]];
				$initProfile = false;
			} else {
				$choixProfile .= "<br />".$club_profiles_listing[$listUserProfiles[$loop]];
			}
		}
		$show_password = $currentUser->isHigherRank($userSession->get_rank());
		$show_password = !$show_password;
		include("./admin/user.content.php");
	}
	$myTemplate->assign('RENEW_MESSAGE',$lang['USER_RENEW']);
	$myTemplate->assign('EXPIRY_DATE', $validationDate);
	$myTemplate->assign('USER_UPDATE', $lang['USER_UPDATE']);
	$myTemplate->display('template/userTableFooter'.$template_suffix.'.tpl');
	$myTemplate->display('./template/tableEnd.tpl');
	break;
case "licensing" :
	include('./admin/license_user.content.php');
	break;
case "del_license" :
	$currentUser->reference = $ref;
	$currentUser->removeMemberLicense();
	include('./admin/license_user.content.php');
	break;
case "update_license" :	
	$currentUser->reference = $ref;
	$currentUser->updateMemberLicense();
	include('./admin/license_user.content.php');
	break;
case "add_license" :
	$currentUser->reference = $ref;
	$currentUser->addMemberLicense();
	include('./admin/license_user.content.php');
	break;
case "import" :	
	$mailing_list = $database->query_and_fetch_single('SELECT MAILING_LIST_NAME FROM clubs WHERE NUM=\'1\'');
	$temporaryClub = new club($database);
	$temporaryClub->getClubFromDatabase();
	$myVar = 'init';
	$currentUser->profiles[$myVar] = $temporaryClub->usual_profiles[$myVar];
	unset($temporaryClub);	
	include("./admin/profiling.php");
	include("./admin/importCSV.content.php");
	break;
case "csvimport" :
	$currentUser->importFromCSV();
	$myResultArray = $currentUser->resultTab;
	include('./admin/results.content.php');
	break;
}
$myTemplate->display('./template/footers.tpl');
?>