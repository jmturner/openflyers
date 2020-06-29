<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * bookView.class.php
 *
 * 2 classes managing what should be displayed on the book
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
 * @category   html engine
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: bookView.class.php,v 1.14.2.11 2007/04/29 19:01:35 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// The goal of these objects is to display booking on an HTML page.
// A pageView object is composed of lineView objects
// A lineView object is composed of several parts :
// - an header showing either the day, the aircraft or the instructor for which the books are attached
// - slot objects
// - gaps between 2 slots
// A gap is divided in several free slots of quarter of an hour each.

// When possible, unit used is quarter of hour : 1 = 15 minutes

// Constants used to identify aircrafts and instructors lines and used in JavaScript posts
define('AIRCRAFTS', 'A');
define('ONE_AIRCRAFT', 'a');
define('INSTRUCTORS', 'I');
define('ONE_INST', 'i');

require_once('./classes/srssManager.class.php');
require_once('./classes/booking.class.php');
require_once('./classes/serie.class.php');

/*
* lineView object
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*/
class lineView
{
    // public
    var $dstChange;     // boolean (true = dst change between $begin and $end)

    // private
    var $slots=array(); // array of slot object. This array contains all the slots of the line
    var $slotSize;      // integer indicating the number of slots recorded in $slots array

    var $userNum;       // integer Authentication Num used to know if slot is for ownself or not
    var $tz;            // Date_TimeZone
    var $frenchDisplay; // boolean specifying how displaying dates
    var $noCallsignDisplay; // boolean specifying if we have not to display callsign in the aircraft line
    var $bookDateLimit; // ofDate specifiying last authorized day to book (false if no limit)

    var $beginDate;     // ofDate object begin datetime of the line
    var $begin;         // integer begin time of the line in quarter of hours
    var $amplitude;     // integer amplitude time of the line in quarter of hours
    var $end;           // integer end time of the line in quarter of hours
    var $ephemeris;     // ephemeris object containing sunrise, sunset, aeroDay and aeroNight

    var $beginOffset;   // integer offset in minutes between begin local and UTC according to the timezone
    var $endOffset;     // integer offset in minutes between end local and UTC according to the timezone
    var $type;          // const ONE_AIRCRAFT, AIRCRAFTS, ONE_INST or INSTRUCTORS defining the type of this line
    var $id;            // integer index value for entry in the global $aircrafts_viewed or $instructors_viewed arrays
    var $dayOffset;     // integer day offset from the first day displayed in the page

    /**
     * Constructor
     *
     * Creates a new lineView
     *
     * @access public
     * @param $begin ofDate start datetime of the line
     * @param $amplitude ofDateSpan amplitude time of the line
     * @param $tz Date_TimeZone of the user
     * @param $type const ONE_AIRCRAFT, AIRCRAFTS, ONE_INST or INSTRUCTORS defining the type of this line
     * @param $id integer index value for entry in the global $aircrafts_viewed or $instructors_viewed arrays
     * @param $dayOffset integer day offset from the first day displayed in the page
     * @param $userNum
     * @param $frenchDisplay boolean specifying how displaying dates
     * @param $noCallsignDisplay boolean specifying if we have not to display callsign in the aircraft line
     * @param $bookDateLimit ofDate specifiying last authorized day to book (false if no limit)
     * @return null
     */
    function lineView($begin,$amplitude,$tz,$type,$id,$dayOffset,$userNum,$frenchDisplay,$noCallsignDisplay = false, $bookDateLimit=false)
    {
        $this->beginDate=$begin;
        $this->tz=$tz;
        $this->type=$type;
        $this->id=$id;
        $this->dayOffset=$dayOffset;
        $this->userNum=$userNum;
        $this->frenchDisplay=$frenchDisplay;
        $this->noCallsignDisplay = $noCallsignDisplay;
        $this->bookDateLimit        = $bookDateLimit;

        $this->slotSize=0;

        $this->begin=$begin->getClockQuarter();
        $this->amplitude=$amplitude->getQuarter();
        $this->end=$this->begin+$this->amplitude;

        $this->beginOffset=$tz->getOffset($begin)/60000;
        $begin->addSpan($amplitude);
        $this->endOffset=$tz->getOffset($begin)/60000;
        if($this->beginOffset==$this->endOffset)
        {
            $this->dstChange=false;
        }
        else
        {
            $this->dstChange=true;
        }
    }

