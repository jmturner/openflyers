<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageLicense.php
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
 * @version    CVS: $Id: manageLicense.php,v 1.1.2.3 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

require_once("./classes/license.class.php");
$ref = define_variable("ref",-1);
$currentLicense = new license($ref, $database);
include('./admin/headers.content.php');
require_once('./admin/menu.php');
switch ($ope) {
	case "manage" :
		include('./admin/license.content.php');
		break;
	case "add" :
		$admin_subtitle='ADD_LICENSE_TITLE';
		$currentLicense->createBlankLicense();
		include("./template/licenseAddModTPL.php");
		break;	
	case "modify" :	
		$admin_subtitle='MOD_CLUB_TITLE';
		$currentLicense->getLicenseFromDatabase();
		include("./template/licenseAddModTPL.php");
		break;
	case "destroy" :
		$currentLicense->deleteLicense(true);
		$myResultArray = $currentLicense->resultTab;
		include('./admin/results.content.php');
		include('./admin/license.content.php');
		break;
	case "db" :
		$currentLicense->getLicenseFromForm();							
		$currentLicense->saveLicense();
		$myResultArray = $currentLicense->resultTab;
		include('./admin/results.content.php');
		if ($currentLicense->error) {
			$admin_subtitle='MOD_LICENSE_TITLE';
			include("./template/licenseAddModTPL.php");
		} else {
			include('./admin/license.content.php');
		}
		break;
}
$myTemplate->display('./template/footers.tpl');
?>