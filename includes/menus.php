<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * menus.php
 *
 * display menu and submenu. the display manner depends of prefs
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
 * @version    CVS: $Id: menus.php,v 1.49.2.3 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 3 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// $instructorsClass, $aircraftsClass, $menuNames, $subMenuNames arrays, 
// $menuSize, $menu and $sub_menu, $firstDisplayedDate should be set
if (!isset($onlyDate))
{
    $onlyDate=$firstDisplayedDate;
    $onlyDate->clearClock();
}
?>
<form id="values" action="index.php" method="post"><div class="lightSpacer">
<input type="hidden" name="menu" value="3"/>
<input type="hidden" name="sub_menu" value=""/>
<input type="hidden" name="bookId" value=""/>
<input type="hidden" name="offset_day" value=""/>
<input type="hidden" name="offset_hour" value=""/>
<input type="hidden" name="tsStartDate" value="<?php echo($onlyDate->getTS());?>"/>
<input type="hidden" name="aircraft" value="<?php
if($menu==1)
{
	$item=$aircraftsClass->get_viewed_value($sub_menu);
	echo ($item->NUM);
}?>"/>
<input type="hidden" name="instructor" value="<?php
if($menu==2)
{
	$item=$instructorsClass->get_viewed_value($sub_menu);
	echo $item->NUM;
}
else
{
	echo '0';
}?>"/>
</div></form>
    <div id="conteneurmenu">
<script type="text/javascript">
    preChargement();
</script><?php
for ($i=0;$i<$menuSize;$i++)
{
    if (isset($menuNames[$i]))
    {
        ?><p id="menu<?php echo($i+1);?>" class="menu" onmouseover="MontrerMenu('ssmenu<?php echo($i+1);?>');" onmouseout="CacherDelai();">
        <a href="#" onclick="submit_menu('<?php echo($menuOpe[$i]);?>','')"<?php
        if($menu==$i)
        {
            ?> class="active"<?php
        }
        ?> onfocus="MontrerMenu('ssmenu<?php echo($i+1);?>');"<?php
        ?>><?php
        echo(stripslashes($menuNames[$i]));
        ?><span>&nbsp;;</span></a></p><?php
        $subMenuSize    = sizeof($subMenuNames[$i]);
        $rowQty         = ceil($subMenuSize/30);
        $index          = 0;
        if ($subMenuSize > 0) {
            ?><ul id="ssmenu<?php echo($i+1);?>" class="ssmenu" onmouseover="AnnulerCacher();" onmouseout="CacherDelai();"><?php
            foreach ($subMenuNames[$i] as $j=>$currentSubMenu)
            {
                if (fmod($index,$rowQty)==0) {
                    ?><li><?php
                }
                ?><a href="#" onclick="submit_menu('<?php echo($menuOpe[$i].'\',\''.$j);?>')"<?php
                if ($currentSubMenu->get_popup()!='') {
                    echo(' title="'.$currentSubMenu->get_popup().'"');
                }
                if (($sub_menu==$j)and($menu==$i)) {
                    ?> class="active"<?php
                }
                ?>><?php
                echo(stripslashes($currentSubMenu->getLabel()));

                if (fmod($index,$rowQty)==($rowQty-1)) {
                    ?><span>&nbsp;;</span></a><?php
                    ?></li><?php
                }
                $index++;
            }
            if (fmod($index-1,$rowQty)!=($rowQty-1)) {
                ?><span>&nbsp;;</span></a><?php
                ?></li><?php
            }
            ?></ul><?php
        }
    }
}
?>
  </div>
<script type="text/javascript">
var nbmenu = <?php echo($menuSize);?>;
var centrer_menu = true;
var largeur_menu = new Array(<?php
for ($i=0;$i<$menuSize;$i++)
{
    echo(strlen($menuNames[$i])*8+24);
    if ($i<($menuSize-1))
    {
        echo(',');
    }
}?>);
var largeur_sous_menu = new Array(<?php
for ($i=0;$i<$menuSize;$i++) {
    $maxLen         = 0;
    $subMenuSize    = sizeof($subMenuNames[$i]);
    $index          = 0;
    $currentLen     = 0;
    $rowQty         = ceil($subMenuSize/30);
    foreach ($subMenuNames[$i] as $j=>$currentSubMenu) {
        if (fmod($index,$rowQty)==0) {
            $currentLen = 2+strlen($currentSubMenu->getLabel());
        }
        else {
            $currentLen += 2+strlen($currentSubMenu->getLabel());
        }
        if ($maxLen < $currentLen) {
            $maxLen = $currentLen;
        }
        $index++;
    }
    echo(($maxLen+2)*8);
    if ($i<($menuSize-1)) {
        echo(',');
    }
}?>);
    Chargement();
</script>
