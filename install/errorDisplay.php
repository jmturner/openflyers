<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * errorDisplay.php
 *
 * Display error message during OF installation
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
 * @category   html, install
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: errorDisplay.php,v 1.2.4.2 2005/10/30 10:59:26 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 7 2005
 */

function initWrongConfigMes() {
    ?><html><head><title>OpenFlyers</title></head><body><?php
}

function endWrongConfigMes() {
    ?></body></html><?php
    die();
}

function displayWrongConfigMes($text='')
{
    initWrongConfigMes();
    if($text=='')
    {
?><h1>Unable to config and install properly OpenFlyers.<br />Some OpenFlyers files are missing.</h1>
<h1>Impossible de configurer et d'installer correctement OpenFlyers.<br />Certains fichiers d'OpenFlyers sont absents.</h1><?php
    }
    else
    {
?><h1><?php echo($text);?></h1><?php
    }
    endWrongConfigMes();
}
?>