    /**
     * Set Ephemeris according to the parameter
     *
     * @access public
     * @param $ephemeris ephemeris object
     * @return null
     */
    function setEphemeris($ephemeris)
    {
        $this->ephemeris=$ephemeris;
    }

    /**
     * add a book slot in the line
     *
     * @access public
     * @param $book current booking object
     * @return null
     */
    function addBook($book)
    {
        $amplitude=$book->amplitude->getQuarter();
        if ($this->beginDate->before($book->begin))
        {
            $shortBegin=true;
            $begin=$book->begin->getClockQuarter();
            if ($begin<$this->begin)
            {
                $begin=$begin+96;
                $shortBegin=false;
            }
        }
        else
        {
            $begin=$this->begin;
            $beforeAmp=new ofDateSpan($this->beginDate,$book->begin);
            $amplitude=$amplitude-$beforeAmp->getQuarter();
            $shortBegin=false;
        }
        if (($amplitude+$begin)<($this->end))
        {
            $shortEnd=true;
        }
        else
        {
            $amplitude=$this->end-$begin;
            $shortEnd=false;
        }
        if (($this->type==INSTRUCTORS)or($this->type==ONE_INST))
        {
            $onlyInstructors=true;
        }
        else
        {
            $onlyInstructors=false;
        }
        $this->slots[$this->slotSize]=new slot($book,$book->id,$begin,$amplitude,$shortBegin,$shortEnd,$onlyInstructors,$this->userNum,$this->tz,$this->frenchDisplay);
        $this->slotSize++;
    }

    /**
     * Display a minimal free unit size
     *
     * @access private
     * @param $current integer time to be displayed (in quarter of hours)
     * @param $gap integer number of continuous free slots
     * @return null
     */
    function displayFreeSlot($current,$gap=1)
    {
        $type=$this->type;
        $oldGap=$gap;
        while ($gap>0) {
            $currentDate=$this->beginDate;
            $currentDate->setHour(floor($current/4));
            $currentDate->setMinute(15*($current%4));
            $avails[$gap] = $currentDate->before($this->bookDateLimit) ? true : false;
            if(($type==ONE_AIRCRAFT)or($type==AIRCRAFTS)) {
                $JStype=AIRCRAFTS;
                global $aircrafts_viewed;
                $avails[$gap]=$aircrafts_viewed[$this->id]->non_bookable ? false:$avails[$gap];
                $generics[$gap]=$aircrafts_viewed[$this->id]->NUM;
            }
            else {
                $JStype=INSTRUCTORS;
                global $instructors_viewed;
                global $instAvailTable;
                $generics[$gap]=$instructors_viewed[$this->id]->NUM;
                if($instAvailTable[$this->id]->isAvailable($currentDate))
                {
                    $avails[$gap]=true;
                }
                else
                {
                    $avails[$gap]=false;
                }
            }
            if ($avails[$gap]) {
            	$light[$gap]=$this->ephemeris->whichLight($current);
            }
            if (($avails[$oldGap]!=$avails[$gap])or(($avails[$gap])and($light[$gap]!=$light[$oldGap]))) {
                echo ('<td');
                if ($avails[$oldGap]) {
                    echo (' onclick="b(\''.$JStype.'\','.$generics[$oldGap].','.($current-1).','.$this->dayOffset.')" class="'.$light[$oldGap].'"');
                }
                else 
                {
                    echo (' class="s"');
                }
                if (($oldGap-$gap)>1) {
                    echo (' colspan="'.($oldGap-$gap).'"');
                }
                $oldGap=$gap;
                echo ('/>');
            }
            if ($gap==1) {
                echo ('<td');
                if ($avails[$oldGap]) {
                    echo (' onclick="b(\''.$JStype.'\','.$generics[$oldGap].','.$current.','.$this->dayOffset.')" class="'.$light[$oldGap].'"');
                }
                else 
                {
                    echo (' class="s"');
                }
                if (($oldGap-$gap)>=1) {
                    echo (' colspan="'.($oldGap-$gap+1).'"');
                }
                echo ('/>');
            }
            $gap=$gap-1;
            $current=$current+1;
        }
    }

