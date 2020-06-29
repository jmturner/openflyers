<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageInstructorsRests.php
 *
 * Manage instructors rests (regular and exceptionnal)
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
 * @version    CVS: $Id: manageInstructorsRests.php,v 1.6.2.4 2006/06/19 09:05:22 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat Feb 22 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./displayClasses/requestForm.class.php');

// We assume that $userSession and $database are well defined

$presence_array=array($lang['ABSENT'],$lang['PRESENT']);
$userSession->setOldMenus($menu,$sub_menu);
$authNum=$userSession->getAuthNum();
define_global('all_slots',false);
define_global('other_inst',false);
$otherInst=$userSession->isFreezeInstructorAllowed() ? ($userSession->isInstructor() ? $other_inst : true) : false;

function display_time($time)
{
	return substr($time,0,2).':'.substr($time,3,2);
}

function getQuery($table,$order,$otherInst,$authNum,$all=true)
{
	$query='select '.$table.'_inst_dates.*, instructors.SIGN from '.$table.'_inst_dates
	        left join instructors on instructors.INST_NUM='.$table.'_inst_dates.INST_NUM';

	$clauses = array();

    if (!$all)
    {
	   $clauses[] = 'END_DATE>=\''.date('Y-m-d H:i:s').'\'';
    }
	if(!$otherInst)
	{
		$clauses[] = $table.'_inst_dates.INST_NUM='.$authNum;
	}
	if ($clauses) {
        $query .= ' WHERE '.join(' AND ', $clauses);
    }
	$query=$query.' order by SIGN, '.$order;
	return $query;
}

///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
require_once('./includes/header.php');
?>	</head>
<body>
<?php
require_once('./includes/menus.php');
?><form action="index.php" method="post">
<div><input type="hidden" name="menu" value="4"/>
<input type="hidden" name="sub_menu" value="2"/>
<input type="hidden" name="all_slots" value="<?php echo($all_slots);?>"/>
<input type="checkbox" name="other_inst" value="true" <?php if($otherInst){?>checked="checked"<?php ;}?> onclick="submit()"/>&nbsp;<?php echo($lang['SHOW_OTHER_INST']);?>
</div></form>
	<div class="MINI_TITLE_FONT"><br /><?php echo($lang['REST_REGULAR_ATTENDANCE']);?><br /><br /></div>
	<table class="listing">
		<tr>
			<td rowspan="2"><?php echo($lang['INSTRUCTOR']);?></td>
			<td colspan="2"><?php echo($lang['BEGIN']);?></td>
			<td colspan="2"><?php echo($lang['END']);?></td>
			<td rowspan="2"><?php echo($lang['AVAIL_OPS']);?></td>
		</tr>
		<tr>
			<td><?php echo($lang['DAY']);?></td>
			<td><?php echo($lang['HOUR']);?></td>
			<td><?php echo($lang['DAY']);?></td>
			<td><?php echo($lang['HOUR']);?></td>
		</tr>
<?php //** database access

