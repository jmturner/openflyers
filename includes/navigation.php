<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * navigation.php
 *
 * contruct and display 1 day books or 7 days books for 1 instructor or 1 aircraft
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
 * @version    CVS: $Id: navigation.php,v 1.158.2.10 2007/10/03 09:35:58 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// We assume that $userSession, $menu, $sub_menu and $database are well defined
require_once('./displayClasses/bookView.class.php');
require_once('./classes/booking.class.php');
require_once('./classes/srssManager.class.php');
require_once('./classes/inst_availability.class.php');
$timeZone=$userSession->getTimeZone();
$icao=$userSession->getIcao();
$interval=$userSession->getIntervalDisplayed();
$siteUrl=$userSession->getClubUrl();
// We check if the display day is not one day after firstDisplayedDate
// due to timezone and interval
// if it's so, we subtract 1 day to firstDisplayedDate
$day=$firstDisplayedDate->getDay();
$onlyDate=$firstDisplayedDate;
$onlyDate->setClock($interval->begin);
$onlyDate->convertTZ($timeZone);
if ($day!=$onlyDate->getDay())
{
    $firstDisplayedDate->subtractSpan(new Date_Span('1 00:00:00'));
}
$onlyDate->convertTZbyID('UTC');
$onlyDate->clearClock();

$mgr = new srssManager();
$mgr->getSRSS($firstDisplayedDate,$icao,$sunrise,$sunset,$aero_day,$aero_night);

$userSession->setOldMenus($menu,$sub_menu);

$aircrafts_viewed=$aircraftsClass->get_viewed();
$instructors_viewed=$instructorsClass->get_viewed();

$lastHourQuarter=$interval->end->getQuarter();
$firstHourQuarter=$interval->begin->getQuarter();
$utc_offset=floor($timeZone->getOffset($firstDisplayedDate)/60000);
$lastDisplayedDate=$firstDisplayedDate;

if($menu)
{
    $lastDisplayedDate->addSpan(new Date_Span('6 23:59:59'));
}
else
{
    $lastDisplayedDate->addSpan(new Date_Span('23:59:59'));
	$aircrafts_popup=$aircraftsClass->get_popup_array();
	$instructors_popup=$instructorsClass->get_popup_array();
}

$firstDisplayedDate->setClock($interval->begin);
$lastDisplayedDate->setClock($interval->begin);
$lastDisplayedDate->addSpan($interval->amplitude);

if ($menu)
{
    if ($menu==2)
    {
        $type=ONE_INST;
    }
    else
    {
        $type=ONE_AIRCRAFT;
    }
}
else
{
    if($sub_menu<>2)
    {
        $type=AIRCRAFTS;
    }
    else
    {
        $type='EMPTY';
    }
}

$almostNow = new ofDate();

$maxBookDateAllowed = new ofDate('9999-12-31T23:59:59');
if (($userSession->parameter->isBookDateLimitation()) and (!isAnytimeBookAllowed($userSession->getPermits()))) {
    $weekGap = $userSession->parameter->getBookDateLimitation();
    if ($weekGap) {
        $daysGap = $weekGap * 7;
        $weekSpan = new ofDateSpan($daysGap.'-0-0-0');
        $maxBookDateAllowed = $almostNow;
        $maxBookDateAllowed->addSpan($weekSpan);
    }
}

$completeView=new completePage();

if($type<>'EMPTY')
{
    $completeView->addPage(0,$icao,$timeZone,$firstDisplayedDate,$lastDisplayedDate,$userSession->getIntervalDisplayed(),$type,$userSession->getAuthNum(),$userSession->isFrenchDateDisplay(),$userSession->parameter->isNoCallsignDisplay(),$maxBookDateAllowed,$sub_menu);
}

if(($menu==0)and($sub_menu<>1))
{
    $completeView->addPage(1,$icao,$timeZone,$firstDisplayedDate,$lastDisplayedDate,$userSession->getIntervalDisplayed(),INSTRUCTORS,$userSession->getAuthNum(),$userSession->isFrenchDateDisplay(),$userSession->parameter->isNoCallsignDisplay(),$maxBookDateAllowed);
}

