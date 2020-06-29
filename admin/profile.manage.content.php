<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * profile.manage.content.php
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
 * @version    CVS: $Id: profile.manage.content.php,v 1.1.2.6 2006/07/01 06:32:59 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$myTemplate->assign('TITLE_HEADERS',$lang['LIST_PROFILE']);
$myTemplate->display('template/headersDoc.tpl');
$myTemplate->assign("MODIFY",$lang['MODIFY']);
$myTemplate->assign("DELETE",$lang['DELETE']); 
$myTemplate->assign('PROFILE_NAME', $lang['PROFILE_NAME']);
$myTemplate->assign('R_NO_AUTO_LOGOUT', $lang['R_NO_AUTO_LOGOUT']);
$myTemplate->assign('R_BOOK_ANYTIME', $lang['R_BOOK_ANYTIME']);
$myTemplate->assign('R_BOOK_ANYDURATION', $lang['R_BOOK_ANYDURATION']);
$myTemplate->assign('R_BOOK_WITHOUT_INSTRUCTOR', $lang['R_BOOK_WITHOUT_INSTRUCTOR']);
$myTemplate->assign('R_BOOK_WITH_INSTRUCTOR', $lang['R_BOOK_WITH_INSTRUCTOR']);
$myTemplate->assign('R_BOOK_INSTRUCTOR_ENHANCED',$lang['R_BOOK_INSTRUCTOR_ENHANCED']);
$myTemplate->assign('R_MANAGE_ACFT_AVAILABILITY', $lang['R_MANAGE_ACFT_AVAILABILITY']);
$myTemplate->assign('R_MANAGE_INST_AVAILABILITY', $lang['R_MANAGE_INST_AVAILABILITY']);
$myTemplate->assign('R_MANAGE_USER', $lang['R_MANAGE_USER']);
$myTemplate->assign('R_MANAGE_OWNDATA', $lang['R_MANAGE_OWNDATA']);
$myTemplate->assign('R_ORGANISATION_ADMIN', $lang['R_ORGANISATION_ADMIN']);
$myTemplate->assign('R_MANAGE_ACFT', $lang['R_MANAGE_ACFT']);
$myTemplate->assign('R_MANAGE_OWN_LICENSE', $lang['R_MANAGE_OWN_LICENSE']);
$myTemplate->assign('R_BOOK_EVERYBODY',$lang['R_BOOK_EVERYBODY']);
$myTemplate->display('template/profileTableHeader.tpl');
?>