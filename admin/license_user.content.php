<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * license_user.content.php
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
 * @version    CVS: $Id: license_user.content.php,v 1.5.2.2 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$my_qualif_tab = $currentUser->getQualifOfMember($ref);
$currentUser->getUserFromDatabase('init');
$myTemplate->assign('TITLE_LICENSE', $lang['LIST_LICENSE'].' : '.$currentUser->firstName['init'].' '.$currentUser->lastName['init']);
$myTemplate->assign('UPDATE', $lang['UPDATE']);
$myTemplate->assign('DELETE', $lang['DELETE']);
$myTemplate->assign('LICENSE_NAME_SENTENCE', $lang['LICENSE_NAME_SENTENCE']);
$myTemplate->assign('LICENSE_EXPIRY_DATE', $lang['LICENSE_EXPIRY_DATE']);
$myTemplate->assign('NEED_WARNING', $lang['NEED_WARNING']);
$myTemplate->display('./template/userLicenseTableHeader.tpl');
//include('./template/userLicenseTableHeaderTPL.php');
$alreadyExistingLicense = array();
for ($i=0; $i<count($my_qualif_tab); $i++) {
	$nodate = ($my_qualif_tab[$i]['license_endless'] == 0);
	$myTemplate->assign('LICENSE_NAME',$my_qualif_tab[$i]['license_name']);
	$myTemplate->assign('QUALIF_ID',$my_qualif_tab[$i]['id']);
	$myTemplate->assign('REF',$ref);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$alreadyExistingLicense[] = $my_qualif_tab[$i]['id'];
	if ($nodate) {
		$myTemplate->display('./template/userLicenseTableUnlimited.tpl');
	} else {
		$myTemplate->assign('LICENSE_DATE', showDate($my_qualif_tab[$i]['expire'], $userSession->isFrenchDateDisplay()));
		if ($my_qualif_tab[$i]['alert']==1) {
			$myTemplate->assign('CHECKED', 'checked="checked"');
		} else { 	
			$myTemplate->assign('CHECKED', '');
		}
		$myTemplate->assign('LICENSE_MOD', $lang['LICENSE_MOD']);
		$myTemplate->display('./template/userLicenseTable.tpl');
	}
}
if (!(count($alreadyExistingLicense) == $currentLicense->countLicense())) {
	echo('<tr style="background: #2505ac"><form action="index.php" method="POST"><td>');
	$currentLicense->getUpdatedSelectLicense('newone', $alreadyExistingLicense);
	echo('</td>
	<td>'.showDate('2006-05-24',$userSession->isFrenchDateDisplay()).'</td>
	<td><input type="checkbox" name="warn_limit" value="1" /></td>
	<td colspan="2"><input type="hidden" name="ref" value="'.$ref.'" /><input type="hidden" name="type" value="user" /><input type="hidden" name="ope" value="add_license" /><input type="submit" value="'.$lang['ADD_THIS_LICENSE'].'" /></td>
	</form></tr></table>');
}
	echo('<p>
<a href="index.php?ope=manage&type=user" class="dblink"> &nbsp;'.$lang['BACK'].'&nbsp; </a>
</p>');
?>