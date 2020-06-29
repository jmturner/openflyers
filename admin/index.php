<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * index.php
 *
 * administration interface
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
 * @category   Admin interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: index.php,v 1.49.2.6 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

/* import required files */
require_once('./classes/permits.php'); 		// permits analysis
require_once('./classes/basicTemplate.class.php'); 	// basic template engine
require_once('./classes/APIconfig.class.php'); 		// API config manager
if ( !( $userSession->is_set_club_parameters_allowed()                                                                    
        or $userSession->is_set_pilots_file_allowed()                                                                     
        or $userSession->is_set_aircrafts_file_allowed()
      ) ) {
    die('You are not allowed to be there');
}
$database->connect();
// VAR DECLARATION
$myResultArray = array();
$translatedResultArray = array();
$ope=define_variable('ope','manage');		// Definition de la variable OPE
$type=ucfirst(strtolower(define_variable('type','')));			// Definition de la variable TYPE
$myTemplate = new basicTemplate();
$currentConfig = new APIconfig($database);
//inclusion
require_once('./admin/manage'.$type.'.php');
?>