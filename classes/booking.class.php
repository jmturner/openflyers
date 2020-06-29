<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * booking.class.php
 *
 * book class
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
 * @category   booking management
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: booking.class.php,v 1.20.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./classes/Date.class.php');
require_once('./pool/functions.php');

// book type constants definition
define('BOOK_ALONE',0);
define('BOOK_INST',1);
define('BOOK_MECANIC',2);

class booking
{
    var $id;            // integer indicating the position of this slot in the global books array
    var $rowDB;         // result of database query for this slot
// rowDB has these labels defined : START_DATE, END_DATE, AIRCRAFT_NUM, CALLSIGN, SLOT_TYPE,
// LAST_NAME, FIRST_NAME, MEMBER_NUM, SIGN, INST_LAST_NAME, INST_FIRST_NAME, INST_NUM, FREE_SEATS, COMMENTS
    var $begin;
    var $end;
    var $amplitude;

	var $start_date;
	var $end_date;

    /**
     * Constructor
     *
     * @access public
     * @param $rowDB string fetch result of query
     * @return null
     */
	function booking($rowDB)
	{
		$this->rowDB=$rowDB;
		$this->id=$rowDB->ID;
		$this->begin=new ofDate($rowDB->START_DATE);
		$this->end=new ofDate($rowDB->END_DATE);
        $this->amplitude=new ofDateSpan($this->begin,$this->end);

		$this->start_date=new ofDate($rowDB->START_DATE);
		$this->end_date=new ofDate($rowDB->END_DATE);
	}

	/**
	* @desc Return slot type for this booking
	* @param null
	* @return integer slot type
	*/
	function getSlotType()
	{
		return($this->rowDB->SLOT_TYPE);
	}
	
	/**
	* @desc Return the callsign of the aircraft for this booking
	* @param null
	* @return string callsign
	*/
	function getCallsign()
	{
		return($this->rowDB->CALLSIGN);
	}
	
	/**
	* @desc Return aircraft NUM for this booking
	* @param null
	* @return integer aircraft NUM
	*/
	function getAircraft()
	{
		return($this->rowDB->AIRCRAFT_NUM);
	}
	
	/**
	* @desc Return the complete name of the instructor for this booking
	* @param null
	* @return string instructor name
	*/
	function getInstName()
	{
		return($this->rowDB->INST_FIRST_NAME.'&nbsp;'.$this->rowDB->INST_LAST_NAME);
	}
	
	/**
	* @desc Return instructor NUM for this booking
	* @param null
	* @return integer instructor NUM
	*/
	function getInstructor()
	{
		return($this->rowDB->INST_NUM);
	}
	
	/**
	* @desc Return the complete name of the pilot for this booking
	* @param null
	* @return string pilot name
	*/
	function getPilotName()
	{
		return($this->rowDB->FIRST_NAME.'&nbsp;'.$this->rowDB->LAST_NAME);
	}
	
	/**
	* @desc Return pilot NUM for this booking
	* @param null
	* @return integer pilot NUM
	*/
	function getPilot()
	{
		return($this->rowDB->MEMBER_NUM);
	}
	
	/**
	* @desc Return number of available seats for this booking
	* @param null
	* @return integer free seats number
	*/
	function getFreeSeats()
	{
		return($this->rowDB->FREE_SEATS);
	}
	
	/**
	* @desc Return comments for this booking with automatic addShashes
	* @param null
	* @return string comments
	*/
	function getComments()
	{
		return(addslashes($this->getNoSlashesComments()));
	}

	/**
	* @desc Return comments for this booking without adding slashes
	* @param null
	* @return string comments
	*/
	function getNoSlashesComments()
	{
		return($this->rowDB->COMMENTS);
	}
	
	/**
	* @desc Return the sign of the instructor for this booking
	* @param null
	* @return string instructor sign
	*/
	function getSign()
	{
		return($this->rowDB->SIGN);
	}

	/**
	* @desc Return the label to be displayed in the booking box
	* @param $displayRange integer used to compute the return string max size 
	* @return string label to display
	*/
	function getLabel($displayRange)
	{
        global $lang;
		$rowDB=$this->rowDB;
		if($rowDB->SLOT_TYPE==2)
		{
			return(substr($lang['MECANIC'],0,$displayRange+9));
		}
		elseif(($rowDB->LAST_NAME=='')and($rowDB->FIRST_NAME==''))
		{
			return('???');
		}
		else
		{
			if($rowDB->FIRST_NAME!='')
			{
				$label=substr(html2Text($rowDB->FIRST_NAME),0,1).'. ';
			}
			else
			{
				$label='';
			}
			return(htmlentities($label.substr(html2Text($rowDB->LAST_NAME),0,$displayRange)));
		}
	}
}
?>