    /**
     * Display a whole gap of free slots
     *
     * @access private
     * @param $begin integer begin time (in quarter of hours) to be displayed
     * @param $end integer end time (in quarter of hours) to be displayed
     * @return null
     */
    function displayGap($begin,$end)
    {
//        for($current=$begin;$current<$end;$current++)
//        {
        if ($begin<$end)
        {
        	$this->displayFreeSlot($begin,$end-$begin);
        }
//        }
    }

    /**
     * Display non-bookable slots at the begin of the line
     *
     * @access private
     * @param null
     * @return null
     */
    function addBlankBefore()
    {
        switch($this->begin%4)
        {
            case 1:
                $max=3;
                break;
            case 2:
                $max=0;
                break;
            case 3:
                $max=1;
                break;
            case 0:
                $max=2;
                break;
        }
        for($i=0;$i<$max;$i++)
        {
            ?><td class="s"/><?php
        }
    }

    /**
     * Display non-bookable slots at the end of the line
     *
     * @access private
     * @param null
     * @return null
     */
    function addBlankAfter()
    {
        switch($this->end%4)
        {
            case 1:
                $max=1;
                break;
            case 2:
                $max=0;
                break;
            case 3:
                $max=3;
                break;
            case 0:
                $max=2;
                break;
        }
        for($i=0;$i<$max;$i++)
        {
            ?><td class="s"/><?php
        }
    }

    /**
     * Display the header at the begin of the line
     *
     * @access private
     * @param null
     * @return null
     */
    function displayHeader()
    {
        switch ($this->type)
        {
        case AIRCRAFTS:
            global $aircrafts_popup;
            global $aircrafts_viewed;
            ?><th class="thHeight" onclick="submit_menu('1','<?php echo($this->id);?>')"<?php
            if ($aircrafts_popup[$this->id]->get_popup()!='')
            {
                echo(' title="'.$aircrafts_popup[$this->id]->get_popup().'"');
            }
            ?>><?php
            if (!$this->noCallsignDisplay) {
                echo (stripslashes($aircrafts_viewed[$this->id]->CALLSIGN).'<br />');
            }
            echo(stripslashes($aircrafts_viewed[$this->id]->TYPE));
            break;
        case INSTRUCTORS:
            global $instructors_popup;
            global $instructors_viewed;
            ?><th class="instHeight" onclick="submit_menu('2','<?php echo($this->id);?>')"<?php
            if($instructors_popup[$this->id]->get_popup()!='')
            {
                echo(' title="'.$instructors_popup[$this->id]->get_popup().'"');
            }
            ?>><?php
            echo(stripslashes($instructors_popup[$this->id]->getLabel()));
            break;
        default:
            ?><th class="thHeight" onclick="submit_menu('0','0',<?php
            echo('\''.$this->beginDate->getTS().'\')">'.$this->beginDate->getWeekDay().'<br />'.$this->beginDate->getDayMonth());
            break;
        }
        ?></th><?php
    }

    /**
     * Display the entire line
     *
     * @access public
     * @param null
     * @return null
     */
    function display()
    {
?><tr class="line"><?php
        $this->displayHeader();
        $this->addBlankBefore();
        $slotIndex=0;
        $current=$this->begin;
        while ($current < $this->end) {
            if ($slotIndex == sizeof($this->slots)) {
                $this->displayGap($current,$this->end);
                $current=$this->end;
            }
            else {
                $this->displayGap($current,$this->slots[$slotIndex]->begin);
                $this->slots[$slotIndex]->display();
                $current=$this->slots[$slotIndex]->end;
                $slotIndex++;
            }
        }
        $this->addBlankAfter();
?></tr><?php
    }
}

/*
* slot object
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*/

class slot
{
    var $booking;           // booking object
    var $id;                // integer index of booking object in the books array
    var $begin;             // integer begin time of the slot in quarter of hours
    var $amplitude;         // integer amplitude time of the slot in quarter of hours
    var $end;               // integer end time of the slot in quarter of hours
    var $shortBegin;        // boolean true=display only the start time otherwise display start date+time
    var $shortEnd;          // boolean true=display only the end time otherwise display end date+time
    var $onlyInstructors;   // boolean specifying if we manage only instructors

