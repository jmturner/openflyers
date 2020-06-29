<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageQualif.php
 *
 * Manage own qualifications
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
 * @version    CVS: $Id: manageQualif.php,v 1.14.2.4 2006/06/19 14:49:29 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Sep 22 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// We assume that $userSession and $database are well defined

$userSession->setOldMenus($menu,$sub_menu);

$frenchDisplay=$userSession->isFrenchDateDisplay();
$timezone=$userSession->getTimeZone();
$tzID=$timezone->getID();
$qualifAlertDelay=$userSession->getQualifAlertDelay();

$isSetOwnQualif=$userSession->isSetOwnQualifAllowed();
$isSetOwnLimitations=$userSession->isSetOwnLimitationsAllowed();

///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
require_once('./includes/header.php');
?></head><body><?php
require_once('./includes/menus.php');

?><div class="mainRow">
<ul class="shortDesc"><?php
echo('<li>'.$userSession->getClubName().'</li><li>'.$lang['CONNECTED'].'&nbsp;:&nbsp;'.stripslashes($userSession->getFirstName()).' '.stripslashes($userSession->getLastName()).'</li><li>('.stripslashes($userSession->get_profile_name()).')</li>'); ?>
</ul>
<br class="spacer"/>
</div>
<?php
	if($userSession->isMember())
	{
	    ?><table class="listing"><?php
	    $database->query('select QUALIFID, NAME, TIME_LIMITATION, EXPIREDATE from qualification
	                      left join member_qualif on QUALIFID=ID 
	                      where MEMBERNUM='.$userSession->getAuthNum().' order by NAME');
	    if ($database->numRows()==0)
	    {
	        ?><tr><td colspan="2"><?php echo($lang['QUALIF_NO_QUALIF']);?><br /><br /></td></tr><?php
	    }

	    $result=$database->fetch();
	    while ($result)
	    {
        ?><tr>
            <td align="right"><?php echo($result->NAME);?>&nbsp;:&nbsp;</td>
            <td align="left"><?php
            if ($result->TIME_LIMITATION)
            {
                $date=new ofDate($result->EXPIREDATE);
                if (($isSetOwnLimitations)or($isSetOwnQualif))
            {?>
                <form id="qualif<?php echo($result->QUALIFID);?>" action="index.php" method="post">
                <div>
                    <input type="hidden" name="qualifID" value="<?php echo($result->QUALIFID);?>" />
                    <input type="hidden" name="menu" value="4" />
                    <input type="hidden" name="sub_menu" value="11" />
                    <input type="hidden" name="action" value="update" />
                    <select name="day"><?php
                    for($i=1;$i<=31;$i++)
                    {
                        ?><option <?php if($date->getDay()==$i){?>selected="selected" <?php ;}?>value="<?php echo(sprintf("%'02d",$i));?>"><?php echo(sprintf("%'02d",$i));?></option><?php
                    }
                    ?></select>/<select name="month"><?php
                    for($i=1;$i<=12;$i++)
                    {
                        ?><option <?php if($date->getMonth()==$i){?>selected="selected" <?php ;}?>value="<?php echo(sprintf("%'02d",$i));?>"><?php echo(sprintf("%'02d",$i));?></option><?php
                    }
                    ?></select>/<select name="year"><?php
                    for($i=($date->getYear()-5);$i<=($date->getYear()+5);$i++)
                    {
                        ?><option <?php if($date->getYear()==$i){?>selected="selected" <?php ;}?>value="<?php echo($i);?>"><?php echo($i);?></option><?php
                    }
                ?></select>
                &nbsp;&nbsp;<input type="image" src="img/reload.png" alt="<?php echo($lang['UPDATE']);?>" onclick="submit();" />
                </div></form><?php       
            }
            else
            {
                echo($date->displayDate($timezone,$frenchDisplay));
            }
            }
            else
            {
                echo($lang['QUALIF_NO_TIME_LIMIT']);
            }
            ?></td><?php
            if (($isSetOwnLimitations)or($isSetOwnQualif))
            {
            ?><td align="left">
                    <form id="dropqualif<?php echo($result->QUALIFID);?>" action="index.php" method="post">
                    <div>
                        <input type="hidden" name="qualifID" value="<?php echo($result->QUALIFID); ?>"/>
                        <input type="hidden" name="menu" value="4"/>
                        <input type="hidden" name="sub_menu" value="11"/>
                        <input type="hidden" name="action" value="destroy"/><?php       
                        if ($isSetOwnQualif)
                        {
                            ?>&nbsp;&nbsp;<input type="image" src="img/b_drop.png" alt="<?php echo($lang['DELETE']);?>" onclick="if(confirm('<?php echo($lang['QUALIF_CONFIRM_ERASE']);?>')){submit();}else{return(false);};" /><?php
                        }
                        ?></div></form></td><?php
            }
            ?></tr><?php
            $result=$database->fetch();
	    }
	    ?></table><?php
	    $database->free();
	    $database->query('select ID, NAME from qualification
                      LEFT JOIN member_qualif ON qualification.ID=member_qualif.QUALIFID and member_qualif.MEMBERNUM=\''.$userSession->getAuthNum().'\' 
                      where member_qualif.QUALIFID is NULL 
                      order by NAME');
	    if (($isSetOwnQualif)and($database->numRows()))
	    {
        ?>
                    <form id="addQualif" action="index.php" method="post">
                    <div class="centeredFormSimple"><?php echo($lang['QUALIF_OTHER_QUALIF']);?>&nbsp;: 
                        <input type="hidden" name="menu" value="4"/>
                        <input type="hidden" name="sub_menu" value="11"/>
                        <input type="hidden" name="action" value="add"/>
                        <select name="qualifID"><?php
                        $result=$database->fetch();
                        while ($result)
                        {
                            ?><option value="<?php echo($result->ID); ?>"><?php echo($result->NAME);?></option><?php
                            $result=$database->fetch();
                        }
                        ?></select><input type="submit" value="<?php echo($lang['ADD']);?>"/></div></form><?php
	    }
        ?><form id="modDelay" action="index.php" method="post">
        <div class="centeredFormSimple"><?php echo($lang['QUALIF_ALERT']);?>&nbsp;:
        <input type="hidden" name="menu" value="4"/>
        <input type="hidden" name="sub_menu" value="11"/>
        <input type="hidden" name="action" value="modDelay"/>
        <select name="delay"><?php
        for($i=1;$i<=30;$i++)
        {
            ?><option <?php if($qualifAlertDelay==$i){?>selected="selected" <?php ;}?>value="<?php echo(sprintf("%'02d",$i));?>"><?php echo(sprintf("%'02d",$i));?></option><?php
        }
        ?></select>&nbsp;&nbsp;<input type="image" src="img/b_edit.png" alt="<?php echo($lang['UPDATE']);?>" onclick="submit();" />
        </div></form><?php
	}
	require_once('./includes/footer.php');
?>