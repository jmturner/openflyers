<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * headers.content.php
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
 * @version    CVS: $Id: headers.content.php,v 1.5.2.3 2005/11/30 21:54:03 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

if ($ope=="modify" && $type=="club") { 
	$myTemplate->assign('ONLOAD',' onload="is_new_icao()"');
} else {
	$myTemplate->assign('ONLOAD','');
}
$myTemplate->assign('ADMIN_TITLE',$lang['ADMIN_TITLE']);
$myTemplate->assign('MANAGE_USER_DELETE_CONFIRM',$lang['MANAGE_USER_DELETE_CONFIRM']);
$myTemplate->display('template/headers.tpl');
?>