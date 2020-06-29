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
 * @version    CVS: $Id: profileTableTPL.php,v 1.9.2.4 2006/07/01 06:33:00 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>
<tr>
	<td class="highlighted"><?php echo($currentProfile->name['init']); ?></td>
	<td class="highlight">
		<?php if ($currentProfile->isNoAutoLogout()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isBookAnytime()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isBookAnyduration()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isBookAlone()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlighted">
		<?php if ($currentProfile->isBookInstructor()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isBookUnFreeInstructor()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlighted">
		<?php if ($currentProfile->isFreezeAircraft()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isFreezeInstructor()) { echo('&nbsp;X&nbsp;');	} ?>
	</td>
	<td class="highlighted">
		<?php if ($currentProfile->isSetPilotsFile()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isSetOwnQualifications()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlighted">
		<?php if ($currentProfile->isSetClubParameters()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isSetAircraftsFile()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlighted">
		<?php if ($currentProfile->isSetOwnLimitationsAllowed()) { echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td class="highlight">
		<?php if ($currentProfile->isEverybodyBook())	{ echo('&nbsp;X&nbsp;'); } ?>
	</td>
	<td>
		<form action="index.php" method="post">
		<input type="hidden" name="type" value="profile">
		<input type="hidden" name="ope" value="modify">
		<input type="hidden" name="ref" value="<?php echo($currentProfile->reference); ?>">
		<input type="image" src="img/modify.gif" alt="<?php echo($lang['MODIFY']); ?>">
		</form>
	</td>
	<td>
		<form action="index.php" method="post"  onsubmit="return confirm('<?php echo($lang['DELETE_PROFILE_WARNING']); ?>')">
		<input type="hidden" name="type" value="profile">
		<input type="hidden" name="ope" value="destroy">
		<input type="hidden" name="ref" value="<?php echo($currentProfile->reference); ?>">
		<input type="image" src="img/destroy.gif" alt="<?php echo($lang['DELETE']); ?>">
		</form>
	</td>
</tr>