//*********************************** database accesses *********************************************************
// Warning : the book is sort first by aircraft and init next in case of horizontal view.
// and is sort first by init and aircraft next in case of vertical view.

// Database calls to know all the slots of one day or of one week (depending of start_date and end_date)
$query='select booking.*, aircrafts.CALLSIGN, aircrafts.ORDER_NUM, instructors.SIGN, real_auth.LAST_NAME, real_auth.FIRST_NAME, 
		inst_auth.FIRST_NAME as INST_FIRST_NAME, inst_auth.LAST_NAME as INST_LAST_NAME 
		from booking
		left join aircrafts on booking.AIRCRAFT_NUM=aircrafts.NUM
		left join authentication as real_auth on booking.MEMBER_NUM=real_auth.NUM 
		left join instructors on booking.INST_NUM=instructors.INST_NUM 
		left join authentication as inst_auth on booking.INST_NUM=inst_auth.NUM 
		where booking.START_DATE<\''.$lastDisplayedDate->getDate().'\' and booking.END_DATE>\''.$firstDisplayedDate->getDate().'\'';

switch($menu)
{
	case 1:	// one week, request is sorted by date but result must be exploided according the day and the time
		if($aircraftsClass->get_viewed_size())
		{
			$query=$query.' and booking.AIRCRAFT_NUM=\''.$aircrafts_viewed[$sub_menu]->NUM.'\'';
		}
	break;
	case 2:
		if($instructorsClass->get_viewed_size())
		{
			$query=$query.' and (booking.INST_NUM=\''.$instructors_viewed[$sub_menu]->NUM.'\' or booking.MEMBER_NUM=\''.$instructors_viewed[$sub_menu]->NUM.'\')';
		}
	break;
}
$query=$query.' order by booking.START_DATE';

// Save database answer in $books array
$books=array();
$database->query($query);
$result=$database->fetch();
for ($i=0;$result;$i++)
{
	$book=new booking($result);
	$books[$i]=$book;
    $result=$database->fetch();
}
$database->free();

//
//
//*********************************** end of database accessess **************************************************


// we set the books for the display in the page
if($type<>'EMPTY')
{
    $completeView->SetBooks(0,$books);
}

//and  we set the books for the display in the page2 if necessary (ie: instructors displayed)
if(($menu==0)and($sub_menu<>1)) {
	for($i=0; $i<sizeof($instructors_viewed); $i++) {
		$instNum['\''.$instructors_viewed[$i]->NUM.'\'']=$i;
	}
    for($i=0; $i<sizeof($books); $i++) {
        $booking  = $books[$i];
		$inst     = $booking->getInstructor();
		$pilot    = $booking->getPilot();
		if ((($inst)AND$instructorsClass->is_viewed($inst))
		OR(isInstructor($pilot)AND$instructorsClass->is_viewed($pilot))) {    // we manage only viewed books
            $completeView->pages[1]->addBook($booking);
        }
    }
}


// we now get instructors availibities : for one instructor if menu=2 for all instructors if menu=0

$AtLeastOneInst=false;
$instDisplayed=array();
$instAvailTable=array();    // global array used to know the availibilies of instructors

if(($menu==2)AND($instructorsClass->get_viewed_size()))
{
	$instAvailTable[$sub_menu]=new instAvailibility($instructors_viewed[$sub_menu]->NUM,$database,$firstDisplayedDate,$lastDisplayedDate,$utc_offset);
}
elseif(($menu==0)and($sub_menu<>1))
{
	for($i=0;$i<sizeof($instructors_viewed);$i++)
	{
		$instAvailTable[$i]=new instAvailibility($instructors_viewed[$i]->NUM,$database,$firstDisplayedDate,$lastDisplayedDate,$utc_offset);
        $instBooked[$i]=false;
	}

	for($k=0;$k<sizeof($books);$k++)
	{
		$inst=$books[$k]->getInstructor();
		$pilot=$books[$k]->getPilot();
		if((($inst)AND$instructorsClass->is_viewed($inst))
		OR(isInstructor($pilot)AND$instructorsClass->is_viewed($pilot)))	// we manage only viewed books
		{
			if($books[$k]->getInstructor())
			{
				$inst=$instNum['\''.$books[$k]->getInstructor().'\''];
			}
			else
			{
				$inst=$instNum['\''.$books[$k]->getPilot().'\''];
			}
            $instBooked[$inst]=true;
		}
	}

	$k=0;	
	for($i=0;$i<sizeof($instructors_viewed);$i++)
	{
		$oneTimeAvail = false;
		if ($instBooked[$i]==false)
		{
            if ($instAvailTable[$i]->isAvailInPeriod()) {
                $oneTimeAvail = true;
            }
		}
		if(($oneTimeAvail)OR($instBooked[$i]))
		{
			$instDisplayed[$k]=$i;
			$k=$k+1;
			$AtLeastOneInst=true;
		}
	}
}
$completeView->setInstViewed($instDisplayed);

