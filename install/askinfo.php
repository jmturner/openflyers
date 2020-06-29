<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * askInfo.php
 *
 * Ask database connection informations
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
 * @category   install
 * @author     Soeren MAIRE
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: askinfo.php,v 1.8.4.5 2006/01/18 08:36:17 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Sep 13 2005
 */

require_once('../conf/connect.php');
require_once('../conf/config.php');
require_once('../lang/'.DEFAULT_LANG.'.php');
define('CONFIG_FILE_NAME','../conf/config.php');

if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.str_replace('/install','',PEAR_DIRECTORY));
}

function addInput($name)
{
    global $configFile;
    global $lang;
    if (ereg('@'.$name.'@',$configFile))
    {
        ?><tr><td align="right"><?php echo($lang['INSTALL_INPUT_'.$name]);?></td><td align="left"><input name="<?php echo($name);?>"/><?php echo($lang['INSTALL_INPUT_COMMENT_'.$name]);?></td></tr><?php
    }
}


$DBisEmpty=true;
if (mysql_connect(HOST, VISITOR, PASSWORD_VISITOR))
{
    if(mysql_select_db(BASE))
    {
        //check if database have already been initialized
        $DBisEmpty=(mysql_numrows(mysql_query('SHOW TABLES')) == 0);
    }
    if (!$DBisEmpty) {
        $tablesList = array('aircrafts','authentication','booking','clubs','exceptionnal_inst_dates','icao','instructors','ip_stopped','login_stopped','logs','member_qualif','members','parameter','profiles','qualification','regular_presence_inst_dates');
        $nativeTables = array();
        foreach ($tablesList as $tableName) {
            if (@mysql_numrows(mysql_query('SHOW COLUMNS FROM '.$tableName)) > 0) {
                $nativeTables[] = $tableName;
            }
        }
        if (count($nativeTables)==0) {
            $DBisEmpty = true;
        }
    }
}

$configFile=fread(fopen(CONFIG_FILE_NAME, 'r'), filesize(CONFIG_FILE_NAME));

?><html><title>OpenFlyers</title><body><div align="center"><h1><?php echo $lang['INSTALL_TITLE'];?></h1>
<?php
if (!$DBisEmpty) {
    echo '<b>'.$lang['INSTALL_DB_ADVISE'].'</b><br />';
    foreach ($nativeTables as $key => $tableName) {
        if ($key) {
            echo (', ');
        }
        echo ($tableName);
    }
    echo '<br /><b>'.$lang['INSTALL_DB_ADVISE2'].'<br />'.$lang['INSTALL_DB_ADVISE3'].'</b>';
}
?>
<form method="POST" action="install_db.php">
<table>
<input type="hidden" name="dbisempty" value="<?php echo ($DBisEmpty);?>"/>
<?php
require_once 'Date.php';
$offsetTZ=date('Z')*1000;
$offsetTZList=array();
$saved=$_DATE_TIMEZONE_DATA;
foreach ($saved as $key=>$element)
{
    $offsetTZList[$key]=$element['offset'];
}
$currentTZ=array_search($offsetTZ,$offsetTZList);

// TIMEZONE initialisation, convert date and time to UTC
$noTimezone = true;
if (!$DBisEmpty)
{
    $result=mysql_query('SHOW FIELDS FROM clubs');
  	while ($row=@mysql_fetch_object($result))
  	{
        if ($row->Field=='DEFAULT_TIMEZONE')
        {
            $noTimezone = false;
        }
  	}
}

if ($noTimezone)
{
    echo '<tr><td align="right">'.$lang['INSTALL_TIMEZONE'].'</td>';
    echo '<td align="left"><select name="HOST_TIMEZONE">';

    $alreadySelected=false;
    while($elem=each($_DATE_TIMEZONE_DATA))
    {
        ?><option value=<?php
        echo('"'.$elem['key'].'"');
        if ((!$alreadySelected)and($elem['key']==$currentTZ))
        {
            $alreadySelected=true;
            echo(' selected="selected"');
        }
        ?>><?php echo(floor($elem['value']['offset']/3600000).' '.$elem['key']);?></option><?php
    }
    echo '</td></tr>';
}
mysql_close();

addInput('MAIL_FACTORY');
addInput('MAIL_HOST');
addInput('MAIL_AUTH_NAME');
addInput('MAIL_AUTH_PASSWORD');
?><tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2" align="center"><?php echo $lang['INSTALL_SUBMIT'];?></td></tr>
<tr><td colspan="2" align="center"><font color="red"><b><?php echo $lang['INSTALL_SUBMIT_WARNING'];?></b></font></td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="OK"/></td></tr>
</table>
</form>
</div></body></html>