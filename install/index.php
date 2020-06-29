<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * index.php
 *
 * Handle automatic installation
 * (update config files, create and initialize database)
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
 * @author     Soeren MAIRE
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: index.php,v 1.6.4.1 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Feb 8 2005
 */

require_once('errorDisplay.php');
require_once('../conf/config.php');
if (!defined('DEFAULT_LANG'))
{
    displayWrongConfigMes();
}

$browserLangs=explode(',',strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));

// get all lang file names
$languages=array();
$langDir='../lang';
if (!is_dir($langDir))
{
    displayWrongConfigMes();
}

$langDirPtr=opendir($langDir);
if(!$langDirPtr)
{
    displayWrongConfigMes();
}

$i=0;
while (($fileName = readdir($langDirPtr)) !== false)
{
    if (substr($fileName,strlen($fileName)-4,4)=='.php')
    {
        $languages[$i]=substr($fileName,0,strlen($fileName)-4);
        require_once($langDir.'/'.$fileName);
        $iso639[$i]=$lang['ISO639'];
        $i++;
    }
}
closedir($langDirPtr);

$foundLang=-1;
foreach ($browserLangs as $key=>$element)
{
    $element=substr($element,0,strcspn($element,';'));
    if ($foundLang==-1)
    {
        if (in_array($element,$iso639))
        {
            $foundLang=$key;
        }
    }
}

if (DEFAULT_LANG=='@DEFAULT_LANG@')
{
?>
<html><head><title>OpenFlyers</title></head>
<body>
<div align="center">
<h1>OpenFlyers installation/update</h1>
<h1>Installation/mise à jour d'Openflyers</h1>
<form action="install.php" method="POST">
<table border="0" align="center">
<tr><td>S&eacute;lectionnez votre langue</td>
<td rowspan="2"><select name="language">
<?php
    foreach ($languages as $key => $langName)
    {
        ?><option value=<?php
        echo('"'.$langName.'"');
        if ($iso639[$key]==$browserLangs[$foundLang])
        {
            echo(' selected="selected"');
        }
        ?>><?php
        echo($langName);
        ?></option><?php
    }
?>
</select></td></tr>
<tr><td>Please select your language</td></tr></table>
<div align="center"><input type="submit" value="OK"></div>
</form></body></html>
<?php
}
else
{
    if (!in_array(DEFAULT_LANG,$languages))
    {
        displayWrongConfigMes();
    }
    header('Location: install.php');
}
?>