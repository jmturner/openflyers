<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * addmod_profile.content.php
 *
 * administration interface
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
 * @category   Admin interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: licenseEndTableHeaderTPL.php,v 1.4.2.1 2005/10/28 17:44:23 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>
<tr>
	<td class="highlighted">
		<?php echo(stripslashes($currentLicense->name)); ?>
	</td>
	<td class="highlight">
		<?php 	if (1 == $currentLicense->time_limit)
				{
					echo('x');
				}
				else
				{
					echo(' ');
				} ?>
	</td>
	<td>
		<form action="index.php" method="post" >
		<input type="hidden" name="type" value="license">
		<input type="hidden" name="ope" value="modify">
		<input type="hidden" name="ref" value="<?php echo($currentLicense->reference); ?>">
		<input type="image" src="img/modify.gif" alt="<?php echo($lang['MODIFY']); ?>">
		</form>
	</td>
	<td>
		<form action="index.php" method="post" onsubmit="return confirm('<?php echo($lang['JS_LICENSE_DELETE_WARNING']); ?>')">
		<input type="hidden" name="type" value="license">
		<input type="hidden" name="ope" value="destroy">
		<input type="hidden" name="ref" value="<?php echo($currentLicense->reference); ?>">
		<input type="image" src="img/destroy.gif" alt="<?php echo($lang['DELETE']); ?>">
		</form>
	</td>
</tr>