    var $userNum;           // integer Authentication Num used to know if slot is for ownself or not
    var $tz;                // Date_TimeZone
    var $frenchDisplay;     // boolean specifying how displaying dates

    /**
     * Constructor
     *
     * Creates a new slot
     *
     * @access public
     * @param $booking booking object
     * @param $id integer index value for entry in the global $aircrafts_viewed or $instructors_viewed arrays
     * @param $begin ofDate start time of the line
     * @param $amplitude ofDateSpan amplitude time of the line
     * @param $shortBegin boolean true=display only the start time otherwise display start date+time
     * @param $shortEnd boolean true=display only the end time otherwise display end date+time
     * @param $onlyInstructors boolean specifying if we manage only instructors
     * @return null
     */
    function slot($booking,$id,$begin,$amplitude,$shortBegin,$shortEnd,$onlyInstructors,$userNum,$tz,$frenchDisplay)
    {
        $this->booking=$booking;
        $this->id=$id;
        $this->begin=$begin;
        $this->amplitude=$amplitude;
        $this->end=$this->begin+$this->amplitude;
        $this->shortBegin=$shortBegin;
        $this->shortEnd=$shortEnd;
        $this->onlyInstructors=$onlyInstructors;
        $this->userNum=$userNum;
        $this->tz=$tz;
        $this->frenchDisplay=$frenchDisplay;
    }

    /**
     * Display the slot
     * TODO : manage differents users allowed or not to change a book
     *
     * @access public
     * @param null
     * @return null
     */
    function display()
    {
        global $lang;

        $booking=$this->booking;

        // If this slot is one slot of the current connected member
        if(($booking->getPilot()==$this->userNum)OR($booking->getInstructor()==$this->userNum))
        {
            $backgroundType=array('l','m','q');
        }
        else
        {
            $backgroundType=array('o','p','q');
        }

        $popupText='';
        $title='';	// do not set " for PILOT class due to to javascript args
        // Type of display according to the type of slot
        switch($booking->getSlotType())
        {
            case BOOK_INST:		// do not set " for INSTRUCTOR class due to to javascript args
                $popupText=$popupText.$lang['INSTRUCTOR'].' : '.addslashes($booking->getInstName());
                $title=$title.addslashes($booking->getCallsign()).' '.$lang['BOOKVIEW_LEARNING_WITH'].' '.addslashes($booking->getPilotName());
                break;
            case BOOK_MECANIC:
                $title=$title.addslashes($booking->getCallsign()).' '.$lang['BOOKVIEW_UNAVAIL'];
                break;
            default:
                if($booking->getFreeSeats()!=0)
                {
                    $popupText=$popupText.$booking->getFreeSeats().' '.$lang['BOOKVIEW_FREE_SEATS'].' ';
                }
                $title=$title.$booking->getCallsign().' '.$lang['BOOKVIEW_PILOTED_BY'].' '.addslashes($booking->getPilotName());
                break;
        }
        if($this->shortBegin)
        {
            $startText=$booking->begin->displayTime($this->tz);
        }
        else
        {
            $startText=$booking->begin->displayDatetime($this->tz,$this->frenchDisplay);
        }
        if($this->shortEnd)
        {
            $endText=$booking->end->displayTime($this->tz);
        }
        else
        {
            $endText=$booking->end->displayDatetime($this->tz,$this->frenchDisplay);
        }
        $comments=$booking->getComments();
        ?><td colspan="<?php echo($this->amplitude);?>" class="<?php
        echo($backgroundType[$booking->getSlotType()]);?>" onmouseover="d(<?php echo('\''.$popupText.'\',\''.$lang['BEGIN'].' : '.$startText.'\',\''.$lang['END'].' : '.$endText.'\',\''.$title.'\'');
        if($comments!='')
        {
            echo(',\''.$comments.'\'');
        }
        ?>);" onmouseout="return nd();" onclick="modify('<?php echo($this->id);?>');"><?php
        if(($booking->getSlotType()==BOOK_INST)AND(!$this->onlyInstructors))
        {
            ?><div class="INSTRUCTOR"><?php echo($booking->getSign());?><br /></div><?php
        }
        ?><div class="PILOT"><?php echo($booking->getLabel(floor(($this->amplitude))-1));?></div></td><?php
    }
}

