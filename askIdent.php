<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * askIdent.php
 *
 * display legend for book colors
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
 * @category   Display
 * @author     Christophe Laratte <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: askIdent.php,v 1.4.2.4 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Apr 6 2003
 */

// security constant used by others php files to test if they are called within index.php or not
define('SECURITY_CONST',1);

require_once('./conf/config.php');

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.PEAR_DIRECTORY);
}

require_once('lang/'.DEFAULT_LANG.'.php');		// default language file necessary for correct initialisation of class definitions

require_once('displayClasses/requestForm.class.php');

$title=$lang['ASK_IDENT_TITLE'];
///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
require_once('includes/header.php');

?>	</head>
	<script type="text/javascript" SRC="javascript/askIdent.js"></script>
	<body onload="document.getElementById('login').focus();">
<?php
$ask_form=new requestForm($lang['ASK_IDENT_EXPLAIN'],'onsubmit="transmite();"','Large');
$ask_form->addInput($lang['ASK_LOGIN'],'login');
$ask_form->addInput($lang['ASK_PWD'],'password',true);
$ask_form->close($lang['VALIDATE']);
require_once('includes/footer.php');
?>