///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////

// begin of the HTML page
require_once('./includes/header.php');
?>
<script type="text/javascript" src="javascript/overlib.js"></script>
<script type="text/javascript" src="javascript/calendarPopup.js"></script>
<script type="text/javascript" src="javascript/formatFunctions.js"></script>
<script type="text/javascript" src="javascript/browser_detect.js"></script>
<script type="text/javascript" src="javascript/navigation.js"></script>
<script type="text/javascript">
var cal=new CalendarPopup("calPopup");
cal.setDayHeaders("<?php echoFirstLetter($lang['SUNDAY']);?>","<?php echoFirstLetter($lang['MONDAY']);?>","<?php echoFirstLetter($lang['TUESDAY']);?>","<?php echoFirstLetter($lang['WEDNESDAY']);?>","<?php echoFirstLetter($lang['THURSDAY']);?>","<?php echoFirstLetter($lang['FRIDAY']);?>","<?php echoFirstLetter($lang['SATURDAY']);?>");
cal.setMonthNames("<?php echo($lang['JANUARY'].'","'.$lang['FEBRUARY'].'","'.$lang['MARCH'].'","'.$lang['APRIL'].'","'.$lang['MAY'].'","'.$lang['JUN'].'","'.$lang['JULY'].'","'.$lang['AUGUST'].'","'.$lang['SEPTEMBER'].'","'.$lang['OCTOBER'].'","'.$lang['NOVEMBER'].'","'.$lang['DECEMBER']);?>");
cal.setTodayText("<?php echo($lang['TODAY']);?>");
cal.setWeekStartDay(1);
cal.setReturnFunction("submitDate");
var return_menu=<?php echo($menu);?>;
var return_sub_menu=<?php echo($sub_menu);?>;<?php

if($userSession->isLegendPopup()and($userSession->isFirstLegendPopup()))
{
	$userSession->setFirstLegendPopup(false);
    ?>OF_legend=window.open("legendPopup.php?userLang=<?php echo($userSession->getLang());?>", "OF_legend", "dependent=yes, Height=300, Width=300, screenX=2000, screenY=2000,  location=no, toolbar=no, scrollbar=yes, resizable=yes, directories=no, status=no");<?php
}
?></script>
<style type="text/css">
tr.tdWidth td {width:<?php echo($userSession->getViewWidth());?>px;}
th.thHeight {height:<?php echo($userSession->getViewHeight());?>px;}
th.hoursHeight {height:<?php echo(floor($userSession->getViewHeight())/2);?>px;}
th.instHeight {height:<?php echo(floor($userSession->getViewHeight())/2);?>px;}
th.HHeight {height:<?php echo(floor($userSession->getViewHeight())*3/4);?>px;}
</style>
</head>
<body onload="displayClock(); fill();"><?php
require_once('./includes/menus.php');
?>
<div class="bookDescriptor"><?php echo($menuNames[$menu].'&nbsp;'.$subMenuNames[$menu][$sub_menu]->getLabel());?></div><?php
///////// First div display the logo clug and the second div, info cellular used by the club to display informations
?><div class="lfloat"><?php
if($siteUrl)
{
    ?><a href="<?php echo($siteUrl);?>"><?php
}
?><img class="info" src="img/logo.php" alt="<?php echo(stripslashes($userSession->getClubName()));?>"/><?php
if($siteUrl)
{
    ?></a><?php
}
?></div><div class="info"><?php
echo(stripslashes(nl2br($userSession->getInfoCell())));
?></div><div class="lightSpacer">&nbsp;</div><?php

