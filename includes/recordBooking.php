<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * recordBooking.php
 *
 * check and record in database a booking request
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
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: recordBooking.php,v 1.27.2.10 2006/09/18 12:46:29 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 10 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// Variables that should be posted :
// $member : num of the member of the slot     if is not an aircraft immobilisation
// $member_num : num of the person making the reservation
// $aircraft
// $instructor if there is an instructor
// $free_seats
// $start_year,month,day,hour,minute and the same for $end

require_once('./classes/inst_availability.class.php');
require_once('./classes/qualification.class.php');
require_once('./classes/bookAllocator.class.php');
require_once('./includes/redirect.php');
require_once('./displayClasses/requestForm.class.php');
require_once('./classes/announcer.class.php');

function saveVar()
{
    global $userSession;
    global $bookLogin;
    global $bookPassword;
    global $member;
    global $member_num;
    global $free_seats;
    global $comments;
    global $instructor;
    global $slot_type;
    global $aircraft;
    global $start_date;
    global $end_date;
    global $bookId;
    
    $userSession->add('bookLogin');
    $userSession->add('bookPassword');
    $userSession->add('member');
    $userSession->add('member_num');
    $userSession->add('free_seats');
    $userSession->add('comments');
    $userSession->add('instructor');
    $userSession->add('slot_type');
    $userSession->add('aircraft');
    $userSession->add('start_date');
    $userSession->add('end_date');
    $userSession->add('bookId');
}

function isNonBookableAircraft($aircraftId)
{
    global $database;
    $result = $database->query_and_fetch_single('select non_bookable from aircrafts where num=\''.$aircraftId.'\'');
    if ($result) {
        return $result;
    }
    else {
        return false;
    }
}

////////////////// local functions managing displays /////////////////////////////////

/**
* Test if there is already a book in the range defined by start_date and end_date
* $field define which part of the bookings have to be consider and $value the value of the field
* example : $field='AIRCRAFT_NUM' and $value=the number of the aircraft to be consider
* return has global value $book if there is at least one book interfering
* @access private
* @param $field should be MEMBER_NUM, AIRCRAFT_NUM or INST_NUM
* @param $value value that the field should be equal to
* @return null but global $ok_flag value affected and global $book set if there is another book
*/
function isAlreadyBook($field,$value)
{
    global $database;
    global $sub_menu;
    global $ok_flag;
    global $start_date;
    global $end_date;
    global $book;
    global $bookId;

    $database->query('select booking.*, aircrafts.CALLSIGN, aircrafts.ORDER_NUM, instructors.SIGN, real_auth.LAST_NAME, real_auth.FIRST_NAME, 
                             inst_auth.FIRST_NAME as INST_FIRST_NAME, inst_auth.LAST_NAME as INST_LAST_NAME 
                      from booking
                      left join aircrafts on booking.AIRCRAFT_NUM=aircrafts.NUM
                      left join authentication as real_auth on booking.MEMBER_NUM=real_auth.NUM 
                      left join instructors on booking.INST_NUM=instructors.INST_NUM 
                      left join authentication as inst_auth on booking.INST_NUM=inst_auth.NUM 
                      where booking.START_DATE<\''.$end_date->getDate().'\' 
                        and booking.END_DATE>\''.$start_date->getDate().'\' and booking.'.$field.'=\''.$value.'\' and booking.ID<>\''.$bookId.'\'');
    while(($row=$database->fetch())and($ok_flag))
    {
        $book=new booking($row);
        $ok_flag=false;
    }
    $database->free();
}

