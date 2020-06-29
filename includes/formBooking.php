<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * formBooking.php
 *
 * construct form booking
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
 * @version    CVS: $Id: formBooking.php,v 1.21.2.6 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 03 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./classes/booking.class.php');
require_once('./displayClasses/requestForm.class.php');

// We assume that first_display_date, $userSession are defined
$isBookAllowed=($userSession->isAnybodyBookAllowed()OR$userSession->isAloneBookAllowed()OR$userSession->isInstructorBookAllowed()OR$userSession->isNothingAllowed());
if($isBookAllowed)
{
	$noTwiceNum='no_twice_num();';
}
else
{
	$noTwiceNum='';
}

// Define a time-date equal to now minus a quarter of an hour
$almostNow = new ofDate();
$almostNow->subtractSeconds(900);

//////////////////////// HTML DISPLAY START HERE ////////////////////////////
require_once('./includes/header.php');
?></head><body onload="<?php echo($noTwiceNum);?>checkStartDate();"><?php
require_once('./includes/menus.php');

// Initialisation if missing args
define_global('bookId');
if($bookId!='')
{
    $result=$database->queryAndFetch('select * from booking where ID=\''.$bookId.'\'');
	$book=new booking($result);
	$database->free();
	$start_date=$book->start_date;
	$old_start_date=$start_date;
	$end_date=$book->end_date;
	$old_end_date=$end_date;
	$instructor=$book->getInstructor();
	$old_instructor=$instructor;
	$aircraft=$book->getAircraft();
	$old_aircraft=$aircraft;
	$free_seats=$book->getFreeSeats();
	$old_free_seats=$free_seats;
	$comments=$book->getNoSlashesComments();
	$old_comments=$comments;
	$member=$book->getPilot();
	$old_member=$member;
	$slot_type=$book->getSlotType();
	$old_slot_type=$slot_type;
}
else
{
	$start_date=$firstDisplayedDate;
	$old_start_date=$start_date;
	// We look if there is a ts_old_end_date variable sent
	define_global('ts_old_end_date','');
	if($ts_old_end_date!='')
	{
		$old_end_date=new ofDate($ts_old_end_date);
	}
	else
	{
	// if the ts variable is not send we compute a new now, according of the start_date and the default slot range value
		$old_end_date=$start_date;
		$old_end_date->addSpan($userSession->getDefaultSlotRange());
	}
	define_global('ts_end_date','');
	if($ts_end_date!='')
	{
		$end_date=new ofDate($ts_end_date);
	}
	else
	{
		$end_date=$old_end_date;
	}
	
	define_global('old_instructor',0);
	define_global('instructor',$old_instructor);
	define_global('old_aircraft');
	define_global('aircraft',$old_aircraft);
	define_global('old_free_seats',0);
	define_global('free_seats',$old_free_seats);
	define_global('old_comments');
	define_global('comments',$old_comments);
	define_global('old_member',$userSession->getAuthNum());
	define_global('member',$old_member);
	if($isBookAllowed)
	{
		define_global('old_slot_type',0);
	}
	else
	{
		define_global('old_slot_type',2);
	}
	define_global('slot_type',$old_slot_type);
}

$freezedStartDate   = ($start_date->before($almostNow) and ($sub_menu == 2)) ? true : false;
$noCancel           = ($end_date->before($almostNow) and ($sub_menu == 2)) ? true : false;


// Construct a form for booking a slot
if($sub_menu==2)
{
    $title=$lang['BOOK_MOD_BOOK'];
}
else
{
	$title=$lang['BOOK_ADD_BOOK'];
}
$askForm=new requestForm($title,'onsubmit="return askIdent();"','Book');
$askForm->addHidden('menu',3);
$askForm->addHidden('slot_type',$slot_type);
if($sub_menu==2)
{
	// Record old values for 1 thing : send old values for modifying according to them
	$askForm->addHidden('sub_menu',13);
	$askForm->addHidden('bookId',$bookId);
}
else
{
	$askForm->addHidden('sub_menu',11);
}

// Here his the case of visitor connexion type : local identification required to book a slot
if($userSession->isNothingAllowed())
{
	$askForm->addHidden('bookLogin','');
	$askForm->addHidden('bookPassword','');
}
$javaFuncText='onkeyup="'.$noTwiceNum.'"';
$javaFunc='onchange="'.$noTwiceNum.'"';
$askForm->addCombo($lang['AIRCRAFT'],'aircraft',$aircraftsClass->getList(),$aircraft,$javaFunc);