/*
* hoursRule object
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*/
class hoursRule
{
// private
    var $ExtendedAmplitude; // integer amplitude hours to display
    var $tz;                // Date_TimeZone

    /**
     * Constructor
     *
     * Creates a new lineView
     *
     * @access public
     * @param $interval interval object
     * @param $tz Date_TimeZone object
     * @return null
     */
    function hoursRule($interval,$tz=0)
    {
        $this->tz=$tz;
        $beginTime=$interval->begin;
        $beginHour=$beginTime->hour;
        $beginTime->minute=0;
        $beginTime->add($interval->amplitude);
        $this->ExtendedAmplitude=floor($beginTime->toHours())-$beginHour+ceil(($beginTime->minute-30)/60);
        if ($this->ExtendedAmplitude==0)
        {
            $this->ExtendedAmplitude=24;
        }
    }

    /**
     * Display the rule
     *
     * @access public
     * @param $begin ofDate begin of hours and date to display
     * @param $showHours boolean
     * @return null
     */
    function display($begin,$showHours)
    {
        global $theadDisplayed;
        if ($showHours)
        {
            $currentTime=$begin;
            if ($currentTime->minute>=30)
            {
                $currentTime->addSeconds(3600);
            }
            if (!$theadDisplayed)
            {
                ?><thead><tr class="tdWidth"><th /><?php
                for ($j=0;$j<=(($this->ExtendedAmplitude*4)+3);$j++)
                {
                    echo('<td />');
                }
                ?></tr><?php
            }
            ?><tr><th class="HHeight">H</th><?php
            for ($j=0;$j<=$this->ExtendedAmplitude;$j++)
            {
                $tempBegin=$currentTime;
                echo('<td colspan="4" class="hoursHeight">'.sprintf("%'02d",$tempBegin->getTZHour($this->tz)).'</td>');
                $currentTime->addSeconds(3600);
            }
            $currentTime=$begin;
            if ($currentTime->minute>=30)
            {
                $currentTime->addSeconds(3600);
            }
            ?></tr><?php
            if (!$theadDisplayed)
            {
                ?></thead><?php
            }
            $this->displayRule();
            $theadDisplayed=true;
        }
        else 
        {
            $this->displayRule();
        }
    }

    /**
     * Display the rule (verticals lines)
     * @access private
     * @param null
     * @return null
     */
    function displayRule()
    {
?><tr class="rule"><th /><td colspan="2" class="s"/><?php
		for($j=0;$j<$this->ExtendedAmplitude;$j++)
		{
?><td colspan="4"/><?php
        }
?><td colspan="2"/></tr><?php
    }
}


/*
* pageView object
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*
*/
class pageView
{
// private variables
    var $lines=array();     // array[day][i] of lineView object where day is a day index and i is an index for 1 day
    var $linesPerDayQty;    // integer indicates the number of lines

// displaying variables should be changed with XML use
    var $genericRule;       // hoursRule class
    var $icao;              // string icao code

    var $tz;                // Date_TimeZone
    var $frenchDisplay;     // boolean specifying how displaying dates
    var $noCallsignDisplay; // boolean specifying if we have not to display callsign in the aircraft line
    var $bookDateLimit;     // ofDate specifiying last authorized day to book (false if no limit)
    var $userNum;           // integer Authentication Num used to know if slot is for ownself or not

    var $type;              // const AIRCRAFTS, INSTRUCTORS or ONE_INST defining the type of this line

    var $menu;              // integer menu
    var $id;                // integer index value for entry in the global $aircrafts_viewed or $instructors_viewed arrays

    var $begin;             // ofDate begin date of the page
    var $end;               // ofDate end date of the page
    var $interval;          // interval object defining time displayed for every day
    var $days;              // integer number of days (ie: number of lines to display the "days" (may be overlapping 2 days)