function displayBadRequest($title)
{
    global $lang;

    global $bookId;
    global $member;
    global $instructor;
    global $aircraft;
    global $start_date;
    global $end_date;
    global $free_seats;
    global $slot_type;
    global $old_member;
    global $old_instructor;
    global $old_aircraft;
    global $old_start_date;
    global $old_end_date;
    global $old_free_seats;
    global $old_slot_type;
    global $sub_menu;
    global $outdateFlag;
    global $overtop;
    global $subscription;
    global $timeZone;
    global $frenchDisplay;

    require_once('./includes/header.php');
    ?></head><body onload="document.getElementById('validation').focus();"><?php

    $mainMes='';
    switch($outdateFlag)
    {
    // the person for whom is the book is outdate now
    case 1:
        $mainMes=$lang['BOOK_OUT_SUBSCRIPTION'].$subscription->displayDatetime($timeZone,$frenchDisplay).'.<br />'.$lang['BOOK_RESTRICTED_PERMITS'].'&nbsp;:';
        break;
    // you are out date now
    case 2:
        $mainMes=$lang['BOOK_OWN_OUT_SUBSCRIPTION'].$subscription->displayDatetime($timeZone,$frenchDisplay).'.<br />'.$lang['BOOK_OWN_RESTRICTED_PERMITS'].'&nbsp;:';
        break;
    // the person for whom is the book will be outdate at the date of the book
    case 3:
        $mainMes=$lang['BOOK_WILL_OUT_SUBSCRIPTION'].$subscription->displayDatetime($timeZone,$frenchDisplay).'.<br />'.$lang['BOOK_RESTRICTED_PERMITS'].'&nbsp;:';
        break;
    }
    $request=new requestForm($mainMes);
    $request->addTitle($title);
    $request->addHidden('menu',3);
    if ($sub_menu!=11)
    {
        $request->addHidden('ts_old_start_date',$old_start_date->getTS());
        $request->addHidden('ts_old_end_date',$old_end_date->getTS());
        $request->addHidden('sub_menu',2);
        $request->addHidden('old_member',$old_member);
        $request->addHidden('old_instructor',$old_instructor);
        $request->addHidden('old_aircraft',$old_aircraft);
        $request->addHidden('old_free_seats',$old_free_seats);
        $request->addHidden('old_slot_type',$old_slot_type);
        $request->addHidden('bookId',$bookId);
    }
    else
    {
        $request->addHidden('ts_old_start_date',$start_date->getTS());
        $request->addHidden('ts_old_end_date',$end_date->getTS());
        $request->addHidden('sub_menu',1);
        $request->addHidden('old_member',$member);
        $request->addHidden('old_instructor',$instructor);
        $request->addHidden('old_aircraft',$aircraft);
        $request->addHidden('old_free_seats',$free_seats);
        $request->addHidden('old_slot_type',$slot_type);
    }
    if ($overtop)
    {
        $request->addHidden('overtop',$overtop);
        $request->addTitle($lang['BOOK_WISH_CONFIRM']);
        $request->addButton($lang['YES'],'validation','onclick="this.form.sub_menu.value='.$sub_menu.'; submit()"');
    }
    $request->close($lang['BACK_BUTTON']);
    require_once ('./includes/footer.php');
}

////////////////////////////// Main code start here /////////////////////////////////


$timeZone=$userSession->getTimeZone();
$frenchDisplay=$userSession->isFrenchDateDisplay();
$qualifChecker=new qualifChecker($database,$timeZone,$frenchDisplay);

if (!$database->query('lock tables aircrafts as reference read, member_qualif read, qualification read, aircraft_qualif read, authentication as real_auth read, authentication as inst_auth read, aircrafts read, clubs read, members read, profiles read, instructors read, authentication read, exceptionnal_inst_dates read, regular_presence_inst_dates read, booking write, logs write'))
{
    displayBadRequest($lang['BOOK_UNABLE_LOCK']);
}

// $slot_type value is changed if there is an $instructor value!=0

/* if overtop is defined at 1 or 2 so it's the second time we are here and we have to really book
* (we had confirmation by the user)
* overtop = 1 => qualification outdated but doesn't matter
* overtop = 2 => instructor is not freee but doesn't matter
*/
define_global('overtop',0);
if ($overtop==0)
{
    // if $overtop==0, so it's the first time there, and we do not have saved anything
    define_global('bookId');
}
else
{
    $userSession->define('bookId');
}

