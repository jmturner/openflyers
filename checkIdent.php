<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * checkIdent.php
 *
 * check if Id and Password (GET or POST given) are correct according the database
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
 * @category   Web service
 * @author     Christophe Laratte <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: checkIdent.php,v 1.4.4.4 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Dec 6 2004
 */

/* If your openflyers is located at http://test.org/
* just post at http://test.org/checkIdent.php
* with login and password variables (POST recommended neitherwise http://test.org/checkIdent.php?login=jbond&password=007
* the script display an answer code
* One of this value :
* 0 : OK
* 1 : OK but choose profile
* 2 : outdate but authorized
* 3 : outdate but authorized with outdate profile
* 4 : outdate subscription, unauthorized
* 5 : bad Ident, unauthorized
* 6 : Banned (ip or login), unauthorized
* 7 : no Ident -> ask one
*
* We recommend you to consider 0-2 OK and 3-7 bad
* WARNING : you have to filter public access login (with no right) because for OF, it's a valid access !!!

Here an example how to send a post request with php :

function httpPostRequest($host, $path, $postData)
{ 
  $res = ""; 

  $request = "POST $path HTTP/1.1\n". 
  "Host: $host\n". 
  (($referer) ? "Referer: $referer\n" : ""). 
  "Content-type: Application/x-www-form-urlencoded\n".
  "Content-length: ".strlen($postData)."\n". 
  "Connection: close\n\n". 
  $postData."\n"; 
  
  // Some debug for you my friend :) 
  // print("<pre>Request:\n".htmlentities($request)."</pre>"); 

  if ($fp = fsockopen($host, 80, &$errno, &$errstr, 3))
	{ 
    if (fputs($fp, $request)) 
    { 
      while(! feof($fp)) 
      { 
       $res .= fgets($fp, 128); 
      } 
      fclose($fp); 
//	  print($res);
      return $res; 
    } 
  } 
}

$postData='login=jbond&password=007';
$result=httpPostRequest('test.org','http://test.org/checkIdent.php',$postData);

*/



// security constant used by others php files to test if they are called within index.php or not
define('SECURITY_CONST',1);

require_once('./conf/config.php');

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.PEAR_DIRECTORY);
}

// Connexion to database with initialisation of $database class
require_once('./conf/connect.php');		// DataBase parameters
require_once('./classes/db.class.php');	// MySQL database connector

$database=new DBAccessor(HOST,BASE,VISITOR,PASSWORD_VISITOR);

// OF Date class based on PEAR one
require_once('./classes/Date.class.php');
Date_TimeZone::setDefault('UTC');

// pool functions
require_once('./pool/functions.php');

// authentification and session opening with initialisation of $userSession class
require_once('./classes/userSession.class.php');

PEAR::setErrorHandling(PEAR_ERROR_PRINT);   // useful to debug : display the error message

$userSession=new userSession($database);

// $login variable is posted by connexion form otherwise is set ''
define_global('login');
define_global('password');

if ($login=='')
{
    echo ('7');
    return;
}
list($errNum,$para1,$para2)=$userSession->kernelAuth($login,$password);

echo($errNum);
?>