<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * connect.php
 *
 * Handle database configuration form submission
 * Update conf/connect.php
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
 * @version    CVS: $Id: updateconnect.php,v 1.6.4.1 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Jun 10 2003
 */

require_once('../conf/config.php');
require_once('../lang/'.DEFAULT_LANG.'.php');

//update config params
define('CONNECT_FILE_NAME','../conf/connect.php');
$file=fopen(CONNECT_FILE_NAME, 'r');
$connectFile = fread($file, filesize(CONNECT_FILE_NAME));
fclose($file);

//get html form variables
$host=$_POST['host'];
$base=$_POST['base'];
$user=$_POST['user'];
$password=$_POST['password'];

//test db connection
mysql_connect($host, $user, $password) OR die('Error: '.mysql_error());

//update config params
$connectFile = ereg_replace('@HOST@',$host,$connectFile);
$connectFile = ereg_replace('@BASE@',$base,$connectFile);
$connectFile = ereg_replace('@USER@',$user,$connectFile);
$connectFile = ereg_replace('@PASSWORD@',$password,$connectFile);

//write new config file
$file=fopen(CONNECT_FILE_NAME, 'w');
fwrite($file,$connectFile);
fclose($file);

//install db
header('Location: askinfo.php');
?>