if($bookId!='')
{
    $result=$database->queryAndFetch('select * from booking where ID=\''.$bookId.'\'');
	$book=new booking($result);
	$database->free();
	$old_start_date=$book->start_date;
	$old_end_date=$book->end_date;
	$old_instructor=$book->getInstructor();
	$old_aircraft=$book->getAircraft();
	$old_free_seats=$book->getFreeSeats();
	$old_comments=$book->getComments();
	$old_member=$book->getPilot();
	$old_slot_type=$book->getSlotType();
}

if ($overtop==0)
{
    define_global('aircraft');
    define_global('start_year');
    define_global('start_month');
    define_global('start_day');
    define_global('start_hour');
    define_global('start_minute');
    define_global('end_year');
    define_global('end_month');
    define_global('end_day');
    define_global('end_hour');
    define_global('end_minute');
    if (isset($old_member)) {
        define_global('member',$old_member);
    }
    else {
        define_global('member');
    }
    define_global('member_num');
    define_global('free_seats', 0);
    define_global('comments');
    $comments=ereg_replace('[[:cntrl:]]','',nl2br(htmlentities($comments)));
    define_global('instructor');
    define_global('slot_type');

    if ($end_year=='')
    {
        $end_date=$old_end_date;
    }
    else
    {
        $end_date=new ofDate($end_year.$end_month.$end_day.$end_hour.$end_minute.'00');
        $end_date->setTZ($timeZone);
        $end_date->convertTZbyID('UTC');
    }

    if ($start_year=='') {
        $start_date=$old_start_date;
    }
    else {
        $start_date=new ofDate($start_year.$start_month.$start_day.$start_hour.$start_minute.'00');
        $start_date->setTZ($timeZone);
        $start_date->convertTZbyID('UTC');
    }

    define_global('bookLogin');
    define_global('bookPassword');
}
else
{
    // if $overtop>0, we have already been there, and we can take back saved (sessioned) vars
    $userSession->define('bookLogin');
    $userSession->define('bookPassword');
    $userSession->define('member');
    $userSession->define('member_num');
    $userSession->define('free_seats', 0);
    $userSession->define('comments');
    $userSession->define('instructor');
    $userSession->define('slot_type');
    $userSession->define('aircraft');
    $userSession->define('start_date');
    $userSession->define('end_date');
}

// flag used along the script to end it in case of wrong parameters

$ok_flag=true;
if (getPermits($userSession,$member_num,$subscription,$permits,$bookLogin,$bookPassword))
{
    $ok_flag=false;
    displayBadRequest($lang['BAD_LOGIN']);
}
        
// $member_num = person connected

// $member = person for whom is the book (0 if mecanic)

$announcer=new announcer($database,$member_num,$userSession->getClubMailFromAddress(),$timeZone,$frenchDisplay,$userSession->getClubName());
$bookAllocator=new bookAllocator($userSession->parameter->getBookAllocatorRule(),$database,$member_num,$announcer);

