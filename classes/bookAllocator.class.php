<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * bookAllocator.class.php
 *
 * manage books : new, update, remove and sort books. Mail notification.
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
 * @category   book management
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: bookAllocator.class.php,v 1.13.2.4 2006/06/16 20:11:58 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    return; // we stop the script now
}

require_once('./classes/Date.class.php');
require_once('./classes/booking.class.php');
require_once('./classes/log.class.php');
require_once('./classes/announcer.class.php');

define('BOOK_REMOVE',0);
define('BOOK_ADD',1);
define('BOOK_UPDATE',2);

class bookAllocator
{
    /**
    * rule descriptor (0:standard book managing->no change, 1:we attribute first avail aircraft of the same type)
    * @var integer
    * @access private
    */
    var $rule;

    /**
    * database access
    * @var DBAccessor object
    * @access private
    */
    var $db;

    /**
    * Notify pool
    * @var announcer object
    * @access private
    */
    var $announcer;

    /**
    * log access
    * @var log object
    * @access private
    */
    var $log;

    /**
    * person connected Id
    * @var integer
    * @access private
    */
    var $booker;

    /**
    * Additionnal time from Now compulsory to change books order
    * @var minSeconds integer seconds
    * @access private
    */
    var $minSeconds;

    /**
    * Constructor
	* set latitude and longitude according paramaters
    * @access public
    * @param $rule integer determine which rule we use
    * @param $db DBAccessor object
    * @param $booker integer person connected Id
    * @param $announcer announcer object
	* @return null
	*/
    function bookAllocator($rule,$db,$booker,$announcer)
    {
        $this->rule=$rule;
        $this->db=$db;
        $this->booker=$booker;
        $this->announcer=$announcer;
        $this->log=new log($db,$booker);
        $this->minSeconds=900;
    }

    /**
    * remove
	* remove a book
    * @access public
    * @param $bookId integer book number in the database
	* @return boolean true=cancel is done
	*/
    function remove($bookId)
    {
        $returnValue=false;
        $result=$this->db->queryAndFetch('select * from booking where ID=\''.$bookId.'\'');
        if ($result)
        {
            $book=new booking($result);

            $result=$this->db->queryAndFree('delete from booking where ID=\''.$bookId.'\'');
            if ($result)
            {
                $returnValue=true;
                $this->log->addRemoveBook($book->start_date,$book->end_date,$book->getAircraft(),$book->getPilot(),$book->getSlotType(),$book->getInstructor(),$book->getFreeSeats(),$book->getComments());
                $this->announcer->removeBook($book->start_date,$book->end_date,$book->getAircraft(),$book->getPilot(),$book->getSlotType(),$book->getInstructor(),$book->getFreeSeats(),$book->getNoSlashesComments());
                $this->upgradeBooks($book->start_date,$book->end_date,$book->getAircraft());
            }
        }
        return $returnValue;
    }

    /**
    * add
	* add a new book
    * @access public
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
	* @return boolean true=cancel is done
	*/
    function add($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        $returnValue=false;

        $aircraft=$this->getBestAircraft($startDate,$endDate,$aircraft,$slotType);
        if ($slotType==BOOK_MECANIC)
        {
            if(!$this->downgradeBooks($startDate,$endDate,$aircraft))
            {
                return (false);
            }
        }

        $result=$this->db->queryAndFree('insert into booking (START_DATE,END_DATE,AIRCRAFT_NUM,MEMBER_NUM,SLOT_TYPE,INST_NUM,FREE_SEATS,COMMENTS)
        values (\''.$startDate->getDate().'\',\''.$endDate->getDate().'\',\''.$aircraft.'\',\''.$pilot.'\',\''.$slotType.'\',\''.$instructor.'\',\''.$freeSeats.'\',\''.$comments.'\')');
        
        if ($result)
        {
            $returnValue=true;
            $this->log->addAddBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
            $this->announcer->addBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,stripslashes($comments));
        }
        return $returnValue;
    }

