<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * announcer.class.php
 *
 * send mail to notify something
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
 * @category   mail
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: announcer.class.php,v 1.6.2.3 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat May 21 2005
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    return; // we stop the script now
}

require_once('./classes/permits.php');
require_once('./classes/mail.class.php');

class announcer
{
    /**
    * database access
    * @access private
    * @var DBAccessor object
    */
    var $db;

    /**
    * mail
    * @access private
    * @var ofMail object
    */
    var $mailer;

    /**
    * @var integer person connected Id
    */
    var $booker;

    /**
    * subject
    * @access private
    * @var string
    */
    var $subject;

    /**
    * content
    * @access private
    * @var string
    */
    var $content;

    /**
    * mail sender address used to notify changes by mail
    * @access private
    * @var string
    */
    var $mailSender;

    /**
    * mail sender name (ex: club name)
    * @access private
    * @var string
    */
    var $senderName;

    /**
    * time Zone
    * @access private
    * @var Date_TimeZone object
    */
    var $timeZone;

    /**
    * frenchDisplay
    * @access private
    * @var boolean
    */
    var $frenchDisplay;

    /**
    * htmlTransTable list all translation special html chars
    * @access private
    * @var string
    */
    var $htmlTransTable;

    /**
    * Constructor
	* construct announcer object
    * @access public
    * @param $db DBAccessor object
    * @param $booker integer person connected Id
    * @param $mailSender string mail sender address
    * @param $timeZone Date_TimeZone object
    * @param $frenchDisplay boolean
    * @param $senderName string mail sender name (ex: club name)
	* @return null
	*/
    function announcer($db,$booker,$mailSender,$timeZone,$frenchDisplay,$senderName)
    {
        $this->db=$db;
        $this->booker=$booker;
        $this->mailSender=$mailSender;
        $this->timeZone=$timeZone;
        $this->frenchDisplay=$frenchDisplay;
        $this->senderName=$senderName;
        // we get html special char list and we make a swap for our own use (see sendNotification())
        $this->htmlTransTable=get_html_translation_table(HTML_ENTITIES);
        $this->htmlTransTable=array_flip($this->htmlTransTable);
    }

