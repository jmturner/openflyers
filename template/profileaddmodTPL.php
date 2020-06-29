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
 * @version    CVS: $Id: profileaddmodTPL.php,v 1.15.2.5 2006/07/01 06:33:00 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>
<div align="center">
<form action="index.php" method="post">
<input type="hidden" name="type" value="profile">
<input type="hidden" name="ope" value="db">
<input type="hidden" name="ref" value="<?php echo($currentProfile->reference) ?>">
<table class="form_gen" style="font-size: 12px;">
	<tr>
		<td class="form_cell"><?php echo($lang['PROFILE_NAME']); ?></td>
		<td class="form_cell"><input type="text" name="profile_name" value="<?php echo($currentProfile->name[$myVar]); ?>" /></td>
		<td class="form_cell"><?php echo($lang['PROFILE_NAME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td colspan="3" style="background: #cccccc;"><?php echo($lang['PROFILE_PERMITS']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['NO_AUTO_LOGOUT']); ?></td>
		<td><input type="checkbox" name="profile_no_auto_logout" value="16777216" <?php if ($currentProfile->isNoAutoLogout()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['NO_AUTO_LOGOUT_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['BOOK_ANYTIME']); ?></td>
		<td><input type="checkbox" name="profile_book_anytime" value="1" <?php if ($currentProfile->isBookAnytime()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_ANYTIME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['BOOK_ANYDURATION']); ?></td>
		<td><input type="checkbox" name="profile_book_anyduration" value="8388608" <?php if ($currentProfile->isBookAnyduration()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_ANYDURATION_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['BOOK_ALONE']); ?></td>
		<td><input type="checkbox" name="profile_book" value="2" <?php if ($currentProfile->isBookAlone()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_ALONE_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
		<td><?php echo($lang['BOOK_WITH_INSTRUCTOR']); ?></td>
		<td><input type="checkbox" name="profile_book_inst" value="4" <?php if ($currentProfile->isBookInstructor()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_WITH_INSTRUCTOR_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
		<td><?php echo($lang['BOOK_OVERRIDE']); ?></td>
		<td><input type="checkbox" name="profile_book_unfree_inst" value="32" <?php if ($currentProfile->isBookUnfreeInstructor()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_OVERRIDE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['MANAGE_AIRCRAFT']); ?></td>
		<td><input type="checkbox" name="profile_freeze_aircraft" value="8" <?php if ($currentProfile->isFreezeAircraft()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['MANAGE_AIRCRAFT_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
	    <td><?php echo($lang['MANAGE_INSTRUCTORS']); ?></td>
		<td><input type="checkbox" name="profile_freeze_inst" value="16" <?php if ($currentProfile->isFreezeInstructor()) { echo('CHECKED');	} ?> /></td>
	    <td><?php echo($lang['MANAGE_INSTRUCTORS_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
		<td><?php echo($lang['MANAGE_USER']); ?></td>
		<td><input type="checkbox" name="profile_users" value="64" <?php if ($currentProfile->isSetPilotsFile()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['MANAGE_USER_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['MANAGE_MYSELF']); ?></td>
		<td><input type="checkbox" name="profile_self" value="128" <?php if ($currentProfile->isSetOwnQualifications()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['MANAGE_MYSELF_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
		<td><?php echo($lang['MANAGE_ORGANISATION']); ?></td>
		<td><input type="checkbox" name="profile_club" value="256" <?php if ($currentProfile->isSetClubParameters()) { echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['MANAGE_ORGANISATION_EXPLANATION']); ?></td>
	</tr>	
	<tr>
		<td><?php echo($lang['MANAGE_AIRCRAFTS']); ?></td>
		<td><input type="checkbox" name="profile_aircraft" value="512" <?php if ($currentProfile->isSetAircraftsFile()) { echo('CHECKED');	} ?> /></td>
		<td><?php echo($lang['MANAGE_AIRCRAFTS_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td><?php echo($lang['MANAGE_LIMITS']); ?></td>
		<td><input type="checkbox" name="profile_limits" value="1024" <?php if ($currentProfile->isSetOwnLimitationsAllowed()) { echo('CHECKED');	} ?> /></td>
		<td><?php echo($lang['MANAGE_LIMITS_EXPLANATION']); ?></td>
	</tr>
	<tr class="highlight_prof">
		<td><?php echo($lang['BOOK_EVERYBODY']); ?></td>
		<td><input type="checkbox" name="profile_cnl" value="2048" <?php if ($currentProfile->isEverybodyBook())	{ echo('CHECKED'); } ?> /></td>
		<td><?php echo($lang['BOOK_EVERYBODY_EXPLANATION']) ?></td>
	</tr>	
	<tr>
		<td colspan="3" style="text-align: center;"><input type="submit" value="<?php echo($lang['VALIDATE']); ?>" /></td>
	</tr>
</table>
</form>
</div>
<p><a href="index.php?type=profile&ope=manage" class="dblink"> &nbsp;<?php echo($lang['BACK']); ?>&nbsp; </a></p>