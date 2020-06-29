<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * srssSource.php
 *
 * Get Sunrise & Sunset times for a day
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
 * @category   time computing
 * @author     Patrice Godard <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: srssSource.class.php,v 1.2.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Tue Mar 11 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

/*
* Get Sunrise/Sunset source instance
*/

function srssSource_getInstance($providerClass)
{
	return new $providerClass();
}


/*
* Base class for a SR/SS Source
*/

class srssSource
{
	/*
	* Get Sunrise & Sunset
	*/

	/**
	* use data provider to calculate SunRise, SunSet, Dawn & Twilight
	* This method must be overriden in derived classes
    * @access public
    * @param $date ofDate class (day)
    * @param $coords coordinates class of the location
    * @param &$sr ofDate class SunRise
    * @param &$ss ofDate class SunSet
    * @param &$dawn ofDate class Dawn
    * @param &$twilight ofDate class Twilight
	* @return boolean (compute ok or not)
	*/
	function getSRSS($date,$coords, &$sr, &$ss, &$dawn, &$twilight)
	{
		
	}

}
?>