if ($ok_flag)
{
    // We have to know the rights of the person of the slot
    if ($member_num==$member)
    {
        $member_permits=$permits;
    }
    else
    {
        $database->query('select profiles.PERMITS from authentication
                          left join profiles on (authentication.PROFILE&profiles.NUM)=profiles.NUM
                          where authentication.NUM=\''.$member.'\'');
        $member_permits=0;
        $row=$database->fetch();
        while($row)
        {
            $member_permits=$member_permits|$row->PERMITS;
            $row=$database->fetch();
        }
        $database->free();

        // we have also to take care about the subscription validity
        if ($member!=0)
        {
            $subscription=new ofDate($database->query_and_fetch_single('select SUBSCRIPTION from members where members.NUM=\''.$member.'\'').'T23:59:59');
            if (($userSession->parameter->isUseSubscription()==2)and($subscription->isPast()))
            {
                // if subscription is outdate, we set according permits
                $member_permits=$userSession->parameter->getOutdateSubscriptionPermits();
                $outdateFlag=1;
            }
        }
        else
        {
            $subscription=new ofDate('9999-12-31T23:59:59');
        }
    }

    if (($userSession->parameter->isUseSubscription()==2)and($subscription->before($end_date)))
    {
        $member_permits=$userSession->parameter->getOutdateSubscriptionPermits();
        $outdateFlag=3;
    }

    // we adjust values according to slot type
    if ($slot_type!=BOOK_MECANIC)
    {
        if ($instructor!=0)
        {
            $slot_type=BOOK_INST;
        }
        else
        {
            $slot_type=BOOK_ALONE;
        }
    }
    else
    {
        $member=0;
        $instructor=0;
    }

    // Define a time-date equal to now minus a quarter of an hour
    $almost_now = new ofDate();
    $almost_now->subtractSeconds(900);
    $maxBookDateAllowed = $almost_now;

    // we have to check if member and booker have permissions to do the book

    if ($userSession->parameter->isBookDateLimitation()) {
        $weekGap = $userSession->parameter->getBookDateLimitation();
        if ($weekGap) {
            $daysGap = $weekGap * 7;
            $weekSpan = new ofDateSpan($daysGap.'-0-0-0');
            $maxBookDateAllowed->addSpan($weekSpan);
            if (!isAnytimeBookAllowed($permits) and ($maxBookDateAllowed->before($end_date))) {
                // member is not allowed to book to far in the future
                displayBadRequest($lang['BOOK_NO_OVERLIMIT_1'].$weekGap.$lang['BOOK_NO_OVERLIMIT_2']);
                $ok_flag = false;
            }
        }
    }

    if ($ok_flag and $userSession->parameter->isBookDurationLimitation()) {
        $maxHoursRange = $userSession->parameter->getBookDurationLimitation();
        if ($maxHoursRange) {
            $maxHoursSpan = new ofDateSpan($maxHoursRange);
            $hoursSpan = new ofDateSpan($start_date, $end_date);
            if (!isAnydurationBookAllowed($permits) and ($hoursSpan->greater($maxHoursSpan))) {
                // member is not allowed to book a too long duration slot
                displayBadRequest($lang['BOOK_NO_OVERDURATION_1'].$maxHoursRange.$lang['BOOK_NO_OVERDURATION_2']);
                $ok_flag = false;
            }
        }
    }

    if (!$ok_flag) {
    }
    elseif (($sub_menu==11) and (isNonBookableAircraft($aircraft)==1)) {
        // it's not possible to make a new book with a non bookable aircraft
        displayBadRequest($lang['BOOK_NON_BOOKABLE_AIRCRAFT']);
        $ok_flag=false;
    }
    elseif (($slot_type==BOOK_ALONE)AND(!isAloneBookAllowed($member_permits))AND(($sub_menu!=12)or(!isAnybodyBookAllowed($permits))))
    {
        // member is not allowed to fly alone and there is no instructor
        displayBadRequest($lang['BOOK_NO_ALONE']);
        $ok_flag=false;
    }
    elseif (($slot_type==BOOK_INST)AND(!isInstructorBookAllowed($member_permits))AND(($sub_menu!=12)or(!isAnybodyBookAllowed($permits))))
    {
        // member is not allowed to book with an instructor and is has choosed an instructor
        displayBadRequest($lang['BOOK_NO_LEARNING']);
        $ok_flag=false;
    }
    elseif ((!isFreezeAircraftAllowed($permits))AND($slot_type==BOOK_MECANIC))
    {
        // user is not allowed to unfree an aircraft and is has choosed to do it !
        displayBadRequest($lang['BOOK_NO_MANAGE_AIRCRAFT']);
        $ok_flag=false;
    }
    elseif (!(isAnybodyBookAllowed($permits))AND(($member_num!=$member)OR(($bookId!='')AND($old_member!=$member)))AND(!(isInstructor($member_num)AND($instructor==$member_num)))AND($slot_type!=BOOK_MECANIC))
    {
        // user is not allowed to book for someone else and is has choosed...someone else
        displayBadRequest($lang['BOOK_NO_MANAGE_OTHERS']);
        $ok_flag=false;
    }
    elseif (($sub_menu!=13)AND($start_date->before($almost_now)))
    {
        // user is not allowed to book or cancel with an obsolete start_date
        displayBadRequest($lang['BOOK_NO_OUT']);
        $ok_flag=false;
    }
    elseif ((($sub_menu==13)or($sub_menu==12))AND($old_start_date->before($almost_now))AND(!$start_date->equals($old_start_date)))
    {
        // user is not allowed to modify an obsolete old_start_date
        displayBadRequest($lang['BOOK_NO_OUT_CHANGE']);
        $ok_flag=false;
    }
    elseif (($sub_menu==13)AND($end_date->before($almost_now)OR(!$start_date->equals($old_start_date)AND($start_date->before($almost_now)))))
    {
        $tz=$almost_now->tz;
        // user is not allowed to modify an end_date to be still obsolete
        displayBadRequest($lang['BOOK_NO_SET_OUT']);
        $ok_flag=false;
    }
    elseif ($end_date->before($start_date))
    {
        displayBadRequest($lang['BOOK_TWISTED_DATE']);
        $ok_flag=false;
    }
}
if ($ok_flag)
{
    if ($sub_menu==12)
    {
        // IT'S A CANCELLATION
        if ($bookAllocator->remove($bookId))
        {
            redirect($userSession,$start_date->getTS());
            $ok_flag=false;
        }
        else
        {
            displayBadRequest($lang['ERROR_TRANSMIT_DATA']);
            $ok_flag=false;
        }
    }
}
if (($ok_flag)and($slot_type!=BOOK_MECANIC))
{
    // Check if member has not a slot yet (or another one)
    isAlreadyBook('MEMBER_NUM',$member);
    if (!$ok_flag)
    {
        displayBadRequest($lang['BOOK_ALREADY_BOOK'].$book->start_date->displayDateGap($book->end_date,$timeZone,$frenchDisplay));
    }
}
if (($ok_flag)and(isInstructor($member))and($slot_type!=BOOK_MECANIC))
{
    // Check if member has not a slot yet as an instructor
    isAlreadyBook('INST_NUM',$member);
    if (!$ok_flag)
    {
        displayBadRequest($lang['BOOK_ALREADY_BOOK'].$book->start_date->displayDateGap($book->end_date,$timeZone,$frenchDisplay));
    }
}
if ($ok_flag)
{
    $start_time=$start_date->getClockSpan();
    $end_time=$end_date->getClockSpan();
    // Check if aircraft has not a slot yet
    if (($userSession->parameter->getBookAllocatorRule()!=1)or($slot_type!=BOOK_MECANIC))
    {
        isAlreadyBook('AIRCRAFT_NUM',$aircraft);
    }
    $interval=$userSession->getIntervalDisplayed();
    if (!$ok_flag)
    {
        displayBadRequest($lang['BOOK_AIRCRAFT_BOOK'].$book->start_date->displayDateGap($book->end_date,$timeZone,$frenchDisplay,1));
    }
    elseif (!$userSession->parameter->isNoOpenTimeLimitation()) {
        if (!$interval->isIn($start_time))
        {
            $startOpenTime = $start_date;
            $startOpenTime->setClock($interval->begin);
            $endOpenTime = $start_date;
            $endOpenTime->setClock($interval->end);
            displayBadRequest($lang['BOOK_START_TIME_REQUIRED'].$startOpenTime->displayTime($timeZone).$lang['BOOK_AND'].$endOpenTime->displayTime($timeZone));
            $ok_flag=false;
        }
        elseif (!$interval->isIn($end_time))
        {
            $startOpenTime = $end_date;
            $startOpenTime->setClock($interval->begin);
            $endOpenTime = $end_date;
            $endOpenTime->setClock($interval->end);
            displayBadRequest($lang['BOOK_END_TIME_REQUIRED'].$startOpenTime->displayTime($timeZone).$lang['BOOK_AND'].$endOpenTime->displayTime($timeZone));
            $ok_flag=false;
        }
    }
}
if (($ok_flag)and($overtop==0))
{
    if (($userSession->parameter->isUseQualif())AND(!$qualifChecker->isAllowed($member,$aircraft,$slot_type,$end_date,$answer)))
    {
        // member is not allowed to book with(out) an instructor on this type of aircraft
        if($slot_type==BOOK_ALONE)
        {
            $message=$lang['BOOK_NO_ALONE_TYPE'];
        }
        elseif ($slot_type==BOOK_INST)
        {
            $message=$lang['BOOK_NO_LEARNING_TYPE'];
        }
        $message=$message.'&nbsp;'.$lang['BOOK_NO_TYPE_CAUSE'].'&nbsp;:';
        for ($i=0;$i<sizeof($answer);$i++)
        {
            $message=$message.'<br />'.$answer[$i];
        }
        // if we can book with outdated qualif, so have to ask for confirmation
        if (!$userSession->parameter->isRestrictiveQualif())
        {
            $overtop=1;
            saveVar();
        }
        displayBadRequest($message);
        $ok_flag=false;
    }
}

