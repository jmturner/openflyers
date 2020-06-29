<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CalculatedSrssSource.php
 *
 * Compute sunrise, sunset, twilight and dawn for a given day
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
 * @author     Alain Mathias <alain.mathias@free.fr>
 * @author     Patrice Godard <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: CalculatedSrssSource.class.php,v 1.2.2.1 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Sep 21 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require('./classes/srssSource.class.php');
require_once('./classes/Date.class.php');
require_once('./classes/coordinates.class.php');


    // Donne le sinus de $x en degres
    function sinus($x)
    {
        $y=sin(deg2rad($x));
        return $y;
    }

    // Donne le cosinus de $x en degres
    function cosinus($x)
    {
        $y=cos(deg2rad($x));
        return $y;
    }

    // Donne la tangente de $x en degres
    function tangente($x)
    {
        $y=tan(deg2rad($x));
        return $y;
    }

    // Donne l'arctangente en degres de $x
    function arctangente($x)
    {
        $y=rad2deg(atan($x));
        return $y;
    }

    // Donne l'arcsinus en degres de $x
    function arcsinus($x)
    {
        $y=rad2deg(asin($x));
        return $y;
    }

    // Donne l'arcosinus en degres de $x
    function arcosinus($x)
    {
        $y=rad2deg(acos($x));
        return $y;
    }

/*
* Concrete class to get Sunrise & Sunset time
* Self calculates the values, don't use an internet website
*/

class CalculatedSrssSource extends srssSource
{
    /**
    * use data provider to calculate SunRise, SunSet, Dawn & Twilight
	* override getSRSS in srssSource class
    * @access public
    * @param $date ofDate (day)
    * @param $coords coordinates class of the location
    * @param &$sr ofDate SunRise
    * @param &$ss ofDate SunSet
    * @param &$dawn ofDate Dawn
    * @param &$twilight ofDate Twilight
	* @return boolean (compute ok or not)
	*/
	function getSRSS($date,$coords, &$sr, &$ss, &$dawn, &$twilight)
	{
		$sr = $this->compute('sun','rise',$date,$coords);
		$ss = $this->compute('sun','set',$date,$coords);
		$dawn = $this->compute('day','rise',$date,$coords);
		$twilight = $this->compute('day','set',$date,$coords);
	}

    /**
    * Real sunrise, sunset, twilight or dawn computation
    * @access private
    * @param $Type string (='sun' if sunset/sunrise otherwise ='day' twilight/dawn)
    * @param $riseSet string (='rise' if sunset/sunrise otherwise ='set' twilight/dawn)
    * @param $date ofDate (day)
    * @param $coords coordinates class of the location
	* @return ofDate class (seconds are set null)
	*/
    function compute($type,$riseSet,$date,$coords)
    {
        $year=$date->getYear();
        $month=$date->getMonth();
        $day=$date->getDay();

        if($type=='sun') //SunSet/SunRise
        {
            $zenith=90.5; //Sun
        }else
        {
            $zenith=96; //Day
        }
//	$zenith=102; //Nautique
//	$zenith=108; //Astronomique

        $n1=floor(275*$month/9);
        $n2=floor(($month+9)/12);
        $n3=1+floor(($year-(4*floor($year/4))+2)/3);
        $n=$n1-($n2*$n3)+$day-30;

        $lngHour=$coords->longitude/15;

        if ($riseSet=='rise')
        {
            $t=$n+((6-$lngHour)/24); // Lever
        }
        else
        {
            $t=$n+((18-$lngHour)/24); // Coucher
        }

        $M=(0.9856*$t)-3.289;

        $L=$M+(1.916*sinus($M))+(0.020*sinus(2*$M))+282.634;

        if ($L<0)
        {
            $L=$L+360;
        }
        elseif ($L>360)
        {
            $L=$L-360;
        }

        $RA=arctangente(0.91764*tangente($L));
        if ($RA<0)
        {
            $RA=$RA+360;
        }
        elseif ($RA>360)
        {
            $RA=$RA-360;
        }

        $Lquadrant=(floor($L/90))*90;
        $RAquadrant=(floor($RA/90))*90;

        $RA=$RA+($Lquadrant-$RAquadrant);
        $RA=$RA/15;

        $sinDec=0.39782*sinus($L);
        $cosDec=cosinus(arcsinus($sinDec));

        $cosH=(cosinus($zenith)-($sinDec*sinus($coords->latitude)))/($cosDec*cosinus($coords->latitude));

        if ($riseSet=='rise')
        {
            $H=360-arcosinus($cosH); // Rise
        }
        else
        {
            $H=arcosinus($cosH); // Set
        }

        $H=$H/15;
        $TT=$H+$RA-(0.06571*$t)-6.622;

        $UTC=$TT-$lngHour;

        if ($UTC<0)
        {
            $UTC=$UTC+24;
        }
        elseif ($UTC>24)
        {
            $UTC=$UTC-24;
        }
        $time=new ofDateSpan();
        $time->setFromHours($UTC);
        $date->setClock($time);
        return $date;
    }
}
?>