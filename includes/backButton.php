<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * backButton.php
 *
 * Display a backButton
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
 * @version    CVS: $Id: backButton.php,v 1.3.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat Apr 05 2003
 */

if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// we assume that $firstDisplayedDate and $userSession are well defined

?><form id="back" action="index.php" method="post">
<div>
<input type="hidden" name="menu" value="<?php echo($userSession->getOldMenu());?>"/>
<input type="hidden" name="sub_menu" value="<?php echo($userSession->getOldSubMenu());?>"/>
<input type="hidden" name="ts_old_start_date" value="<?php echo($firstDisplayedDate->getTS());?>"/>
<input name="validation" type="submit" value="<?php echo($lang['BACK_BUTTON']); ?>"/>
</div>
</form>
