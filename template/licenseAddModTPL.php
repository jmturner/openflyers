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
 * @version    CVS: $Id: licenseAddModTPL.php,v 1.3.2.3 2005/11/30 21:21:00 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>

<br />
<div align="center">
<form action="index.php" method="post" enctype="multipart/form-data" name="form_place">
<input type="hidden" name="type" value="license" />
<input type="hidden" name="ope" value="db" />
<?php
if (isset($ref))
{ ?>
<input type="hidden" name="ref" value="<?php echo($ref); ?>" /><?php
} ?>
<table class="form_gen" style="width: 92%; font-size: 11px;">
	<tr>
		<td class="form_cell"><?php echo($lang['LICENSE_NAME']); ?></td>
		<td class="form_cell"><input type="text" name="license_name" value="<?php echo($currentLicense->name); ?>" /></td>
		<td class="form_cell"><?php echo($lang['LICENSE_NAME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['LICENSE_TIMELIMIT']); ?></td>
		<td class="form_cell"><input type="checkbox" name="license_timelimit" value="1" <?php if ($currentLicense->time_limit==1)
																			{
																				echo('checked="checked"');
																			} ?> /></td>
		<td class="form_cell"><?php echo($lang['LICENSE_TIMELIMIT_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;"><input type="submit" value="<?php echo($lang['VALIDATE']); ?>" /></td>
	</tr>
</table>
</form>
</div>
<p>
<a href="index.php?type=license&ope=manage" class="dblink"> &nbsp;<?php echo($lang['BACK']); ?>&nbsp; </a>
</p>