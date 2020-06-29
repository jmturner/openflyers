<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * formInstructorsRests.php
 *
 * construct form instructor rest
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
 * @version    CVS: $Id: formInstructorsRests.php,v 1.12.2.3 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 24 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./displayClasses/requestForm.class.php');

// We assume that $ts_old_start_date, $firstDisplayedDate, $userSession, $sub_menu are defined

$instructors=array();
if($userSession->isFreezeInstructorAllowed())
{
    $instructors=$instructorsClass->getList();
}
else
{
    $instructors[]=array($userSession->getAuthNum(),$userSession->getFirstName().' '.$userSession->getLastName());
}

//////////////////////// HTML DISPLAY START HERE ////////////////////////////
require_once('./includes/header.php');
?></head><body onload="<?php
if($sub_menu!=4)
{
    ?>checkExceptionnalSlot(); <?php
}
if(($sub_menu!=4)or($exceptionnalSlot))
{
    ?>checkStartDate(); <?php
}
if(($sub_menu!=4)or(!$exceptionnalSlot))
{
    ?>check_regular_start_day(); <?php
}
?>"><?php
require_once('./includes/menus.php');

// Initialisation if missing args
$start_date=$firstDisplayedDate;
define_global('exceptionnalSlot',1);
define_global('old_member',$userSession->getAuthNum(),0);
define_global('member',$old_member,0);
define_global('old_instructor',$userSession->getAuthNum());
define_global('instructor',$old_instructor,0);
define_global('ts_old_start_date');
define_global('ts_old_end_date');
define_global('ts_end_date',$ts_old_end_date);
define_global('old_presence',1);
define_global('presence',$old_presence);
define_global('ts_old_start_time','');
define_global('ts_start_time','');
define_global('ts_old_end_time','');
define_global('ts_end_time','');
if($ts_old_start_date!='')
{
	$old_start_date=new ofDate($ts_old_start_date);
}
else
{
	$old_start_date=$start_date;
}
if($ts_end_date!='')
{
	$end_date=new ofDate($ts_end_date);
}
else
{
	$end_date=$start_date;
	$end_date->addSpan($userSession->getDefaultSlotRange());
}
if($ts_old_end_date!='')
{
	$old_end_date=new ofDate($ts_old_end_date);
}
else
{
	$old_end_date=$end_date;
}
if($ts_old_start_time!='')
{
	$old_start_time=new ofDateSpan($ts_old_start_time.':00');
}
else
{
    $firstDisplayedDate->convertTZ($userSession->getTimeZone());
	$old_start_time=$firstDisplayedDate->getDateSpan();
	$firstDisplayedDate->convertTZbyID('UTC');
}
if($ts_start_time!='')
{
	$start_time=new ofDateSpan($ts_start_time.':00');
}
else
{
	$start_time=$old_start_time;
}
if($ts_old_end_time!='')
{
	$old_end_time=new ofDateSpan($ts_old_end_time.':00');
}
else
{
	$old_end_time=$start_time;
	$old_end_time->add($userSession->getDefaultSlotRange());
}
if($ts_end_time!='')
{
	$end_time=new ofDateSpan($ts_end_time.':00');
}
else
{
	$end_time=$old_end_time;
}

// Construct a form for adding or modifying an instructor (un)availibity period
if($sub_menu==4)
{
    $title=$lang['REST_MOD_PERIOD'];
}
else
{
    $title=$lang['ADD_PERIOD'];
}

