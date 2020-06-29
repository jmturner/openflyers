<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * config.php
 *
 * configuration file
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
 * @category   config
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: config.php,v 1.29.2.9 2007/10/03 11:47:26 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Mar 12 2003
 */

// OpenFlyers release number
define('OF_RELEASE','1.3.1');

// OpenFlyers debug option : useful to debug the application (no time-out, no login fail count, display time counter)
define ('OF_DEBUG','off');

// OpenFlyers help path
define('OF_HELP','http://wiki.openflyers.org/index.php/UserDoc1.2');
define('OF_BTS','http://bts.openflyers.org/');

//Sunrise & Sunset source name (full file name must be <sourceName>.class.php)
define ('SRSS_SOURCE','CalculatedSrssSource');

define('ROOT_PATH',dirname($_SERVER['SCRIPT_FILENAME']).'/');

// type of mail driver allowed with current host
define ('MAIL_FACTORY','@MAIL_FACTORY@');
define ('MAIL_HOST','@MAIL_HOST@');        // should be change : with localhost you don't send mail far away !
define ('MAIL_AUTH_NAME','@MAIL_AUTH_NAME@');           // if you have authentication with smtp fill this. Work only with smtp factory
define ('MAIL_AUTH_PASSWORD','@MAIL_AUTH_PASSWORD@');       // if you have authentication with smtp fill this. Work only with smtp factory

// default language choosed for the host
define ('DEFAULT_LANG','@DEFAULT_LANG@');

// session max time in user mode (ie: on user pages)
define ('USER_SESSION_MAX_TIME',300);     // 60*5 minutes

// session max time in admin mode (ie: on admin pages)
define ('ADMIN_SESSION_MAX_TIME',1200);    // 60*20 minutes

// PEAR directory access or NULL
define ('PEAR_DIRECTORY','');

// Webmaster alert file or NULL
define ('WEBMASTER_FILE', null);
?>