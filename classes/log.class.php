<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * log.class.php
 *
 * record events in database log table
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
 * @category   log management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: log.class.php,v 1.2.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 19 2005
 */

require_once('./classes/Date.class.php');

class log
{
    /**
    * database access
    * @var DBAccessor object
    */
    var $db;

    /**
    * @var integer person connected Id
    */
    var $booker;

    /**
    * Constructor
	* set latitude and longitude according paramaters
    * @access public
    * @param $db DBAccessor object
    * @param $booker integer person connected Id
	* @return null
	*/
    function log($db,$booker)
    {
        $this->db=$db;
        $this->booker=$booker;
    }

    /**
    * addAddBook
	* add add book log
    * @access public
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
    * @return null
	*/
    function addAddBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
	{
        global $lang;

		$text='NEW,START_DATE='.$startDate->getDate().',END_DATE='.$endDate->getDate().',AIRCRAFT='.$aircraft.',MEMBER='.$pilot.',SLOT_TYPE='.$slotType.',INSTRUCTOR='.$instructor.',FREE_SEATS='.$freeSeats.',COMMENTS='.$comments;
		return $this->add($text);
	}

    /**
    * addRemoveBook
	* add remove book log
    * @access public
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
    * @return null
	*/
    function addRemoveBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
	{
        global $lang;

		$text='REMOVE,OLD_START_DATE='.$startDate->getDate().',OLD_END_DATE='.$endDate->getDate().',OLD_AIRCRAFT='.$aircraft.',OLD_MEMBER='.$pilot.',OLD_SLOT_TYPE='.$slotType.',OLD_INSTRUCTOR='.$instructor.',OLD_FREE_SEATS='.$freeSeats.',OLD_COMMENTS='.$comments;
		return $this->add($text);
	}

    /**
    * addUpdateBook
	* add update book log
    * @access public
    * @param $oldStartDate ofDate object
    * @param $oldEndDate ofDate object
    * @param $oldAircraft integer aircraft Id
    * @param $oldPilot integer pilot Id
    * @param $oldSlotType integer
    * @param $oldInstructor integer instructor Id
    * @param $oldFreeSeats integer free seats number
    * @param $oldComments string comments
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
    * @return null
	*/
    function addUpdateBook($oldStartDate,$oldEndDate,$oldAircraft,$oldPilot,$oldSlotType,$oldInstructor,$oldFreeSeats,$oldComments,$startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
	{
        global $lang;

        $text='CHANGE,OLD_START_DATE='.$oldStartDate->getDate().',OLD_END_DATE='.$oldEndDate->getDate().',OLD_AIRCRAFT='.$oldAircraft.',OLD_MEMBER='.$oldPilot.',OLD_SLOT_TYPE='.$oldSlotType.',OLD_INSTRUCTOR='.$oldInstructor.',OLD_FREE_SEATS='.$oldFreeSeats.',OLD_COMMENTS='.$oldComments;
        if(!$oldStartDate->equals($startDate))
        {
            $text=$text.',START_DATE='.$startDate->getDate();
        }
        if(!$oldEndDate->equals($endDate))
        {
            $text=$text.',END_DATE='.$endDate->getDate();
        }
        if($oldAircraft!=$aircraft)
        {
            $text=$text.',AIRCRAFT='.$aircraft;
        }
        if($oldPilot!=$pilot)
        {
            $text=$text.',MEMBER='.$pilot;
        }
        if($oldInstructor!=$instructor)
        {
            $text=$text.',INSTRUCTOR='.$instructor;
        }
        if($oldFreeSeats!=$freeSeats)
        {
            $text=$text.',FREE_SEATS='.$freeSeats;
        }
        if($oldComments!=$comments)
        {
            $text=$text.',comments='.$comments;
        }

        return $this->add($text);
	}

    /**
    * addAutoUpdate
	* add update book log with automatic aircraft change
    * @access public
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
    * @param $newAircraft integer aircraft Id
    * @return null
	*/
    function addAutoUpdate($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments,$newAircraft)
	{
        global $lang;

		$text='AUTO-UPDATE,AIRCRAFT='.$newAircraft.',OLD_START_DATE='.$startDate->getDate().',OLD_END_DATE='.$endDate->getDate().',OLD_AIRCRAFT='.$aircraft.',OLD_MEMBER='.$pilot.',OLD_SLOT_TYPE='.$slotType.',OLD_INSTRUCTOR='.$instructor.',OLD_FREE_SEATS='.$freeSeats.',OLD_COMMENTS='.$comments;
		return $this->add($text);
	}

	/**
    * add
	* add log
    * @access private
    * @param $text string comments
    * @return null
	*/
    function add($text)
	{
		$now=new ofDate();
		return $this->db->query('insert into logs (DATE,USER,MESSAGE) values (\''.$now->getDate().'\','.$this->booker.',\''.$text.'\')');
	}
}
?>