$database->query(getQuery('regular_presence','START_DAY, START_HOUR',$otherInst,$authNum));
while($row=$database->fetch())
{
    $start_time=new ofDateSpan($row->START_DAY.' '.$row->START_HOUR);
    $end_time=new ofDateSpan($row->END_DAY.' '.$row->END_HOUR);
?>		<tr>
			<td><?php echo($row->SIGN);?></td>
			<td><?php echo($start_time->getWeekDay());?></td>
			<td><?php echo(display_time($row->START_HOUR));?></td>
			<td><?php echo($end_time->getWeekDay());?></td>
			<td><?php echo(display_time($row->END_HOUR));?></td>
			<td>
				<form action="index.php" method="post">
				<div>
					<input type="hidden" name="menu" value="4"/>
					<input type="hidden" name="sub_menu" value="4"/>
					<input type="hidden" name="exceptionnalSlot" value="0"/>
					<input type="hidden" name="old_instructor" value="<?php echo($row->INST_NUM);?>"/>
					<input type="hidden" name="ts_old_start_time" value="<?php echo($start_time->getNNSV());?>"/>
					<input type="hidden" name="ts_old_end_time" value="<?php echo($end_time->getNNSV());?>"/>
					<input type="image" src="img/b_edit.png" alt="<?php echo($lang['MODIFY']);?>" onclick="submit();"/>
					&nbsp;&nbsp;
					<input type="image" src="img/b_drop.png" alt="<?php echo($lang['DELETE']);?>" onclick="if(confirm('<?php echo($lang['PERIOD_CONFIRM_ERASE']);?>')){this.form.sub_menu.value=13; submit();}else{return(false);};"/>
				</div>
				</form>
			</td>
		</tr>
<?php
}
$database->free();
?>	</table>


	<div class="MINI_TITLE_FONT"><br /><?php echo($lang['REST_EXCEP_ATTENDANCE']);?><br /><br /></div>
	<table class="listing">
        <tr class="TABLE">
            <td colspan="4">
                <form action="index.php" method="post">
                <div>
                    <input type="hidden" name="menu" value="4"/>
                    <input type="hidden" name="sub_menu" value="2"/>
                    <input type="hidden" name="other_inst" value="<?php echo($otherInst);?>"/>
                    <input type="checkbox" name="all_slots" <?php if($all_slots){?>checked="checked"<?php ;}?> onclick="submit()"/>&nbsp;<?php echo($lang['SHOW_EXPIRED']);?>
                </div>
                </form>
            </td>
        </tr>
		<tr>
			<td><?php echo($lang['INSTRUCTOR']);?></td>
			<td><?php echo($lang['REST_AVAIL_TYPE']);?></td>
			<td><?php echo($lang['PERIOD']);?></td>
			<td><?php echo($lang['AVAIL_OPS']);?></td>
		</tr>
<?php  //** database access
$frenchDisplay=$userSession->isFrenchDateDisplay();
$timezone=$userSession->getTimeZone();
$database->query(getQuery('exceptionnal','START_DATE',$otherInst,$authNum,$all_slots));
while($row=$database->fetch())
{
    $start_date=new ofDate($row->START_DATE);
    $end_date=new ofDate($row->END_DATE);
?>		<tr>
			<td><?php echo($row->SIGN);?></td>
			<td><?php echo($presence_array[$row->PRESENCE]);?></td>
			<td><?php echo($start_date->displayDateGap($end_date,$timezone,$frenchDisplay));?></td>
			<td>
				<form action="index.php" method="post">
				<div>
					<input type="hidden" name="menu" value="4"/>
					<input type="hidden" name="sub_menu" value="4"/>
					<input type="hidden" name="exceptionnalSlot" value="1"/>
					<input type="hidden" name="old_instructor" value="<?php echo($row->INST_NUM);?>"/>
					<input type="hidden" name="old_presence" value="<?php echo($row->PRESENCE);?>"/>
					<input type="hidden" name="ts_old_start_date" value="<?php echo($start_date->getTS());?>"/>
					<input type="hidden" name="ts_old_end_date" value="<?php echo($end_date->getTS());?>"/>
					<input type="image" src="img/b_edit.png" alt="<?php echo($lang['MODIFY']);?>" onclick="submit();"/>
					&nbsp;&nbsp;
					<input type="image" src="img/b_drop.png" alt="<?php echo($lang['DELETE']);?>" onclick="if(confirm('<?php echo($lang['PERIOD_CONFIRM_ERASE']);?>')){this.form.sub_menu.value=13; submit();}else{return(false);};"/>
				</div>
				</form>
			</td>
		</tr>
<?php
}
$database->free();
?>	</table>
<?php
$askForm=new requestForm('','','Simple');
$askForm->addHidden('menu',4);
$askForm->addHidden('sub_menu',3);
$askForm->close($lang['ADD_PERIOD']);

require_once('./includes/footer.php');
?>
