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
 * @version    CVS: $Id: userLicenseTableHeaderTPL.php,v 1.2.2.2 2005/11/07 11:03:30 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$myTemplate->assign('TITLE_LICENSE', $lang['LIST_LICENSE']);
$myTemplate->assign('UPDATE', $lang['UPDATE']);
$myTemplate->assign('DELETE', $lang['DELETE']);
$myTemplate->assign('LICENSE_NAME_SENTENCE', $lang['LICENSE_NAME_SENTENCE']);
$myTemplate->assign('LICENSE_EXPIRY_DATE', $lang['LICENSE_EXPIRY_DATE']);
$myTemplate->assign('NEED_WARNING', $lang['NEED_WARNING']);
$myTemplate->display('./template/userLicenseTableHeader.tpl');
?>