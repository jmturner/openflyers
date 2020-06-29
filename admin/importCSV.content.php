<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * importCSV.content.php
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
 * @version    CVS: $Id: importCSV.content.php,v 1.4.2.4 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$myTemplate->assign('CSV_TITLE', $lang['IMPORT_USER_TITLE']);
$myTemplate->assign('CSV_IMPORT_METHOD', $lang['CSV_IMPORT_METHOD']);
$myTemplate->assign('CSV_IMPORT_METHOD_EXPLANATION', $lang['CSV_IMPORT_METHOD_EXPLANATION']);
$myTemplate->assign('CSV_IMPORT_TYPE', $lang['CSV_IMPORT_TYPE']);
$myTemplate->assign('CSV_INIT_EXPLANATION', $lang['CSV_INIT_EXPLANATION']);
$myTemplate->assign('CSV_INIT', $lang['CSV_INIT']);
$myTemplate->assign('CSV_INIT_EXPLANATION', $lang['CSV_INIT_EXPLANATION']);
$myTemplate->assign('CSV_SEPARATOR_TYPE', $lang['CSV_SEPARATOR_TYPE']);
$myTemplate->assign('CSV_COMMA', $lang['CSV_COMMA']);
$myTemplate->assign('CSV_SEMI_COLON', $lang['CSV_SEMI_COLON']);
$myTemplate->assign('CSV_FILE_PATH', $lang['CSV_FILE_PATH']);
$myTemplate->assign('CSV_FILE', $lang['CSV_FILE']);
$myTemplate->assign('CSV_FILE_EXPLANATION', $lang['CSV_FILE_EXPLANATION']);
$myTemplate->assign('CSV_MORE_EXPLANATION', $lang['CSV_MORE_EXPLANATION']);
$myTemplate->assign('CSV_PROFILE_CHOICE', $lang['CSV_PROFILE_CHOICE']);
$myTemplate->assign('CSV_PROFILE', $lang['CSV_PROFILE']);
$myTemplate->assign('CSV_VIEW_MODE', $lang['CSV_VIEW_MODE']);
$myTemplate->assign('CSV_VIEW_AIRCRAFTS', $lang['CSV_VIEW_AIRCRAFTS']);
$myTemplate->assign('CSV_VIEW_AIRCRAFTS_EXPLANATION', $lang['CSV_VIEW_AIRCRAFTS_EXPLANATION']);
$myTemplate->assign('CSV_VIEW_INSTRUCTORS', $lang['CSV_VIEW_INSTRUCTORS']);
$myTemplate->assign('CSV_VIEW_INSTRUCTORS_EXPLANATION', $lang['CSV_VIEW_INSTRUCTORS_EXPLANATION']);
$myTemplate->assign('CSV_POPUP', $lang['CSV_POPUP']);
$myTemplate->assign('CSV_POPUP_EXPLANATION', $lang['CSV_POPUP_EXPLANATION']);
$myTemplate->assign('CSV_DATE_FORMAT', $lang['CSV_DATE_FORMAT']);
$myTemplate->assign('CSV_DATE_FORMAT_EXPLANATION', $lang['CSV_DATE_FORMAT_EXPLANATION']);
$myTemplate->assign('CSV_MAILING_LIST_HEADER', $lang['CSV_MAILING_LIST_HEADER']);
$myTemplate->assign('CSV_SUBSCRIBE_ML', $lang['CSV_SUBSCRIBE_ML']);
$myTemplate->assign('CSV_MAILING_LIST', $lang['CSV_MAILING_LIST']);
$myTemplate->assign('CSV_VALIDATE', $lang['CSV_VALIDATE']);
$myTemplate->assign('VALIDATE', $lang['VALIDATE']);
$myTemplate->assign('CSV_WARNING', $lang['CSV_WARNING']);
$myTemplate->assign('BACK', $lang['BACK']);
$myTemplate->assign('CSV_SELECT_PROFILE', $choixProfile);
$myTemplate->assign('CSV_USER_HOME_PHONE', $lang['CSV_USER_HOME_PHONE']);
$myTemplate->assign('CSV_USER_HOME_PHONE_EXPLANATION', $lang['CSV_USER_HOME_PHONE_EXPLANATION']); 
$myTemplate->assign('CSV_USER_WORK_PHONE', $lang['CSV_USER_WORK_PHONE']);
$myTemplate->assign('CSV_USER_WORK_PHONE_EXPLANATION', $lang['CSV_USER_WORK_PHONE_EXPLANATION']);
$myTemplate->assign('CSV_USER_CELL_PHONE', $lang['CSV_USER_CELL_PHONE']);
$myTemplate->assign('CSV_USER_CELL_PHONE_EXPLANATION', $lang['CSV_USER_CELL_PHONE_EXPLANATION']);
$myTemplate->assign('CSV_USER_EMAIL', $lang['CSV_USER_EMAIL']);
$myTemplate->assign('CSV_USER_EMAIL_EXPLANATION', $lang['CSV_USER_EMAIL_EXPLANATION']);
$myTemplate->display('./template/importCSVlight.php');
?>