    /**
    * update
	* update a book
    * @access public
    * @param $bookId integer book number in the database to update
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
	* @return boolean true=cancel is done
	*/
    function update($bookId,$startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        $returnValue=false;
        if ($slotType==BOOK_MECANIC)
        {
            if(!$this->downgradeBooks($startDate,$endDate,$aircraft,$bookId))
            {
                return (false);
            }
        }
        $result=$this->db->queryAndFetch('select * from booking where ID=\''.$bookId.'\'');
        if ($result)
        {
            $book=new booking($result);

            $result=$this->db->query('update booking set START_DATE=\''.$startDate->getDate().'\', END_DATE=\''.$endDate->getDate().'\', 
            AIRCRAFT_NUM=\''.$aircraft.'\', MEMBER_NUM=\''.$pilot.'\', 
            SLOT_TYPE=\''.$slotType.'\', INST_NUM=\''.$instructor.'\', FREE_SEATS=\''.$freeSeats.'\', COMMENTS=\''.$comments.'\' 
            where ID=\''.$bookId.'\'');
            if ($result)
            {
                $returnValue=true;
                $this->log->addUpdateBook($book->start_date,$book->end_date,$book->getAircraft(),$book->getPilot(),$book->getSlotType(),$book->getInstructor(),$book->getFreeSeats(),$book->getComments(),$startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
                $this->announcer->updateBook($book->start_date,$book->end_date,$book->getAircraft(),$book->getPilot(),$book->getSlotType(),$book->getInstructor(),$book->getFreeSeats(),$book->getNoSlashesComments(),$startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,stripslashes($comments));
                $this->upgradeBooks($book->start_date,$book->end_date,$aircraft);
            }
        }
        return $returnValue;
    }

    /**
    * getBestAircraft
	* return best aircraft avail according to the $this->rule rule
    * @access public
    * @param $startDate ofDate begin of the slot
    * @param $endDate ofDate end of the slot
	* @param $aircraft integer aircraft num
    * @param $bookType integer slot type (see constants defined in booking.class.php)
	* @return integer aircraft num
	*/
    function getBestAircraft($startDate,$endDate,$aircraft,$bookType)
    {
        if ($this->rule==1)
        {
            if ($bookType==BOOK_MECANIC)
            {
                return ($aircraft);           
            }
            else
            {
                $result = $this->db->query_and_fetch_single('select aircrafts.NUM from aircrafts as reference
                left join aircrafts on aircrafts.TYPE=reference.TYPE
                left join booking on booking.START_DATE<\''.$endDate->getDate().'\' and booking.END_DATE>\''.$startDate->getDate().'\' and booking.AIRCRAFT_NUM=aircrafts.NUM
                where reference.NUM=\''.$aircraft.'\' and booking.ID is NULL
                order by aircrafts.ORDER_NUM LIMIT 1 OFFSET 0');
                if ($result)
                {
                    return ($result);
                }
                else
                {
                    return ($aircraft);
                }
            }
        }
        else 
        {
            return ($aircraft);
        }
    }

    /**
    * upgradeBooks
	* sort bookings according to the $this->rule rule (used to enhance booking due to remove or update)
    * @access public
    * @param $startDate ofDate begin of the interval to sort
    * @param $endDate ofDate end of the interval to sort
	* @param integer aircraft num to know aircraft type pool to sort
	* @return null
	*/
    function upgradeBooks($startDate,$endDate,$aircraft)
    {
        // Define a time-date equal to now plus a quarter of an hour
        $almostNow=new ofDate();
        $almostNow->addSeconds($this->minSeconds);

        if ($this->rule==1)
        {
            // we take back all aircrafts NUM with the same TYPE as $aircraft
            $this->db->query('select aircrafts.NUM from aircrafts as reference
                              left join aircrafts on aircrafts.TYPE=reference.TYPE
                              where reference.NUM=\''.$aircraft.'\'
                              order by aircrafts.ORDER_NUM');
            // we save the NUMs in the $aircrafts array
            $aircrafts=array();
            while ($row=$this->db->fetch())
            {
                $aircrafts[]=$row->NUM;
            }
	        $this->db->free();
	        // then, we try for each aircraft NUM to level down books to a better aircraft
	        foreach ($aircrafts as $aircraftLevel => $higherAircraft)
	        {
	            // Foreach aircraft NUM, we save books
	            $books=array(); // each array element contain : ID, START_DATE and END_DATE
                $this->db->query('select booking.ID, booking.START_DATE, booking.END_DATE, booking.SLOT_TYPE from booking
                where booking.START_DATE<\''.$endDate->getDate().'\' and booking.END_DATE>\''.$startDate->getDate().'\'
                and booking.AIRCRAFT_NUM=\''.$higherAircraft.'\' order by START_DATE');
                while ($row=$this->db->fetch())
                {
                    $books[]=array($row->ID,$row->START_DATE,$row->END_DATE,$row->SLOT_TYPE);
                }
                $this->db->free();
	            
                // we look for a better aircraft
                foreach ($books as $book)
                {
                    $currentStart=new ofDate($book[1]);
                    $currentEnd=new ofDate($book[2]);
                    if ($currentStart->after($almostNow))
                    {
                        $newAircraft=$this->getBestAircraft($currentStart,$currentEnd,$higherAircraft,$book[3]);
                        if (array_search($newAircraft,$aircrafts)<$aircraftLevel)
                        {
                            // we take back previous book
                            $result=$this->db->queryAndFetch('select * from booking where ID=\''.$book[0].'\'');
                            $bookDesc=new booking($result);
                            // if we found a better aircraft we change the book and set it to the new aircraft
                            $this->db->query('update booking set booking.AIRCRAFT_NUM=\''.$newAircraft.'\'
                            where booking.ID=\''.$book[0].'\'');
                            $this->log->addAutoUpdate($bookDesc->start_date,$bookDesc->end_date,$bookDesc->getAircraft(),$bookDesc->getPilot(),$bookDesc->getSlotType(),$bookDesc->getInstructor(),$bookDesc->getFreeSeats(),$bookDesc->getComments(),$newAircraft);
                            $this->announcer->autoUpdateBook($bookDesc->start_date,$bookDesc->end_date,$bookDesc->getAircraft(),$bookDesc->getPilot(),$bookDesc->getSlotType(),$bookDesc->getInstructor(),$bookDesc->getFreeSeats(),$bookDesc->getNoSlashesComments(),$newAircraft);

                            // we made a space for the actual aircraft level,
                            // so we check if we have to enlarge the interval search
                            if ($currentStart->before($startDate))
                            {
                                $startDate=$currentStart;
                            }
                            if ($currentEnd->after($endDate))
                            {
                                $endDate=$currentEnd;
                            }
                        }
                    }
                }
	        }
        }
    }

    /**
    * downgradeBooks
	* sort bookings according to the $this->rule rule (used to make space due to new MECANIC SLOT or update MECANIC SLOT)
    * @access public
    * @param $startDate ofDate begin of the interval to sort
    * @param $endDate ofDate end of the interval to sort
	* @param $aircraft integer aircraft num to know aircraft type pool to sort
	* @return $oldId integer used in update case to ignore this Id which is the current mecanic slot to update
	* @return boolean false if impossible (there is already a MECANIC SLOT)
	*/
    function downgradeBooks($startDate,$endDate,$aircraft,$oldId = 0)
    {
        if ($this->rule == 1) {
            // we take back all aircrafts NUM with the same TYPE as $aircraft and a superior ORDER_NUM
            $this->db->query('select aircrafts.NUM from aircrafts as reference
                              left join  aircrafts on aircrafts.TYPE=reference.TYPE and reference.ORDER_NUM<aircrafts.ORDER_NUM
                              where reference.NUM=\''.$aircraft.'\'
                              order by aircrafts.ORDER_NUM');
            // we save the NUMs in the $aircrafts array
            $aircrafts      = array();
            $aircrafts[]    = $aircraft; // we set the first entry (entry 0) with the concerned aircraft for the correct start of the double foreach below
            $books          = array(); // 3 dimensions table (aircraft num, book num) and each 3rd array element contain : ID, START_DATE and END_DATE
            while ($row = $this->db->fetch()) {
                $aircrafts[]    = $row->NUM;
                $books[]        = array(); // to set the row empty
            }
            $this->db->free();
            $books[0]           = array();
	        // then, we try for each aircraft NUM to level down books to a better aircraft

            // Get first line
            $this->db->query('select booking.ID, booking.START_DATE, booking.END_DATE, booking.SLOT_TYPE from booking
                              where booking.START_DATE<\''.$endDate->getDate().'\' and booking.END_DATE>\''.$startDate->getDate().'\'
                              and booking.AIRCRAFT_NUM=\''.$aircraft.'\' order by START_DATE');
            while ($row = $this->db->fetch()) {
                if ($row->SLOT_TYPE == BOOK_MECANIC) {
                    // we ignore current MECANIC slot
                    if ($row->ID != $oldId) {
                        // if we are here, then we already got a MECANIC slot, so we can't add this one
                        return (false);
                    }
                }
                else {
                    $books[0][] = array($row->ID,$row->START_DATE,$row->END_DATE,$row->SLOT_TYPE);
                }
            }
            $this->db->free();

            // we assume that when we are here, there is no MECANIC slot on the first line (otherwise we had already escape)
            // $aicraftLevel is the key in the $aircrafts array
            // $higherAircraft is the NUM in the database of current aircraft
            foreach ($aircrafts as $aircraftLevel => $higherAircraft) {
                if ($aircraftLevel > 0) {
                    $books[$aircraftLevel] = array(); // to set the row empty

                    foreach ($books[$aircraftLevel-1] as $idUpperLine => $book) {
                        // Foreach aircraft NUM, we save books
                        $this->db->query('select booking.ID, booking.START_DATE, booking.END_DATE, booking.SLOT_TYPE from booking
                        where booking.START_DATE<\''.$book[2].'\' and booking.END_DATE>\''.$book[1].'\'
                        and booking.AIRCRAFT_NUM=\''.$higherAircraft.'\' order by START_DATE');
                        while ($row=$this->db->fetch()) {
                            if ($row->SLOT_TYPE==BOOK_MECANIC) {
	                            // if we got an intersection between the book trying to be at this $aircraftLevel and a MECANIC SLOT already at this level
	                            // so we needn't to move others slots which ought to be altered by this one : we don't memorize them
                                $this->db->free();
                                $row->ID            = $book[0];
                                $row->START_DATE    = $book[1];
                                $row->END_DATE      = $book[2];
                                $row->SLOT_TYPE     = $book[3];
                                for ($i = $idUpperLine; $i < (sizeof($books[$aircraftLevel-1])-1); $i++) {
                                    $books[$aircraftLevel-1][$i]=$books[$aircraftLevel-1][$i+1];
                                }
                                unset ($books[$aircraftLevel-1][$i]);
                            }
                            $i      = 0;
                            $found  = false;
                            while (($i<sizeof($books[$aircraftLevel]))and(!$found)) {
                                if ($books[$aircraftLevel][$i][0]==$row->ID) {
                                    $found = true;
                                }
                                $i++;
                            }
                            if (!$found) {
                                $books[$aircraftLevel][] = array($row->ID,$row->START_DATE,$row->END_DATE,$row->SLOT_TYPE);
                            }
                        }
                        $this->db->free();
                    }
                }
            }

            // we drop last $books line
            foreach ($books[sizeof($aircrafts)-1] as $book) {
                $this->remove($book[0]);
            }

            for ($aircraftLevel = sizeof($aircrafts)-2; $aircraftLevel >= 0; $aircraftLevel--) {
                $newAircraft = $aircrafts[$aircraftLevel+1];
                foreach ($books[$aircraftLevel] as $book) {
                    // we take back previous book
                    $result     = $this->db->queryAndFetch('select * from booking where ID=\''.$book[0].'\'');
                    $bookDesc   = new booking($result);
                    $this->db->query('update booking set booking.AIRCRAFT_NUM=\''.$newAircraft.'\'
                                      where booking.ID=\''.$book[0].'\'');
                    $this->log->addAutoUpdate($bookDesc->start_date,$bookDesc->end_date,$bookDesc->getAircraft(),$bookDesc->getPilot(),$bookDesc->getSlotType(),$bookDesc->getInstructor(),$bookDesc->getFreeSeats(),$bookDesc->getComments(),$newAircraft);
                    $this->announcer->autoUpdateBook($bookDesc->start_date,$bookDesc->end_date,$bookDesc->getAircraft(),$bookDesc->getPilot(),$bookDesc->getSlotType(),$bookDesc->getInstructor(),$bookDesc->getFreeSeats(),$bookDesc->getNoSlashesComments(),$newAircraft);
                }
            }
        }
        return (true);
    }
}
?>