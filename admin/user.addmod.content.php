<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * user.addmod.content.php
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
 * @version    CVS: $Id: user.addmod.content.php,v 1.1.2.2 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// User ADD or MOD page content displayed using template
$myTemplate->assign("SUBTITLE",$lang[$title_user]);
$myTemplate->assign("FORM_NAME","user_manager");
$myTemplate->assign("FORM_ENCTYPE","application/x-www-form_urlencoded");
$myTemplate->assign("FORM_OBJECT_TYPE","user");
$myTemplate->assign("FORM_OPE","db");
$myTemplate->assign("FORM_REF",$ref);
$myTemplate->display('template/adminAddModHeader.tpl');
?>