<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Coordinates.php
 *
 * latitude and longitude conversions
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
 * @category   computation
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: coordinates.class.php,v 1.3.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Jun 9 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

/*
* Concrete class to get Sunrise & Sunset time
* Self calculates the values, don't use an internet website
*/

class coordinates
{
    /**
    * the latitude in degrees
    * @var float
    */
    var $latitude;
    /**
    * the longitude in degrees
    * @var float
    */
    var $longitude;
    
    /**
    * Constructor
	* set latitude and longitude according paramaters
    * @access public
    * @param $lat string latitude (NDDMMSS format N='S' or 'N')
    * @param $long string longitude (NDDMMSS format N='W' or 'E')
	* @return null
	*/
    function Coordinates($lat,$long)
    {
        $this->setLatitude($lat);
        $this->setLongitude($long);
    }
    
    /**
	* set latitude according paramater
    * @access public
    * @param $lat string latitude (NDDMMSS format N='S' or 'N')
	* @return null
	*/
    function setLatitude($lat)
    {
        $latd = substr($lat,1,2);
        $latm = substr($lat,3,2);
        $lats = substr($lat,5,2);
        $latx = substr($lat,0,1);
        $this->latitude=$latd+$latm/60+$lats/3600;
        if ($latx=='S')
        {
            $this->latitude=-$this->latitude;
        }
    }

    /**
	* set longitude according paramater
    * @access public
    * @param $long string longitude (NDDMMSS format N='W' or 'E')
	* @return null
	*/
    function setLongitude($long)
    {
		$lond = substr($long,1,3);
		$lonm = substr($long,4,2);
		$lons = substr($long,6,2);
		$lonx = substr($long,0,1);
        $this->longitude=$lond+$lonm/60+$lons/3600;
        if ($lonx=='W')
        {
            $this->longitude=-$this->longitude;
        }
    }
}
?>