if (($ok_flag)and($instructor!=0))
{
    if ($instructor==$member)
    {
        $ok_flag=false;
        displayBadRequest($lang['BOOK_NO_SAME_PILOT_AND_INST']);
    }
}
if (($ok_flag)and($instructor!=0))
{
    // Check if instructor has not a slot yet
    isAlreadyBook('INST_NUM',$instructor);
    if (!$ok_flag)
    {
        displayBadRequest($lang['BOOK_INST_BOOK'].$book->start_date->displayDateGap($book->end_date,$timeZone,$frenchDisplay,1));
    }
}
if (($ok_flag)and($instructor!=0))
{
    // Check if instructor has not a slot yet as a member
    isAlreadyBook('MEMBER_NUM',$instructor);
    if (!$ok_flag)
    {
        displayBadRequest($lang['BOOK_INST_BOOK'].$book->start_date->displayDateGap($book->end_date,$timeZone,$frenchDisplay,1));
    }
}
if (($ok_flag)and($instructor!=0))
{
    /* if $overtop==2 -> we don't have to check instructor disponibility because booker have already confirm,
    * otherwise we must test for each quarter of hour if the instructor is available
    */
    if ($overtop!=2)
    {
        $instAvail=new instAvailibility($instructor,$database,$start_date,$end_date,floor($timeZone->getOffset($start_date)/60000));
        for($i=$start_date;$i->before($end_date)AND($ok_flag);$i->addSeconds(900))
        {
            $ok_flag=$instAvail->isAvailable($i);
        }
    }
    if (!$ok_flag)
    {
        if (isBookUnfreeInstAllowed($permits))
        {
            $overtop=2;
            saveVar();
        }
        displayBadRequest($lang['BOOK_NO_INST']);
    }
}
if ($ok_flag)
{
    if ($overtop)
    {
        $userSession->kill('bookLogin');
        $userSession->kill('bookPassword');
        $userSession->kill('member');
        $userSession->kill('member_num');
        $userSession->kill('free_seats');
        $userSession->kill('comments');
        $userSession->kill('instructor');
        $userSession->kill('slot_type');
        $userSession->kill('aircraft');
        $userSession->kill('start_date');
        $userSession->kill('end_date');
        $userSession->kill('old_start_date');
        $userSession->kill('old_end_date');
        $userSession->kill('old_member');
        $userSession->kill('old_comments');
        $userSession->kill('old_aircraft');
        $userSession->kill('old_free_seats');
        $userSession->kill('old_instructor');
        $userSession->kill('old_slot_type');
    }
    if ($sub_menu==11)
    {
        ///////////// IT'S A REAL BOOK
        $result=$bookAllocator->add($start_date,$end_date,$aircraft,$member,$slot_type,$instructor,$free_seats,$comments);
    }
    elseif ($sub_menu==13)
    {
        // IT'S A MODIFICATION
        $result=$bookAllocator->update($bookId,$start_date,$end_date,$aircraft,$member,$slot_type,$instructor,$free_seats,$comments);
    }
    if ($result)
    {
        redirect($userSession,$start_date->getTS());
    }
    else
    {
        displayBadRequest($lang['ERROR_TRANSMIT_DATA']);
    }
}
$database->query('unlock tables');
?>