<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * menu.php
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
 * @version    CVS: $Id: menu.php,v 1.31.2.4 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>
<div id="conteneurmenu">
<script language="Javascript" type="text/javascript">
// pour éviter le clignotement désagréable
preChargement();
</script>
<?php 
if ($userSession->is_set_club_parameters_allowed()) { ?>
	<p id="menu1" class="menu"
		onmouseover="MontrerMenu('ssmenu1');"
		onmouseout="CacherDelai();">
	<a href="index.php?ope=manage"
		onmouseover="MontrerMenu('ssmenu1');"
		onfocus="MontrerMenu('ssmenu1');"><?php echo($lang['ADMIN_MENU_HOME_HOME']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu1" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?ope=manage"><?php echo($lang['ADMIN_MENU_HOME']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php"><?php echo($lang['ADMIN_MENU_BACK_TO_BOOKINGS']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?menu=6"><?php echo($lang['ADMIN_MENU_DISCONNECT']); ?><span>&nbsp;;</span></a>
	</li>
	</ul>
    <p id="menu2" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu2');">
      <a href="index.php?ope=modify"
		onfocus="MontrerMenu('ssmenu2');"><?php echo($lang['ADMIN_MENU_CONFIGURATION']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu2" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=club&ope=modify"><?php echo($lang['ADMIN_MENU_CONFIGURE_ORGANISATION']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=license&ope=manage"><?php echo($lang['ADMIN_MENU_LIST_LICENSES']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=license&ope=add"><?php echo($lang['ADMIN_MENU_ADD_LICENSE']); ?></a>
	</li>
    </ul>
    <p id="menu3" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu3');">
      <a href="index.php?ope=manage"
		onfocus="MontrerMenu('ssmenu3');"><?php echo($lang['ADMIN_MENU_MANAGE_DATABASE']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu3" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=db&ope=csv"><?php echo($lang['ADMIN_MENU_CSV_EXPORT']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=db&ope=excel"><?php echo($lang['ADMIN_MENU_XLS_EXPORT']); ?></a>
	</li>
	<li>
		<a href="index.php?type=db&ope=backup"><?php echo($lang['ADMIN_MENU_SQL_BACKUP']); ?></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=import"><?php echo($lang['ADMIN_MENU_IMPORT_USERS']); ?></a>
	</li>
    </ul>
    <p id="menu4" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu4');">
	  <a href="index.php?ope=manage"
		onfocus="MontrerMenu('ssmenu4');"><?php echo($lang['ADMIN_MENU_MANAGE_AIRCRAFTS']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu4" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=aircraft&ope=manage"><?php echo($lang['ADMIN_MENU_LIST_AIRCRAFTS']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=aircraft&ope=add"><?php echo($lang['ADMIN_MENU_ADD_AIRCRAFT']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=aircraft&ope=rank"><?php echo($lang['ADMIN_MENU_AIRCRAFT_RANKING']); ?><span>&nbsp;;</span></a>
	</li>
	</ul>
    <p id="menu5" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu5');">
	  <a href="index.php?ope=manage"
		onfocus="MontrerMenu('ssmenu5');"><?php echo($lang['ADMIN_MENU_MANAGE_PROFILES']); ?><span>.</span></a>
    </p>
    <ul id="ssmenu5" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=profile&ope=manage"><?php echo($lang['ADMIN_MENU_LIST_PROFILES']); ?></a>
	</li>
	<li>
		<a href="index.php?type=profile&ope=add"><?php echo($lang['ADMIN_MENU_ADD_PROFILE']); ?></a>
	</li>
	</ul>
    <p id="menu6" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu6');">
      <a href="index.php?ope=manage"
		onfocus="MontrerMenu('ssmenu6');"><?php echo($lang['ADMIN_MENU_MANAGE_USERS']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu6" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=user&ope=manage&tri=NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_LOGIN']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=manage&tri=LAST_NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_LAST_NAME']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=manage&tri=FIRST_NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_FIRST_NAME']); ?></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=manage&tri=MEMBERS"><?php echo($lang['ADMIN_MENU_LIST_MEMBERS']); ?></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=manage&tri=INSTRUCTORS"><?php echo($lang['ADMIN_MENU_LIST_INSTRUCTORS']); ?></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=add"><?php echo($lang['ADMIN_MENU_MENU_ADD_USER']); ?></a>
	</li>
	<li>
		<a href="index.php?type=user&ope=rank"><?php echo($lang['ADMIN_MENU_RANK_USER']); ?></a>
	</li>
    </ul>
<?php } else { ?>
	<p id="menu1" class="menu"
		onmouseover="MontrerMenu('ssmenu1');"
		onmouseout="CacherDelai();">
	<a href="index.php?ope=manage"
		onmouseover="MontrerMenu('ssmenu1');"
		onfocus="MontrerMenu('ssmenu1');"><?php echo($lang['ADMIN_MENU_HOME_HOME']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu1" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?ope=manage"><?php echo($lang['ADMIN_MENU_HOME']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php"><?php echo($lang['ADMIN_MENU_BACK_TO_BOOKINGS']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?menu=6"><?php echo($lang['ADMIN_MENU_DISCONNECT']); ?><span>&nbsp;;</span></a>
	</li>
	</ul>
    <p id="menu2" class="menu">
      <a href="index.php?ope=modify">&nbsp;</span></a>
    </p>
    <ul id="ssmenu2" class="ssmenu">
    </ul>
    <p id="menu3" class="menu">
      <a href="index.php?ope=manage">&nbsp;</a>
    </p>
    <ul id="ssmenu3" class="ssmenu">
    </ul>

<?php	
	if ($userSession->is_set_aircrafts_file_allowed()) { ?>
	<!-- ----------------------------------------- -->
    <p id="menu4" class="menu"
		onmouseout="CacherDelai();"
		onmouseover="MontrerMenu('ssmenu4');">
	  <a href="index.php?ope=manage"
		onfocus="MontrerMenu('ssmenu4');"><?php echo($lang['ADMIN_MENU_MANAGE_AIRCRAFTS']); ?><span>&nbsp;:</span></a>
    </p>
    <ul id="ssmenu4" class="ssmenu"
		onmouseover="AnnulerCacher();"
		onmouseout="CacherDelai();"
		onfocus="AnnulerCacher();"
		onblur="CacherDelai();">
	<li>
		<a href="index.php?type=aircraft&ope=manage"><?php echo($lang['ADMIN_MENU_LIST_AIRCRAFTS']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=aircraft&ope=add"><?php echo($lang['ADMIN_MENU_ADD_AIRCRAFT']); ?><span>&nbsp;;</span></a>
	</li>
	<li>
		<a href="index.php?type=aircraft&ope=rank"><?php echo($lang['ADMIN_MENU_AIRCRAFT_RANKING']); ?><span>&nbsp;;</span></a>
	</li>
	</ul><?php
 	} else { ?>
    <p id="menu4" class="menu">
	  <a href="index.php?ope=manage"><span>&nbsp;</span></a>
    </p>
    <ul id="ssmenu4" class="ssmenu">
	</ul><?php
 	} ?>
    <p id="menu5" class="menu">
	  <a href="index.php?ope=manage">&nbsp;</a>
    </p>
    <ul id="ssmenu5" class="ssmenu">
	</ul><?php
	if ($userSession->is_set_pilots_file_allowed()) { ?>
	    <p id="menu6" class="menu"
			onmouseout="CacherDelai();"
			onmouseover="MontrerMenu('ssmenu6');">
	      <a href="index.php?ope=manage"
			onfocus="MontrerMenu('ssmenu6');"><?php echo($lang['ADMIN_MENU_MANAGE_USERS']); ?><span>&nbsp;:</span></a>
	    </p>
	    <ul id="ssmenu6" class="ssmenu"
			onmouseover="AnnulerCacher();"
			onmouseout="CacherDelai();"
			onfocus="AnnulerCacher();"
			onblur="CacherDelai();">
		<li>
			<a href="index.php?type=user&ope=manage&tri=NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_LOGIN']); ?><span>&nbsp;;</span></a>
		</li>
		<li>
			<a href="index.php?type=user&ope=manage&tri=LAST_NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_LAST_NAME']); ?><span>&nbsp;;</span></a>
		</li>
		<li>
			<a href="index.php?type=user&ope=manage&tri=FIRST_NAME"><?php echo($lang['ADMIN_MENU_LIST_USER_ORDERED_BY_FIRST_NAME']); ?></a>
		</li>
		<li>
			<a href="index.php?type=user&ope=manage&tri=MEMBERS"><?php echo($lang['ADMIN_MENU_LIST_MEMBERS']); ?></a>
		</li>
		<li>
			<a href="index.php?type=user&ope=manage&tri=INSTRUCTORS"><?php echo($lang['ADMIN_MENU_LIST_INSTRUCTORS']); ?></a>
		</li>
		<li>
			<a href="index.php?type=user&ope=add"><?php echo($lang['ADMIN_MENU_MENU_ADD_USER']); ?></a>
		</li>
	    </ul><?php 	
	} else { ?>
	    <p id="menu6" class="menu">
	      <a href="index.php?ope=manage">&nbsp;</a>
	    </p>
	    <ul id="ssmenu6" class="ssmenu">
	    </ul><?php 	
	}
} 
?>
</div>
<script language="Javascript" type="text/javascript">var nbmenu=6;
Chargement();</script>