    /**
    * send add book notification
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
    function addBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        global $lang;

        $this->makeMailer($this->senderName.'. '.$lang['BOOK_CONFIRM_NEW']);
        $this->addNumAsRecipient($instructor);
        $this->addNumAsRecipient($pilot);

        $this->content=$this->makeBookDesc($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
        $this->content.=$lang['BOOK_SENTENCE_CONFIRM'].$lang['BOOK_OPERATOR'].$this->getAuthName($this->booker);
        $this->sendNotification();
    }

    /**
    * send remove book notification
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
    function removeBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        global $lang;

        $this->makeMailer($this->senderName.'. '.$lang['BOOK_CONFIRM_CANCEL']);
        $this->addNumAsRecipient($instructor);
        $this->addNumAsRecipient($pilot);
        
        $this->content=$this->makeBookDesc($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
        $this->content.=$lang['BOOK_SENTENCE_CANCEL'].$lang['BOOK_OPERATOR'].$this->getAuthName($this->booker);
        $this->sendNotification();
    }

    /**
    * send update book notification
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
    function updateBook($oldStartDate,$oldEndDate,$oldAircraft,$oldPilot,$oldSlotType,$oldInstructor,$oldFreeSeats,$oldComments,$startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        global $lang;

        $this->makeMailer($this->senderName.'. '.$lang['BOOK_CONFIRM_MOD']);
        $this->addNumAsRecipient($instructor);
        $this->addNumAsRecipient($pilot);
        $this->addNumAsRecipient($oldInstructor);
        $this->addNumAsRecipient($oldPilot);
        
        $this->content=$this->makeBookDesc($oldStartDate,$oldEndDate,$oldAircraft,$oldPilot,$oldSlotType,$oldInstructor,$oldFreeSeats,$oldComments);
        $this->content.=$lang['BOOK_SENTENCE_MOD'];
        $this->content.=$this->makeBookDesc($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
        $this->content.=$lang['BOOK_OPERATOR'].$this->getAuthName($this->booker);
        $this->sendNotification();
    }

    /**
    * send auto update book notification
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
    function autoUpdateBook($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments,$newAircraft)
    {
        global $lang;

        $this->makeMailer($this->senderName.'. '.$lang['BOOK_CONFIRM_AUTO_UPDATE']);
        $this->addNumAsRecipient($instructor);
        $this->addNumAsRecipient($pilot);
        
        $this->content=$this->makeBookDesc($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments);
        $this->content.=$lang['BOOK_SENTENCE_AUTO_UPDATE'].$this->getAircraftCallsign($newAircraft).$lang['BOOK_AUTO_OPERATOR'].$this->getAuthName($this->booker);
        $this->sendNotification();
    }

    /**
    * construct a book description sentence
    * @access private
    * @param $startDate ofDate object
    * @param $endDate ofDate object
    * @param $aircraft integer aircraft Id
    * @param $pilot integer pilot Id
    * @param $slotType integer
    * @param $instructor integer instructor Id
    * @param $freeSeats integer free seats number
    * @param $comments string comments
    * @return string book description sentence
    */
    function makeBookDesc($startDate,$endDate,$aircraft,$pilot,$slotType,$instructor,$freeSeats,$comments)
    {
        global $lang;

        $sentence=$lang['BOOK_BOOKING'].$startDate->displayDatetime($this->timeZone,$this->frenchDisplay)
                 .$lang['BOOK_UNTIL'].$endDate->displayDatetime($this->timeZone,$this->frenchDisplay)
                 .$lang['BOOK_WHICH_AIRCRAFT'].$this->getAircraftCallsign($aircraft);
        if ($comments!='')
        {
            $sentence.=$lang['BOOK_COMMENTS'].$comments;
        }
        if ($instructor)
        {
            $sentence.=$lang['BOOK_SENTENCE_INST'].$this->getAuthName($instructor);
        }
        return $sentence;
    }
    
    /**
    * Get complete name from an authentication $num
    * @access private
    * @param integer $num
    * @return string name sentence
    */
    function getAuthName($num)
    {
        $row=$this->db->queryAndFetch('select FIRST_NAME, LAST_NAME from authentication where NUM=\''.$num.'\'');
        return ($row->FIRST_NAME.' '.$row->LAST_NAME);
    }

    /**
    * Get callsign from an aicraft $num
    * @access private
    * @param integer $num
    * @return string callsign
    */
    function getAircraftCallsign($num)
    {
        return ($this->db->query_and_fetch_single('select CALLSIGN from aircrafts where NUM=\''.$num.'\''));
    }

    /**
    * Return email address for mail notification or null if no automatic notification
    * @access private
    * @param $num int authentication num
    * @return string or boolean false
    */
    function mailNotify($num)
    {
        $row = $this->db->queryAndFetch('select NOTIFICATION, EMAIL from authentication where NUM=\''.$num.'\'');
        if (($row) and isMailNotify($row->NOTIFICATION) and ($row->EMAIL)) {
            return $row->EMAIL;
        }
        return false;
    }

    /**
    * add one recipient to the list from authenticaion num
    *
    * @access private
    * @param integer $num
    * @return null
    */
    function addNumAsRecipient($num)
    {
        if ($num)
        {
            $mailAddr=$this->mailNotify($num);
            if ($mailAddr)
            {
                $this->mailer->addRecipient($mailAddr);
            }
        }
    }

    /**
    * construct Mailer
	* send notification mail
    * @access private
    * @param $subject string
	* @return null
	*/
    function makeMailer($subject)
    {
        $this->mailer=new ofMail($this->mailSender);
        $this->subject=$subject;
        $this->content='';
    }

    /**
	* send notification mail
    * @access private
	* @return null
	*/
    function sendNotification()
    {
        $this->mailer->send(strtr($this->subject,$this->htmlTransTable),strtr($this->content,$this->htmlTransTable));
    }
}
?>