    /**
     * Constructor
     *
     * Creates a new pageView
     *
     * @access public
     * @param $icao string icao code
     * @param $tz Date_TimeZone object
     * @param $begin ofDate begin date of the display (we assume that time is correctly set, ie: equal to $interval->begin)
     * @param $end ofDate end date of the display (we assume that time is correctly set, ie: equal to $interval->end)
     * @param $interval interval object defining the begin and end time displayed for each day
     * @param $type const ONE_AIRCRAFT, AIRCRAFTS, ONE_INST or INSTRUCTORS defining the type of this line
     * @param $userNum integer Authentication Num used to know if slot is for ownself or not
     * @param $frenchDisplay boolean specifying how displaying dates
     * @param $noCallsignDisplay boolean specifying if we have not to display callsign in the aircraft line
     * @param $bookDateLimit ofDate specifiying last authorized day to book (false if no limit)
     * @param $id integer index value for entry in the global $aircrafts_viewed or $instructors_viewed arrays used only if $type is ONE_SOMETHING
     * @return null
     */
    function pageView($icao,$tz,$begin,$end,$interval,$type,$userNum,$frenchDisplay,$noCallsignDisplay=false,$bookDateLimit=false,$id=0)
    {
        $this->icao=$icao;
        $this->tz=$tz;
        $this->begin=$begin;
        $this->end=$end;
        $this->interval=$interval;
        $this->type=$type;
        $this->userNum=$userNum;
        $this->frenchDisplay=$frenchDisplay;
        $this->noCallsignDisplay=$noCallsignDisplay;
        $this->bookDateLimit        = $bookDateLimit;
        $this->id=$id;

        $amplitudeDate=new ofDateSpan($end,$begin);
        $amplitudeDate->subtract($this->interval->amplitude);
        $this->days=1+$amplitudeDate->day;

        $this->genericRule=new hoursRule($this->interval,$this->tz);

        $this->SetLines();
    }

    /**
     * Creates all the lines according the $
     *
     * @access public
     * @param null
     * @return null
     */
    function SetLines()
    {
        switch ($this->type)
        {
        case ONE_AIRCRAFT :
            $this->linesPerDayQty=1;
            break;
        case AIRCRAFTS :
            global $aircrafts_viewed;
            $this->linesPerDayQty=sizeof($aircrafts_viewed);
            break;
        case INSTRUCTORS :
            global $instructors_viewed;
            $this->linesPerDayQty=sizeof($instructors_viewed);
            break;
        case ONE_INST :
            $this->linesPerDayQty=1;
            break;
        }
        $begin=$this->begin;
        for($i=0;$i<$this->days;$i++)
        {
            $ephemeris=new ephemeris($begin,$this->icao);
            for($j=0;$j<$this->linesPerDayQty;$j++)
            {
                if (($this->type==ONE_AIRCRAFT)or($this->type==ONE_INST)) {
                	$id=$this->id;
                }
                else {
                	$id=$j;
                }
                $this->lines[$i][$j]=new lineView($begin,$this->interval->amplitude,$this->tz,$this->type,$id,$i,$this->userNum,$this->frenchDisplay,$this->noCallsignDisplay,$this->bookDateLimit);
                $this->lines[$i][$j]->setEphemeris($ephemeris);
            }
            $begin->addSeconds(86400);  // 86400 = 24*60*60
        }
    }

    /**
     * dispatch a book on lines as necessary (according to the days)
     *
     * @access private
     * @param $book current booking object to dispatch
     * @param $idLine integer line number where we put the book (several copies : one for each day)
     * @return null
     */
    function dispatchBook($book,$idLine)
    {
        $idDay=0;
        if($this->begin->before($book->begin))
        {
            $gap=new ofDateSpan($book->begin,$this->begin);
            $idDay=floor($gap->toDays());
            if($idDay>=$this->days)
            {
                $idDay=$this->days-1;
            }
        }

        $begin=$this->begin;
        $begin->addSeconds(86400*$idDay);
        $end=$begin;
        $end->addSpan($this->interval->amplitude);
        for($i=$idDay;($i<$this->days)and($begin->before($book->end));$i++)
        {
            if ($end->after($book->begin))
            {
                $this->lines[$i][$idLine]->addBook($book);
            }
            $begin->addSeconds(86400);  // 86400 = 24*60*60
            $end->addSeconds(86400);
        }
    }


