<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * install_db.php
 *
 * Create database & initialize tables
 * Check for session directory presence
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
 * @author     Patrice Godard <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: install_db.php,v 1.14.4.4 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Jun 10 2003
 */

define('CONFIG_FILE_NAME','../conf/config.php');

function replaceVar($name)
{
    global $configFile;
    if (isset($_POST[$name]))
    {
        $configFile=ereg_replace('@'.$name.'@',$_POST[$name],$configFile);
    }
}

// First, we save config parameters !
$file=fopen(CONFIG_FILE_NAME, 'r');
$configFile=fread($file, filesize(CONFIG_FILE_NAME));
fclose($file);
replaceVar('MAIL_FACTORY');
replaceVar('MAIL_HOST');
replaceVar('MAIL_AUTH_NAME');
replaceVar('MAIL_AUTH_PASSWORD');
$file=fopen(CONFIG_FILE_NAME, 'w');
fwrite($file,$configFile);
fclose($file);

require_once('../conf/connect.php');
require('defines.lib.php');
require('read_dump.php');
require_once('../conf/config.php');
require_once('../lang/'.DEFAULT_LANG.'.php');

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.str_replace('/install','',PEAR_DIRECTORY));
}

/**
 * Set up value for some variables
 */
$sql_file_init= "../sql/openflyers_init.sql";

$DBisEmpty=$_POST['dbisempty'];

mysql_connect(HOST, VISITOR, PASSWORD_VISITOR) OR die('Error: '.mysql_error());
if(mysql_select_db(BASE) == false)
{
        echo($lang['INSTALL_DATABASE_NOTFOUND'].' ('.BASE.')<br />');
        //no db found, try to create one
        mysql_query('create database '.BASE) or die ($lang['INSTALL_DATABASE_CREATIONFAILED']);
        echo("base de données ".BASE." créée.<br />");
}

if ($DBisEmpty)
{
    echo $lang['INSTALL_CREATE_TABLES'].'<br />';
    initDatabase($sql_file_init);
}
else
{
  echo($lang['INSTALL_TABLES_FOUND'].'<br />');
  echo($lang['INSTALL_TABLES_UPDATE'].'<br />');
  include ('update_db.php'); 
}

//traitement du changement de fuseau
if (isset($_POST['HOST_TIMEZONE']))
{
  $timezone=$_POST['HOST_TIMEZONE'];
  require_once 'Date.php';
  
  $result=mysql_query('select id,start_date,end_date from booking');
  	while ($row=mysql_fetch_object($result)) {
      
      //get dates
      $start_date=new Date($row->start_date);
      $end_date=new Date($row->end_date);
      
      //set dates timezone
      $start_date->setTZ($timezone);
      $end_date->setTZ($timezone);
      
      //convert dates to UTC
      $start_date->convertTZbyID('UTC');
      $end_date->convertTZbyID('UTC');
      
      //get dates TimeStamp
      $ts_start_date=$start_date->getDate(DATE_FORMAT_TIMESTAMP);
      $ts_end_date=$end_date->getDate(DATE_FORMAT_TIMESTAMP);
      
 
      $sql_query='update booking set start_date=\''.$ts_start_date.'\' ,end_date=\''.$ts_end_date.'\' where id='.$row->id;
      @mysql_query($sql_query) or die('<b>'.mysql_error().'<br />'.$sql_query.'<br />Le script a echou&eacute; il est conseill&eacute de restaurer votre base de donn&eacute;e<br /></b>');
    	
    	
	}
      $sql_query='update clubs set default_timezone=\''.$timezone.'\'';
      @mysql_query($sql_query) or die('<b>'.mysql_error().'<br />'.$sql_query.'<br /></b>');
      
      $sql_query='update authentication set timezone=\''.$timezone.'\'';
      @mysql_query($sql_query) or die('<b>'.mysql_error().'<br />'.$sql_query.'<br /></b>');

      echo ($lang['INSTALL_TO_UTC_SUCCEED'].'<br />');
}

//definition de la langue
$sql_query='update clubs set lang=\''.DEFAULT_LANG.'\' where lang=\'\'';
@mysql_query($sql_query) or die('<b>'.mysql_error().'<br />'.$sql_query.'<br /></b>');
$sql_query='update authentication set lang=\''.DEFAULT_LANG.'\' where lang=\'\'';
@mysql_query($sql_query) or die('<b>'.mysql_error().'<br />'.$sql_query.'<br /></b>');
echo ($lang['INSTALL_LANG_SUCCEED'].'<br />');

@mysql_close();

//check for sessions directory presence
$sessionDir = ini_get("session.save_path");
if(!is_dir($sessionDir)){
  echo("Création du répertoire de sessions:" .$sessionDir."<br />");
  @mkdir($sessionDir); //or die("ERREUR: Création du répertoire de sessions: ".$sessionDir." impossible. Veuillez le créer manuellement et relancer l'installation.<br />");
}
else{
  echo("Répertoire de sessions: " .$sessionDir." trouvé.<br />");
}

include("postInstallMessage.html");
?>
