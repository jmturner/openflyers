<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * listMembers.php
 *
 * Display members list
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
 * @version    CVS: $Id: listMembers.php,v 1.3.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Fri Mar 21 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

$userSession->setOldMenus($menu,$sub_menu);

///////////////////////////////////// HTML DISPLAY START HERE ////////////////////////////////////////////////////
require_once('./includes/header.php');
?></head><body><?php
require_once('./includes/menus.php');

if($userSession->isNothingAllowed())
{
    ?><table class="listing"><thead><tr><th><?php echo($lang['LIST_MEMBERS_NO_PUBLIC_ACCESS']);?></th></tr></thead></table><?php
}
else
{
	$database->query('select * from profiles');
	for($profile_size=0;$row=$database->fetch();$profile_size++)
	{
		$profile_name[$profile_size]=$row->NAME;
		$profile_num[$profile_size]=intval($row->NUM);
	}
	$database->free();
	
	$query='select authentication.*, instructors.INST_NUM, members.NUM as M_NUM from authentication 
			left join instructors on authentication.NUM=instructors.INST_NUM 
			left join members on authentication.NUM=members.NUM order by ';
	if($sub_menu==1)
	{
		$query=$query.' PROFILE';
	}
	else
	{
		$query=$query.' LAST_NAME';
	}

	$database->query($query);

?><table class="listing"><thead><tr>
<th class="lastNameMemberTitle"><?php echo($lang['LASTNAME']); ?></th>
<th class="firstNameMemberTitle"><?php echo($lang['FIRSTNAME']); ?></th>
<th class="profileMemberTitle"><?php echo($lang['PROFILE']); ?></th>
<th class="statusMemberTitle"><?php echo($lang['STATUS']); ?></th>
<th class="emailMemberTitle"><?php echo($lang['EMAIL']); ?></th>
<th class="homePhoneMemberTitle"><?php echo($lang['HOME_PHONE']); ?></th>
<th class="workPhoneMemberTitle"><?php echo($lang['WORK_PHONE']); ?></th>
<th class="cellPhoneMemberTitle"><?php echo($lang['CELL_PHONE']); ?></th>
</tr></thead><tbody><?php

	for($i=0;$row=$database->fetch();$i++)
	{
        ?><tr><td>&nbsp;<?php echo(stripslashes($row->LAST_NAME));?>&nbsp;</td>
              <td>&nbsp;<?php echo(stripslashes($row->FIRST_NAME));?>&nbsp;</td>
              <td><?php
        for($j=0;$j<$profile_size;$j++)
        {
            if($profile_num[$j]&intval($row->PROFILE))
            {
                echo(stripslashes($profile_name[$j]));?>&nbsp;<?php
            }
        }
        ?></td><td>&nbsp;<?php

        if($row->INST_NUM)
        {
            ?>&nbsp;<?php echo($lang['INSTRUCTOR']); ?>&nbsp;<?php
        }
        if($row->M_NUM)
        {
            ?>&nbsp;<?php echo($lang['MEMBER']); ?>&nbsp;<?php
        }
        ?>&nbsp;</td><td class="PILOT"><?php
        if(($row->EMAIL)&&(isPublicEmail($row->VIEW_TYPE)))
        {
            ?><a href="mailto:<?php echo($row->EMAIL);?>"><?php echo($row->EMAIL);?></a><?php
        }
        else
        {
            ?>&nbsp;<?php
        }
        ?></td><td>&nbsp;<?php
        if(isPublicHomePhone($row->VIEW_TYPE))
        {
            echo(stripslashes($row->HOME_PHONE));
        }
        ?>&nbsp;</td><td>&nbsp;<?php
        if(isPublicWorkPhone($row->VIEW_TYPE))
        {
		    echo(stripslashes($row->WORK_PHONE));
        }
        ?>&nbsp;</td><td>&nbsp;<?php
        if(isPublicCellPhone($row->VIEW_TYPE))
        {
		    echo(stripslashes($row->CELL_PHONE));
        }
        ?>&nbsp;</td></tr><?php
    }
    $database->free();
?></tbody></table><?php
}

require_once('./includes/footer.php');
?> 