    /**
     * add a book slot in the page
     *
     * @access public
     * @param $book current booking object to save
     * @return null
     */
    function addBook($book)
    {
        // we search the line num according the aircraft or instructor NUM
        switch ($this->type) {
        case AIRCRAFTS:
            global $aircrafts_viewed;
            $idLine=-1;
            for ($i=0;$i<sizeof($aircrafts_viewed);$i++) {
                if ($aircrafts_viewed[$i]->NUM==$book->getAircraft()) {
                    $idLine=$i;
                }
            }
            if ($idLine<>-1) {
                $this->dispatchBook($book,$idLine);
            }
            break;
        case INSTRUCTORS:
            global $instNum;
			if ($book->getInstructor()) {
                if (isset($instNum['\''.$book->getInstructor().'\''])) {
                    $this->dispatchBook($book,$instNum['\''.$book->getInstructor().'\'']);
                }
                else {
                    $this->dispatchBook($book,0);
                }
            }
            if ((isInstructor($book->getPilot())) and (isset($instNum['\''.$book->getPilot().'\'']))) {
                $this->dispatchBook($book, $instNum['\''.$book->getPilot().'\'']);
            }
            break;
        default:
            $this->dispatchBook($book,0);
            break;
        }
    }

    /**
     * Set all the books according the $books array
     *
     * @access public
     * @param $books array of booking objects
     * @return null
     */
    function SetBooks($books)
    {
        for($i=0;$i<sizeof($books);$i++)
        {
            $this->addBook($books[$i]);
        }
    }

    /**
     * Display the page with all the lines
     *
     * @access public
     * @param $linesShown array of integer specifying which line must be shown
     * @return null
     */
    function display($linesShown=null)
    {
        $begin=$this->begin;
        for ($i=0;$i<$this->days;$i++)
        {
            if($linesShown==null)
            {
                $j=0;
            }
            else
            {
                $k=0;
                if($k<sizeof($linesShown))
                {
                    $j=$linesShown[$k];
                }
                else
                {
                    $j=$this->linesPerDayQty;
                }
            }
            $bestRulePos = 10;
            if ($this->linesPerDayQty > 10) {
                $bestRulePos = ceil($this->linesPerDayQty/ceil($this->linesPerDayQty/10));
            }
            
            while($j<$this->linesPerDayQty)
            {
                if (($i==0)and((fmod($j, $bestRulePos)==0)or(($this->lines[$i][$j]->dstChange)and($j==0))))
                {
                    $showHours=true;
                }
                else
                {
                    $showHours=false;
                }
                $this->genericRule->display($begin,$showHours);
                $this->lines[$i][$j]->display();
                if($linesShown==null)
                {
                    $j++;
                }
                else
                {
                    $k++;
                    if($k<sizeof($linesShown))
                    {
                        $j=$linesShown[$k];
                    }
                    else
                    {
                        $j=$this->linesPerDayQty;
                    }
                }
            }
            $begin->addSeconds(86400);   // 86400 = 24 hours * 60 minutes * 60 seconds
        }
    }
}

class completePage
{
    var $theadDisplayed;
    var $pages=array();
    var $instViewed=array();

    /**
     * Constructor
     *
     * Creates a complete page (several pageViews)
     */
    function completePage()
    {
        $this->theadDisplayed=false;
        $this->pagesSize=0;
    }

    function addPage($pageNum,$icao,$tz,$begin,$end,$interval,$type,$userNum,$frenchDisplay,$noCallsignDisplay=false,$maxBookDateLimitation,$id=0)
    {
        $this->pages[$pageNum]=new pageView($icao,$tz,$begin,$end,$interval,$type,$userNum,$frenchDisplay,$noCallsignDisplay,$maxBookDateLimitation,$id);
    }

    function setBooks($id,$books)
    {
        $this->pages[$id]->setBooks($books);
    }
    
    function setInstViewed($instList)
    {
        $this->instViewed=$instList;
    }
    
    function display($allThings)
    {
        ?><table class="BOOKVIEW"><?php

        if (isset($this->pages[0]))
        {
            $this->pages[0]->display();
        }
        if ((isset($this->pages[1]))and($allThings))
        {
            $this->pages[1]->display($this->instViewed);
        }
        ?></table><?php
    }
}
?>