$askForm=new requestForm($title,'','Book');
$askForm->addHidden('menu',4);
//$ if sub_menu==4 -> we modify an existing period otherwise we add a new period
if($sub_menu==4)
{
	// Record old values for 1 thing : send old values for modifying according to them
    $askForm->addHidden('sub_menu',14);
    $askForm->addHidden('old_instructor',$old_instructor);
    $askForm->addHidden('exceptionnalSlot',$exceptionnalSlot);
    $askForm->addCombo($lang['INSTRUCTOR'],'instructor',$instructors,$instructor);

    // We display combos according to the period type (exceptionnal or regular)
	if($exceptionnalSlot)
	{
        $askForm->addHidden('ts_old_start_date',$old_start_date->getTS());
        $askForm->addHidden('ts_old_end_date',$old_end_date->getTS());
        $askForm->addHidden('old_presence',$old_presence);
        $askForm->addTitle($lang['REST_EXCEP_SLOT']);
	}
	else
	{
        $askForm->addHidden('ts_old_start_time',$old_start_time->getNNSV());
        $askForm->addHidden('ts_old_end_time',$old_end_time->getNNSV());
        $askForm->addTitle($lang['REST_REGULAR_ATTENDANCE']);
	}
}
else
{
    $askForm->addHidden('sub_menu',12);
    $askForm->addCombo($lang['INSTRUCTOR'],'instructor',$instructors,$instructor);
    $askForm->addCheckBox('','exceptionnalSlot',1,$exceptionnalSlot,$lang['REST_EXCEP_SLOT'],'onclick="checkExceptionnalSlot()"');
}

if(($sub_menu!=4)or(!$exceptionnalSlot))
{
    $askForm->addDayHour($lang['REST_AVAIL_FROM'],'regular_start',$start_time->getNNSV(),'onchange="check_regular_start_day()"');
	if($userSession->isSameDayBox())
	{
	    $askForm->addCheckBox('','regSameDay',1,($start_time->day==$end_time->day),$lang['SAME_DAY'],'onchange="check_regular_start_day()"');
	}
	else
	{
        $askForm->addHidden('regSameDay',false);
	}
    $askForm->addDayHour($lang['REST_AVAIL_UNTIL'],'regular_end',$end_time->getNNSV(),'onchange="check_regular_end_day()"');
}
if(($sub_menu!=4)or($exceptionnalSlot))
{
    $presenceList=array(array(0,$lang['ABSENT']),array(1,$lang['PRESENT']));
    $askForm->addRadioBox($lang['REST_AVAIL_TYPE'],'presence',$presenceList,$presence,'onchange="checkStartDate()"');
    $localStartDate=$start_date;
    $localStartDate->convertTZ($userSession->getTimeZone());
    $askForm->addDatetime($lang['BEGIN'],'start',$localStartDate->getTS(),$userSession->isFrenchDateDisplay(),'onchange="checkStartDate()"');
	// The box is checked if start_date=end_date
	if($userSession->isSameDayBox())
	{
	    $askForm->addCheckBox('','sameDay',1,$start_date->isSameDay($end_date),$lang['SAME_DAY'],'onchange="checkStartDate()"');
	}
	else
	{
        $askForm->addHidden('sameDay',false);
	}
    $localEndDate=$end_date;
    $localEndDate->convertTZ($userSession->getTimeZone());
    $askForm->addDatetime($lang['END'],'end',$localEndDate->getTS(),$userSession->isFrenchDateDisplay(),'onchange="checkEndDate()"');
}
if($sub_menu==4)
{
	define('CANCEL_MENU',true);
	$askForm->addButton($lang['DELETE'],'change','onclick="if(confirm(\''.$lang['PERIOD_CONFIRM_ERASE'].'\')){document.getElementById(\'formId\').sub_menu.value=13; submit();}"');
    $askForm->close($lang['MODIFY']);
}
else
{
	define('CANCEL_MENU',false);
    $askForm->close($lang['VALIDATE']);
}
?>
	<script type="text/javascript">
	// Javascript functions follow :

	var min_slot_range=<?php echo($userSession->parameter->isBookInstructionMinTime()?$userSession->parameter->getBookInstructionMinTime():$userSession->getMinSlotRange());?>;
	var cancel_flag=<?php if($sub_menu==4){echo('true');}else{echo('false');}?>;
	var exceptionnal_at_first_flag=<?php if($exceptionnalSlot){echo('true');}else{echo('false');}?>;
	var freeze_flag=<?php if((($userSession->isFreezeAircraftAllowed())OR($userSession->isNothingAllowed()))AND($menu==3)){echo('true');}else{echo('false');}?>;
	</script>
	<script type="text/javascript" src="javascript/formatFunctions.js"></script>
	<script type="text/javascript" src="javascript/checkDates.js"></script>
	<script type="text/javascript" src="javascript/formInstructorsRests.js"></script>
<?php
require_once('./includes/footer.php');
?> 
