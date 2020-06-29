<?PHP
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
 * @version    CVS: $Id: clubGenOpeTPL.php,v 1.30.2.3 2006/05/21 09:41:19 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

/*
$myTemplate new basicTemplate();
$myTemplate->assign("SUBTITLE",$lang[$admin_subtitle]);
$myTemplate->assign("FORM_NAME","form_place");
$myTemplate->assign("FORM_ENCTYPE","multipart/form-data");
$myTemplate->assign("FORM_OBJECT_TYPE","profile");
$myTemplate->assign("FORM_OPE","db");
$myTemplate->assign("FORM_REF",$currentProfile->reference); */
?>
<br />
<h1><?php echo(''); ?></h1>
<br />
<div align="center">
<form action="index.php" method="post" enctype="multipart/form-data" name="form_place">
<input type="hidden" name="type" value="club" />
<input type="hidden" name="ope" value="db" />
<?php
if (isset($ref))
{ ?>
<input type="hidden" name="ref" value="<?php echo($ref); ?>" /><?php
} ?>
<table class="form_gen" style="width: 92%; font-size: 11px;">
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_NAME']); ?></td>
		<td class="form_cell"><input type="text" name="club_name" value="<?php echo($currentClub->name[$myVar]); ?>" /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_NAME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_LANGUAGE']); ?></td>
		<td class="form_cell"> <select name="organisation_language" size="1">
		<?php echo($currentClub->listLanguage($myVar)); ?>
		</select> 
		</td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_LANGUAGE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_TIMEZONE']); ?></td>
		<td><select name="organisation_timezone" size="1">
			<?php
			while($timezone_list = each($_DATE_TIMEZONE_DATA)) {
			?>	<option <?php if($timezone_list['key'] == $currentClub->timezone[$myVar]){?>selected="selected" <?php ;}?>value="<?php echo($timezone_list['key']);?>"><?php echo(floor($timezone_list['value']['offset']/3600000).' '.$timezone_list['key']);?></option>
	<?php	}
	?>		</select>
		</td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_TIMEZONE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['CURRENT_LOGO']); ?></td>
		<td class="form_cell" colspan="2"><img src="img/logo.php" alt="logo"></td>
	</tr> 
	<tr>
		<td class="form_cell"><?php echo($lang['ICAO_CODE']); ?></td>
		<td class="form_cell"><?php echo($currentClub->showICAO($myVar)); ?></td>
		<td class="form_cell"><?php echo($lang['ICAO_CODE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><?php echo($lang['ICAO_CODE_NOT_AVAILABLE']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRFIELD_NAME']); ?></td>
		<td class="form_cell"><input type="text" name="airfield_name" maxlength="64" /></td>
		<td class="form_cell"><?php echo($lang['AIRFIELD_NAME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRFIELD_ICAO_CODE']); ?></td>
		<td class="form_cell"><input type="text" name="airfield_oaci" maxlength="6" /></td>
		<td class="form_cell"><?php echo($lang['AIRFIELD_ICAO_CODE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRFIELD_LAT']); ?></td>
		<td class="form_cell"><input type="text" name="airfield_lat" maxlength="7" /></td>
		<td class="form_cell"><?php echo($lang['AIRFIELD_LAT_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRFIELD_LONG']); ?></td>
		<td class="form_cell"><input type="text" name="airfield_long" maxlength="8" /></td>
		<td class="form_cell"><?php echo($lang['AIRFIELD_LONG_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRFIELD_ALT']); ?></td>
		<td class="form_cell"><input type="text" name="airfield_alt" maxlength="7" /></td>
		<td class="form_cell"><?php echo($lang['AIRFIELD_ALT_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><?php echo($lang['CELL_INFO_CONTENT']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3"><textarea name="club_text_cell" cols="60" rows="5"><?php echo($currentClub->infoCell[$myVar]); ?></textarea></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_LOGO']); ?></td>
		<td class="form_cell"><input type="file" name="club_logo_picture" value="<?php echo($currentClub->logo_name[$myVar].'.'.$currentClub->logo_ext[$myVar]); ?>" /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_LOGO_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MANAGER']); ?></td>
		<td class="form_cell"><select name="organisation_manager"><?php echo($currentUser->listAdmin($currentClub->adminNum[$myVar])); ?></select></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MANAGER_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_WEBSITE_URL']); ?></td>
		<td class="form_cell"><input type="text" name="website_URL" value="<?php echo($currentClub->website_url[$myVar]); ?>" /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_WEBSITE_URL_EXPLANATION']); ?></td>
	</tr>
