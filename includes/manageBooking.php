<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageBooking.php
 *
 * Display books of a member
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
 * @version    CVS: $Id: manageBooking.php,v 1.5.2.6 2006/08/23 15:14:36 claratte Exp $
 * @link       http://openflyers.org
 * @since      Fri Feb 14 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

$userSession->setOldMenus($menu,$sub_menu);

$frenchDisplay=$userSession->isFrenchDateDisplay();
$timezone=$userSession->getTimeZone();

/////////////////////////// HTML DISPLAY START HERE //////////////////////////////////////////////
require_once('./includes/header.php');
?></head><body><?php
require_once('./includes/menus.php');

// we have to call header and menu first before getting list of booking due to the db class
//****************************** database accesses *************************************************

$query='select booking.*, instructors.SIGN, real_auth.LAST_NAME, real_auth.FIRST_NAME, aircrafts.CALLSIGN 
		from booking
		left join aircrafts on booking.AIRCRAFT_NUM=aircrafts.NUM
		left join authentication as real_auth on booking.MEMBER_NUM=real_auth.NUM 
		left join instructors on booking.INST_NUM=instructors.INST_NUM 
		left join authentication as inst_auth on booking.INST_NUM=inst_auth.NUM';

$clauses = array();

define_global('all_slots', false);
define_global('only_mine', false);

if (!$all_slots) {
    $almostNow = new ofDate();
    $almostNow->subtractSeconds(900);
	$clauses[] = 'booking.END_DATE>=\''.$almostNow->getDate().'\'';
 }


if ($only_mine or ((!$userSession->isAnybodyBookAllowed())and(!$userSession->isNothingAllowed()))) {
    if ($userSession->isInstructor()) {
        $clauses[] ='(booking.MEMBER_NUM='.$userSession->getAuthNum().' or booking.INST_NUM='.$userSession->getAuthNum().')';
	}
	else
	{
		$clauses[] ='booking.MEMBER_NUM='.$userSession->getAuthNum();
	}
}
if ($only_mine or ((!$userSession->isFreezeAircraftAllowed())and(!$userSession->isNothingAllowed())))
{
	$clauses[] ='booking.SLOT_TYPE!=2';
}
if ($clauses) {
    $query .= ' WHERE '.join(' AND ', $clauses);
}

$query=$query.' order by booking.START_DATE';
$database->query($query);

// $result is used whithin the HTML code below

///////////////////////////////////// HTML DISPLAY CONTINUES HERE ////////////////////////////////////////////////////
?><table class="listing"><thead><tr>
<th class="dateBookTitle"><form action="index.php" method="post">
<div><input type="hidden" name="menu" value="3"/>
<input type="hidden" name="sub_menu" value="0"/>
<input type="hidden" name="only_mine" value="<?php echo($only_mine);?>"/>
<?php echo($lang['SLOTS']);?><br />
<input type="checkbox" name="all_slots" value="true" <?php if($all_slots){?>checked="checked"<?php ;}?> onclick="submit()"/>&nbsp;<?php echo($lang['BOOK_DISPLAY_OUTDATE']);?>
</div></form></th>
<th class="pilotBookTitle"><?php
if (!$userSession->isNothingAllowed()) {
    ?><form action="index.php" method="post">
<div><input type="hidden" name="menu" value="3"/>
<input type="hidden" name="sub_menu" value="0"/>
<input type="hidden" name="all_slots" value="<?php echo($all_slots);?>"/>
<?php echo($lang['PILOT']);?><br />
<input type="checkbox" name="only_mine" value="true" <?php if($only_mine){?>checked="checked"<?php ;}?> onclick="submit()"/>&nbsp;<?php echo($lang['BOOK_DISPLAY_ONLY_MINE']);?>
</div></form><?php
}
else {
    echo($lang['PILOT']);
}
?></th>
<th class="aircraftBookTitle"><?php echo($lang['AIRCRAFT']);?></th>
<th class="instBookTitle"><?php echo($lang['INSTRUCTOR']);?></th>
<th class="seatsBookTitle"><?php echo($lang['AVAIL_SEATS']);?></th>
<th class="actionsBookTitle"><?php echo($lang['AVAIL_OPS']);?></th>
</tr></thead><tbody><?php

for($i=0;$row=$database->fetch();$i++)
{
    $start_date=new ofDate($row->START_DATE);
    $end_date=new ofDate($row->END_DATE);
    ?><tr><th>&nbsp;<?php echo($start_date->displayDateGap($end_date,$timezone,$frenchDisplay));?></th><td><?php
	if($row->SLOT_TYPE==2)
	{
        echo($lang['MECANIC']);
	}
	elseif(($row->LAST_NAME=='')and($row->FIRST_NAME==''))
	{
        ?>???<?php
	}
	else
	{
		echo($row->FIRST_NAME.'&nbsp;'.$row->LAST_NAME);
	}
	?></td><td><?php echo(stripslashes($row->CALLSIGN));?></td>
    <td>&nbsp;<?php echo(stripslashes($row->SIGN));?>&nbsp;</td>
    <td><?php echo($row->FREE_SEATS);?></td>
    <td>
    <form id="formId<?php echo($i);?>" action="index.php" method="post"><div>
        <input type="hidden" name="menu" value="3"/>
		<input type="hidden" name="sub_menu" value="2"/>
		<input type="hidden" name="bookId" value="<?php echo($row->ID);?>"/>
		<input type="image" src="img/b_edit.png" alt="<?php echo($lang['MODIFY']);?>" onclick="submit();"/>
		&nbsp;&nbsp;
        <input type="image" src="img/b_drop.png" alt="<?php echo($lang['DELETE']);?>" onclick="if(confirm('<?php echo($lang['BOOK_CONFIRM_ERASE']);?>')){this.form.sub_menu.value=12; submit();}else{return(false);};"/>
	</div></form>
    </td>
</tr><?php
}
?></tbody></table><?php
$database->free();
require_once('./includes/footer.php');
?> 