<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * legendPopup.php
 *
 * Display legend for book colors
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
 * @version    CVS: $Id: legendPopup.php,v 1.3.2.5 2007/10/03 09:35:58 claratte Exp $
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

// OF Date class based on PEAR one
require_once('classes/Date.class.php');

require_once('conf/config.php');
// pool functions
require_once('pool/functions.php');

// Serie manager class
require_once('classes/serie.class.php');

// Connection to database with initialisation of $database class
require_once('conf/connect.php');		// DataBase parameters
require_once('classes/db.class.php');	// MySQL database connector
$database=new DBAccessor(HOST,BASE,VISITOR,PASSWORD_VISITOR);

require_once('classes/userSession.class.php');
$userSession=new userSession($database);
define_global('userLang',DEFAULT_LANG);
$userSession->openLangFile($userLang);
///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
$title='L&eacute;gende des couleurs';
define('CLOSE_WINDOW',1);
require_once('includes/header.php');
?>	</head>
<?php

if(isset($_REQUEST['hideLegendPopup'])or!($userSession->isLegendPopup()))
{
    if(!$userSession->isNothingAllowed())
    {
        $userSession->setLegendPopup(false);
    }
?>
<script type="text/javascript">
window.close();
</script>
<?php
}
else
{
?><body>
    <h3><?php echo($lang['LEGEND_USED_SLOT']);?></h3>
    <table class="BOOKVIEW">
        <thead>
        <tr>
			<th>&nbsp;</th>
			<th><?php echo($lang['LEGEND_OWNSELF']);?></th>
			<th><?php echo($lang['LEGEND_OTHER']);?></th>
		</tr>
		</thead>
		<tr class="line">
			<th><?php echo($lang['ALONE']);?></th>
			<td class="l">&nbsp;</td>
			<td class="o">&nbsp;</td>
		</tr>
		<tr class="line">
			<th><?php echo($lang['LEGEND_INSTRUCTION']);?></th>
			<td class="m">&nbsp;</td>
			<td class="p">&nbsp;</td>
		</tr>
		<tr class="line">
			<th><?php echo($lang['LEGEND_UNFREE']);?></th>
			<td colspan="2" class="q">&nbsp;</td>
		</tr>
    </table>
    <h3><?php echo($lang['LEGEND_FREE']);?></h3>
    <table class="BOOKVIEW">
		<thead class="line">
		<tr>
			<th><?php echo($lang['LEGEND_DAY']);?></th>
			<th><?php echo($lang['LEGEND_TWILIGHT']);?></th>
			<th><?php echo($lang['LEGEND_NIGHT']);?></th>
		</tr>
		</thead>
		<tr class="line">
			<td class="d">&nbsp;</td>
			<td class="t">&nbsp;</td>
			<td class="n">&nbsp;</td>
		</tr>
	</table><?php
	if(!$userSession->isNothingAllowed())
	{
		require_once('./displayClasses/requestForm.class.php');
		$request=new requestForm('','','simple','legendPopup');
		$request->addHidden('hideLegendPopup',true);
		$request->close($lang['LEGEND_NO_MORE_DISPLAY']);
	}
}
require_once('includes/footer.php');
?>