<?php
$same_day_box = $currentClub->sameDayBox($myVar);
$comment_book = $currentClub->bookComment($myVar);
?>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_SAME_DAY_BOX']); ?></td>
		<td class="form_cell"><input type="checkbox" name="same_day_box" value="1" <?php if ($same_day_box)
																			{
																				echo('CHECKED');
																			} ?> /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_SAME_DAY_BOX_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_BOOKING_COMMENTS']); ?></td>
		<td class="form_cell"><input type="checkbox" name="book_comment" value="2" <?php if ($comment_book)
																			{
																				echo('CHECKED');
																			} ?> /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_BOOKING_COMMENTS_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_START_HOUR']); ?></td>
		<td class="form_cell"><?php echo($hour_begin); ?>:<?php echo($minute_begin); ?></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_START_HOUR_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_END_HOUR']); ?></td>
		<td class="form_cell"><?php echo($hour_end); ?>:<?php echo($minute_end); ?></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_END_HOUR_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MIN_SLOT']); ?></td>
		<td class="form_cell"><select name="min_book" size="1">
		<?php for ($minSlotI=1; $minSlotI<13; $minSlotI++) {
            ?><option value="<?php echo($minSlotI*15);?>" <?php
            if ($currentClub->min_slot[$myVar]==($minSlotI*15))	{
                echo('selected');
            }
            echo('>'.($minSlotI*15).' minutes</option>');
		}
		?></select></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MIN_SLOT_EXPLANATION']); ?></td>
	</tr>
	<?php
	$les_heures= floor($currentClub->default_slot[$myVar]/60);
	$les_minutes = $currentClub->default_slot[$myVar] - $les_heures * 60;
	?>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_DEFAULT_SLOT']); ?></td>
		<td class="form_cell"><select name="default_book_hours" size="1">
			<?php 	for ($boucle = 0; $boucle < 12; $boucle++)
				{
					if ($boucle==$les_heures)
					{
						echo('<option value="'.$boucle.'" selected>'.$boucle.'</option>');
					}
					else
					{
						echo('<option value="'.$boucle.'">'.$boucle.'</option>');
					}
				} ?>
		</select>
		<select name="default_book_minutes" size="1">
			<option value="0" <?php if ($les_minutes==0) 	{ echo('selected'); } ?>>00</option>
			<option value="15" <?php if ($les_minutes==15) 	{ echo('selected'); } ?>>15</option>
			<option value="30" <?php if ($les_minutes==30) 	{ echo('selected'); } ?>>30</option>
			<option value="45" <?php if ($les_minutes==45) 	{ echo('selected'); } ?>>45</option></select></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_DEFAULT_SLOT_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_DEFAULT_PROFILE']); ?></td>
		<td class="form_cell"><?php echo($choixProfile); ?></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_DEFAULT_PROFILE_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MAILER_ADDRESS']); ?></td>
		<td class="form_cell"><input type="text" name="mailer" value="<?php echo($currentClub->mailer[$myVar]); ?>" /></td>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MAILER_ADDRESS_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MAILING_LIST_NAME']); ?></td>
		<td class="form_cell"><input type="text" name="mailing_list_name" value="<?php echo($currentClub->mlName[$myVar]); ?>" /></td>
		<td class="form_cell" rowspan="2"><?php echo($lang['ORGANISATION_MAILING_LIST_NAME_EXPLANATION']); ?></td>
	</tr>
	<tr>
		<td class="form_cell"><?php echo($lang['ORGANISATION_MAILING_LIST_TYPE']); ?></td>
		<td class="form_cell"> <select name="mailing_list_type" size="1">
		<?php require_once('./admin/select_mailing.php'); ?>
		</select> 
		</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;"><input type="submit" value="<?php echo($lang['VALIDATE']); ?>" /></td>
	</tr>
</table>
</form>
</div>
<p>
<a href="index.php?menu=7" class="dblink"> &nbsp;<?php echo($lang['BACK']); ?>&nbsp; </a>
</p>