///////// left block display name of the connected, timezone and profile
?><div class="mainRow">
        <ul class="shortDesc"><?php
echo('<li>'.$lang['TIMEZONE'].'&nbsp;:&nbsp;'.$timeZone->getID().'</li><li>'.$lang['CONNECTED'].'&nbsp;:&nbsp;'.stripslashes($userSession->getFirstName()).' '.stripslashes($userSession->getLastName()).'</li><li>('.stripslashes($userSession->get_profile_name()).')</li>'); ?>
        </ul><?php
if ($menu==0)
{
///////// right block display ephemeris
    ?>  <ul class="ephemeris"><?php
echo('<li>'.$lang['AERORISE'].' : '.$aero_day->displayTime($timeZone).'</li>');
echo('<li>'.$lang['SUNRISE'].' : '.$sunrise->displayTime($timeZone).'</li>');
echo('<li>'.$lang['SUNSET'].' : '.$sunset->displayTime($timeZone).'</li>');
echo('<li>'.$lang['AEROSET'].' : '.$aero_night->displayTime($timeZone).'</li>');?>
        </ul><?php
}
///////// center block display date and arrow to change date
?>      <div class="datePanel">
        <a class="arrow" title="<?php echo($lang['NAVIGATION_PREVIOUS_WEEK']);?>" href="javascript:previousDays(7);">&lt;&lt;</a>
        <a class="arrow" title="<?php echo($lang['NAVIGATION_PREVIOUS_DAY']);?>" href="javascript:previousDays(1);">&lt;</a>
        <a class="<?php if($menu) {echo('small');}?>date" href="javascript:void(0);" title="<?php echo($lang['NAVIGATION_CHANGE_DATE']);?>" onclick="cal.select(document.getElementById('values').tsStartDate,'calPos','yyyyMMddHHmmss'); return false;">
        <?php
if($menu)
{
    echo($lang['DATE_FROM'].' ');
}
echo($firstDisplayedDate->displaySentenceDate($timeZone));
if($menu)
{
    echo(' '.$lang['DATE_TIL_DATE'].' ');
	echo($lastDisplayedDate->displaySentenceDate($timeZone));
}?>&nbsp;<img src="img/calendar.gif" alt="calendar Popup"/></a><a name="calPos" id="calPos"> </a>

        <a class="arrow" title="<?php echo($lang['NAVIGATION_NEXT_DAY']);?>" href="javascript:nextDays(1);">&gt;</a>
        <a class="arrow" title="<?php echo($lang['NAVIGATION_NEXT_WEEK']);?>" href="javascript:nextDays(7);">&gt;&gt;</a>
        <p id="clock">&nbsp;</p>
        </div>
    </div>
    <div class="lightSpacer">&nbsp;</div>
<?php

/////////////////////////////////// Choice of slots display and construction of it
if((($aircraftsClass->get_viewed_size()==0)and($menu==1))or(($instructorsClass->get_viewed_size()==0)and($menu==2))or(($instructorsClass->get_viewed_size()==0)and($aircraftsClass->get_viewed_size()==0)and($menu==0)))
{
    ?><table class="BOOKVIEW"><?php
	if(($aircraftsClass->get_viewed_size()==0)and($menu!=2))
	{
        ?><tr class="INFO_BOX"><td>&nbsp;</td></tr><tr class="INFO_BOX"><td align="center"><?php echo($lang['NAVIGATION_NO_VISIBLE_AIRCRAFT']);?></td></tr><?php
	}
	if(($instructorsClass->get_viewed_size()==0)and($menu!=1))
	{
        ?><tr class="INFO_BOX"><td>&nbsp;</td></tr><tr class="INFO_BOX"><td align="center"><?php echo($lang['NAVIGATION_NO_VISIBLE_INST']);?></td></tr><?php
	}
	?></table><?php
}
else
{
// Construction of the table slots (lines for aircrafts and column for hours)
    $completeView->display($AtLeastOneInst);
}
?><div><a id="calPopup" name="calPopup"> </a></div><table><tr><td id="fillMarker">&nbsp;</td></tr></table><?php
require_once('./includes/footer.php');
?>