// if is booking allowed, we display members combo, instructors combo and free seats combo
if($isBookAllowed)
{
	if(($userSession->isAnybodyBookAllowed())or($userSession->isNothingAllowed()or$userSession->isInstructor()))
	{
        $members=$userSession->getMembers();
	}
	else
	{
		$members=array(array($member,$userSession->getLastName().' '.$userSession->getFirstName()));
	}
    $askForm->addCombo($lang['MEMBER'],'member',$members,$member,$javaFunc);

    $instructors=array();
    if($userSession->isAnybodyBookAllowed()OR$userSession->isInstructorBookAllowed()OR$userSession->isNothingAllowed())
	{
	    $instructors=$instructorsClass->getList();
	}
    if($userSession->isAnybodyBookAllowed()OR$userSession->isAloneBookAllowed()OR$userSession->isNothingAllowed())
	{
        $instructors[]=array(0,$lang['ALONE']);
	}
	$askForm->addCombo($lang['INSTRUCTOR'],'instructor',$instructors,$instructor,'onchange="'.$noTwiceNum.' checkStartDate();"');

	$seats=array();
	for ($i=0;$i<6;$i++)
	{
	    $seats[]=array($i,$i);
	}
	$askForm->addCombo($lang['BOOK_FREE_SEAT'],'free_seats',$seats,$free_seats,$javaFunc);
}

// if is freezing aircraft allowed we display a checkbox to freeze the aircraft
if($userSession->isFreezeAircraftAllowed()OR$userSession->isNothingAllowed())
{
	$askForm->addCheckBox('','freeze_aircraft',1,($slot_type==2),$lang['BOOK_FREEZE_AIRCRAFT'],'onclick="checkFreezeAircraft()"',!$isBookAllowed);
	if(!$isBookAllowed)
	{
		$askForm->addHidden('freeze_aircraft',true);
	}
}

// add start date combos
$localStartDate=$start_date;
$localStartDate->convertTZ($userSession->getTimeZone());
$askForm->addDatetime($lang['BEGIN'],'start',$localStartDate->getTS(),$userSession->isFrenchDateDisplay(),'onchange="checkStartDate()"', $freezedStartDate);

// The box is checked if start_date=end_date
if($userSession->isSameDayBox())
{
	$askForm->addCheckBox('','sameDay',1,$start_date->isSameDay($end_date),$lang['SAME_DAY'],'onchange="checkStartDate()"');
}
else
{
	$askForm->addHidden('sameDay',false);
}

// add end date combos
$localEndDate=$end_date;
$localEndDate->convertTZ($userSession->getTimeZone());
$askForm->addDatetime($lang['END'],'end',$localEndDate->getTS(),$userSession->isFrenchDateDisplay(),'onchange="checkEndDate()"');

// Display Comment box
if($userSession->isBookComment())
{
	$askForm->addTextArea($lang['COMMENTS'],'comments',str_replace('<br />',"\n",stripslashes($comments)),$javaFuncText);
}
else
{
	$askForm->addHidden('comments',$comments);
}

if ($sub_menu == 2)
{
    if (!$noCancel) {
        if ($freezedStartDate) {
            $almostNow->convertTZ($userSession->getTimeZone());
            $askForm->addButton($lang['DELETE'],'change',
            'onclick="if(confirm(\''.$lang['BOOK_CONFIRM_ERASE'].'\')){
document.getElementById(\'formId\').sub_menu.value=13;
document.getElementById(\'end_day\').value=\''.sprintf("%'02d",$almostNow->getDay()).'\';
document.getElementById(\'end_month\').value=\''.sprintf("%'02d",$almostNow->getMonth()).'\';
document.getElementById(\'end_year\').value=\''.sprintf("%'04d",$almostNow->getYear()).'\';
document.getElementById(\'end_hour\').value=\''.sprintf("%'02d",$almostNow->getHour()).'\';
document.getElementById(\'end_minute\').value=\''.sprintf("%'02d",(floor(($almostNow->getMinute())/15)*15+15)).'\';
if(askIdent()){submit()}}"'
            );
        }
        else {
            $askForm->addButton($lang['DELETE'],'change','onclick="if(confirm(\''.$lang['BOOK_CONFIRM_ERASE'].'\')){document.getElementById(\'formId\').sub_menu.value=12; if(askIdent()){submit()}}"');
        }
    }
    $askForm->close($lang['MODIFY']);
}
else
{
    $askForm->close($lang['VALIDATE']);
}
require_once('./includes/backButton.php');
?>
	<script type="text/javascript">
	// Javascript functions follow :

		var min_slot_range=<?php echo($userSession->getMinSlotRange());?>;
		var min_instr_slot_range=<?php echo($userSession->parameter->isBookInstructionMinTime()?$userSession->parameter->getBookInstructionMinTime():$userSession->getMinSlotRange());?>;
		var cancel_flag=<?php if($sub_menu==2){echo('true');}else{echo('false');}?>;
		var book_allowed_flag=<?php if($isBookAllowed){echo('true');}else{echo('false');}?>;
		var freeze_flag=<?php if((($userSession->isFreezeAircraftAllowed())OR($userSession->isNothingAllowed()))AND($menu==3)){echo('true');}else{echo('false');}?>;
	</script>
	<script type="text/javascript" src="javascript/formatFunctions.js"></script>
	<script type="text/javascript" src="javascript/checkDates.js"></script>
	<script type="text/javascript" src="javascript/formBooking.js"></script>
<?php
require_once('./includes/footer.php');
?> 
