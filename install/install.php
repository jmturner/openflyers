<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * install.php
 *
 * Handle automatic installation
 * (update config files, create and initialize database)
 * PEAR package tests come from SPIP-AGORA install file
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
 * @author     Patrice Godard <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: install.php,v 1.6.4.3 2006/01/16 17:00:51 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 7 2005
 */

require_once('errorDisplay.php');

define('CONNECT_FILE_NAME','../conf/connect.php');
define('CONFIG_FILE_NAME','../conf/config.php');

//Test if a language value was sent
if (isset($_POST['language']))
{
    // If so, we have to save this value in the config file. Buf, first we check if file exist !
    $language=$_POST['language'];
    if(!is_file('../lang/'.$language.'.php'))
    {
        displayWrongConfigMes();        
    }
    $file=fopen(CONFIG_FILE_NAME, 'r');
    $configFile=fread($file, filesize(CONFIG_FILE_NAME));
    fclose($file);
    $configFile=ereg_replace('@DEFAULT_LANG@',$language,$configFile);
    $file=fopen(CONFIG_FILE_NAME, 'w');
    fwrite($file,$configFile);
    fclose($file);
}

require_once('../conf/config.php');
require_once('../lang/'.DEFAULT_LANG.'.php');

// Test PEAR directory

// PEAR packages required
$pearRequiredPacks = array('Archive/Tar.php', 'Console/Getopt.php', 'Date.php', 'Mail.php', 'Net/SMTP.php', 'Net/Socket.php', 'OLE.php', 'PEAR.php', 'Spreadsheet/Excel/Writer.php', 'XML/RPC.php');

$missingPacks = array();
$openedPacks = array();

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.str_replace('/install','',PEAR_DIRECTORY));
}

// We create paths list
$pathPackage = ini_get('include_path');
$explosedPaths = explode(PATH_SEPARATOR, $pathPackage);

// For each PEAR file required, we look in every path if we can open it
// We put successfully opened file in the openFile array

foreach ($pearRequiredPacks as $pearFile) {
    foreach ($explosedPaths as $currentPath) {
        if (substr ($currentPath, strlen($currentPath)-1, 1) != '/') $currentPath .= '/';
        if ($handler=file_exists($currentPath.$pearFile)) {
            $openedPacks[] = $pearFile;
        }
    }
}

//On parcourt à nouveau la liste des fichiers nécessaire
//et on fait la différence avec ceux qui sont stockés dans $fichier_vouert
// This time we check missing file (which are not opened) from the $pearRequiredPacks

foreach ($pearRequiredPacks as $pearFile) {
    if (!in_array($pearFile, $openedPacks))
    $missingPacks[] = $pearFile;
}

// If there is a missing PEAR pack, we display a message and stop the installation
if (sizeof($missingPacks)>0) {
    initWrongConfigMes();
    echo '<h1>'.$lang['INSTALL_MISSING_PEAR_PACKS'].'</h1>';
    echo '<ul>';
    foreach ($missingPacks as $value) {
        echo '<li><a href=\'http://pear.php.net\'>'.$value.'</a></li>';
    }
    echo '</ul>';
    echo '<p>'.$lang['INSTALL_PEAR_ADIVSE'].'</p>';
    echo '<br />'.dirname($_SERVER['SCRIPT_FILENAME']);
    endWrongConfigMes();
}
/*else{
    // ajout Olivier Mansour, test de la version de NestedSet
    require_once ('DB/NestedSet.php');
    $obj = new DB_NestedSet('');
    if (method_exists($obj, 'apiVersion')) {    // verification de l'existance de cette methode
    $api = $obj->apiVersion();
    $version = str_replace ('.', '', $api['version']);
    if ($version < 13) { // au moins version 1.3 pour NestedSet
    install_debut_html();
    $etape2 = false;
    echo '<p>'._T('maj_nestedset').'</p';
    install_fin_html();
    }
    // fin ajout Olivier Mansour


    //On continue l'installation, la variable étape 2 reste à true...
    if ($etape2) {
        install_debut_html();
        echo "<p>"._T('configuration_ok')."</p>";
    }
    } else { // très vielle version de Nestedset
    install_debut_html();
    $etape2 = false;
    echo '<p>'._T('maj_nestedset').'</p';
    install_fin_html();
    }
}*/


//continue installation
$connectFile = fread(fopen(CONNECT_FILE_NAME, 'r'), filesize(CONNECT_FILE_NAME));

//if connect.php needs to be initialised, display form, else install database
if (ereg('@HOST@',$connectFile))
{
    header('Location: connect.php');
}
else
{
    header('Location: askinfo.php');
}
?>
