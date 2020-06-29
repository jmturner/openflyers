<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageAircraft.php
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
 * @version    CVS: $Id: manageAircraft.php,v 1.1.2.8 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

require_once("./classes/aircraft.class.php");	//--- addslashes(htmlentities($ref))
require_once("./classes/license.class.php");

$ref = define_variable('ref',-1);
$currentAircraft = new aircraft($ref, $database);
$currentLicense = new license(-1, $database);
$myTemplate->assign('ONLOAD','');
$myTemplate->assign('ADMIN_TITLE',$lang['ADMIN_TITLE']);
$myTemplate->assign('MANAGE_USER_DELETE_CONFIRM',$lang['MANAGE_USER_DELETE_CONFIRM']);
$myTemplate->display('template/headers.tpl');
require_once('./admin/menu.php');
switch ($ope) {
case "rank" :
	$list_of_aircrafts = $currentAircraft->getAllAircrafts();
	$content_display = '';
	$styleOfAircraft = '';
	$styleOfTargetLeft = '';
	$style_of_target_right = '';
	$style_of_fulltarget = '';
	$form_content ='';
	$javascript_DHTML = 'SET_DHTML(CURSOR_MOVE';
	for ($boucle = 0; $boucle < count($list_of_aircrafts); $boucle++) {
		$currentAircraft->reference = $list_of_aircrafts[$boucle];
		$currentAircraft->getAircraftFromDatabase();
		$y_div = 150+60*$boucle;
		$content_display .= '<div id="aircraft'.$boucle.'">'.$currentAircraft->callsign['init'].' - '.$currentAircraft->type['init'].'</div>'."\n";
		$styleOfAircraft .= '#aircraft'.$boucle.' { position: absolute; cursor: pointer; width: 150px; background: #ffffff; color: #000000; font-size: 1em; top: '.($y_div+5).'px; z-index: 3; padding: 10px; text-align: center; left: 55px; }'."\n";
		$styleOfAircraft .= '#aircraft'.$boucle.':hover { position: absolute; cursor:pointer; width: 150px; background: #000000; color: #ffffff; font-size: 1em; top: '.($y_div+5).'px; z-index: 3; padding: 10px; text-align: center; left: 55px; }'."\n";
		$content_display .= '<div id="targetleft'.$boucle.'">'.($boucle+1).'</div>'."\n";
		$styleOfTargetLeft .= '#targetleft'.$boucle.'{ position: absolute; width: 50px; left: 0px; background: #0000cc; font-size: 3em; top: '.$y_div.'px; height: 50px; z-index: 2; color: #ffffff; text-align: center; vertical-align: middle; font-weight: bolder; }'."\n";
		$content_display .= '<div id="targetright'.$boucle.'"></div>'."\n";
		$style_of_target_right .= '#targetright'.$boucle.' { position: absolute; width: 620px; left: 50px; background: #0000aa; font-size: 1em; top: '.$y_div.'px; height: 50px; z-index: 1; color: #ffffff; text-align: right; }'."\n";
		$content_display .= '<div id="fulltarget'.$boucle.'">'.$lang['AIRCRAFT_ORDER_COMMENTS'].'</div>'."\n";
		$style_of_fulltarget .= '#fulltarget'.$boucle.'{ position: absolute; width: 570px; left: 90px; background: #ffff00; font-size: 1em; top: '.($y_div+5).'px; height: 40px; z-index: 2; color: #000000; text-align: right; }'."\n";
		$form_content .= '<input type="hidden" name="aircraft_tab['.$boucle.']" value="'.$currentAircraft->reference.'" />';
		$form_content .= '<input type="hidden" name="aircraft_y_tab['.$boucle.']" value="" />';
		$javascript_DHTML .= ', "aircraft'.$boucle.'"';
	}
	$javascript_DHTML .= ');';
	$styleOfTargetLeft .= $style_of_target_right;
	$styleOfTargetLeft .= $style_of_fulltarget;
	$styleOfTargetLeft .= $styleOfAircraft;
	$myTemplate->assign("FORMCONTENT", $form_content);
	$myTemplate->assign("MAXAIRCRAFT", count($list_of_aircrafts));
	$myTemplate->assign("STYLE", $styleOfTargetLeft);
	$myTemplate->assign("JAVASCRIPT", $javascript_DHTML);
	$myTemplate->assign("FILECONTENT", $content_display);
	$myTemplate->assign("VALIDATE", $lang['VALIDATE']);
	$myTemplate->display('template/ordonnerTPL.html');
	break;
case "ranking" :
	$aircraft_tab = $_POST['aircraft_tab']; // get the IDs tab
	$aircraft_y_tab = $_POST['aircraft_y_tab']; // get the y coordinates (keys are same for IDs)
	for ($boucle_acft = 0; $boucle_acft < count($aircraft_tab); $boucle_acft++) {
		$aircraftOrder[$aircraft_tab[$boucle_acft]] = $aircraft_y_tab[$boucle_acft];
		//echo($aircraft_tab[$boucle_acft].'---'.$aircraft_y_tab[$boucle_acft]."<br />");
	}
	asort($aircraftOrder, SORT_NUMERIC); // sort aircrafts ids according to their y - keys, now, can help to determine new order
	$top_rank = $currentAircraft->getMaxRank();
	$top_rank++;
	if (count($aircraftOrder) == count(array_unique($aircraftOrder))) {// check for duplicate (2 aircrafts in the same box)
		$boucleFinal = 0 + $top_rank;
		foreach ($aircraftOrder as $aircraft_key => $aircraft_y) {
			// lors de l'injection en base de données, le changement d'ordre provoque une collision (duplicate) due à un doublon ORDER_NUM
			$database->query("UPDATE aircrafts SET ORDER_NUM='".$boucleFinal."' WHERE NUM='".$aircraft_key."'");
			$boucleFinal++;
		}
	}
	// seconde injection pour réinitialisation
	if (count($aircraftOrder) == count(array_unique($aircraftOrder))) {// check for duplicate (2 aircrafts in the same box)
		$boucleFinal = 0;
		foreach ($aircraftOrder as $aircraft_key => $aircraft_y) {
			// lors de l'injection en base de données, le changement d'ordre provoque une collision (duplicate) due à un doublon ORDER_NUM
			$database->query("UPDATE aircrafts SET ORDER_NUM='".$boucleFinal."' WHERE NUM='".$aircraft_key."'");
			$boucleFinal++;
		}
	}
	// fin de la remise en ordre
	$currentAircraft->resultTab[] = 'PROCESSED';
	$myResultArray = $currentAircraft->resultTab;
	include('./admin/results.content.php');
	include('./admin/aircraft.manage.content.php');
	break;
case "manage" :
	include('./admin/aircraft.manage.content.php');
	break;
case "destroy" :
	$currentAircraft->deleteAircraft(true);
	$myResultString = $lang['AIRCRAFT_REMOVED'];
	include('./admin/results.content.php');
	include('./admin/aircraft.manage.content.php');
	break;
case "modify" :
	$currentAircraft = new aircraft($ref, $database); 	// create a object with callsign "ref"
	$currentAircraft->getAircraftFromDatabase();		// extract from database all details about this aircraft
	$admin_subtitle = 'MOD_AIRCRAFT_TITLE';
	$myVar = 'init';
	$myTemplate->assign("SUBTITLE",$lang[$admin_subtitle]);
	$myTemplate->assign("FORM_NAME","aircraft_form");
	$myTemplate->assign("FORM_ENCTYPE","application/x-www-form_urlencoded");
	$myTemplate->assign("FORM_OBJECT_TYPE","aircraft");
	$myTemplate->assign("FORM_OPE","db");
	$myTemplate->assign("FORM_REF",$currentAircraft->reference);
	$myTemplate->display('template/adminAddModHeader.tpl');
	// *******
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_CALLSIGN']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_callsign');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->callsign[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
?>
	<tr>
		<td class="form_cell"><?php echo($lang['AIRCRAFT_NON_BOOKABLE'])?></td>
		<td class="form_cell"><input type="checkbox" name="aircraft_non_bookable" value="1"<?php if($currentAircraft->nonBookable[$myVar]){?>checked="checked"<?php }?>/></td>
	</tr>
<?php
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_TYPE']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_type');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->type[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_HOURLY_COST']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_hourlycost');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->flight_hour_costs[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_MAX_CREW_NUMBER']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_payload');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->seats_available[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_COMMENTS']);
	$myTemplate->assign('FIELD_NAME','aircraft_comments');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->comments[$myVar]); 
	$myTemplate->display('template/corpsTextForm.tpl');
	$myTemplate->assign('VALIDATION',$lang['VALIDATE']);
	$myTemplate->assign('BACK_LINK','index.php?type=aircraft&ope=manage');
	$myTemplate->assign('BACK_TEXT',$lang['BACK']); 
	$myTemplate->display('template/adminEndForm.tpl');
	// *******
	break;
case "add" :
	$currentAircraft = new aircraft('',$database); 	// blank callsign in order to create a blank object => all fields are empty as needed for a new one
	$currentAircraft->createBlankAircraft('init'); 	// set all properties to ''
	$admin_subtitle = 'ADD_AIRCRAFT_TITLE';
	$myVar = 'init';
	$myTemplate = new basicTemplate();
	$myTemplate->assign("SUBTITLE",$lang[$admin_subtitle]);
	$myTemplate->assign("FORM_NAME","aircraft_form");
	$myTemplate->assign("FORM_ENCTYPE","application/x-www-form_urlencoded");
	$myTemplate->assign("FORM_OBJECT_TYPE","aircraft");
	$myTemplate->assign("FORM_OPE","db");
	$myTemplate->assign("FORM_REF",$currentAircraft->reference);
	$myTemplate->display('template/adminAddModHeader.tpl');
	// *************
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_CALLSIGN']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_callsign');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->callsign[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_TYPE']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_type');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->type[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_HOURLY_COST']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_hourlycost');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->flight_hour_costs[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_MAX_CREW_NUMBER']);
	$myTemplate->assign('FIELD_TYPE','text');
	$myTemplate->assign('FIELD_NAME','aircraft_payload');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->seats_available[$myVar]); 
	$myTemplate->display('template/corpsForm.tpl');
	$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_COMMENTS']);
	$myTemplate->assign('FIELD_NAME','aircraft_comments');
	$myTemplate->assign('FIELD_VALUE',$currentAircraft->comments[$myVar]); 
	$myTemplate->display('template/corpsTextForm.tpl');
	$myTemplate->assign('VALIDATION',$lang['VALIDATE']);
	$myTemplate->assign('BACK_LINK','index.php?type=aircraft&ope=manage');
	$myTemplate->assign('BACK_TEXT',$lang['BACK']); 
	$myTemplate->display('template/adminEndForm.tpl');
	break;
case "db" :
	$currentAircraft->acft_init();
	$currentAircraft->saveAircraft();
	$myResultArray = $currentAircraft->resultTab;
	include('./admin/results.content.php');
	if ($currentAircraft->error) {
		$myVar = 'form';
		$admin_subtitle = 'MOD_AIRCRAFT_TITLE';
		$myTemplate = new basicTemplate();
		$myTemplate->assign("SUBTITLE",$lang[$admin_subtitle]);
		$myTemplate->assign("FORM_NAME","aircraft_form");
		$myTemplate->assign("FORM_ENCTYPE","application/x-www-form_urlencoded");
		$myTemplate->assign("FORM_OBJECT_TYPE","aircraft");
		$myTemplate->assign("FORM_OPE","db");
		$myTemplate->assign("FORM_REF",$currentAircraft->reference);
		$myTemplate->display('template/adminAddModHeader.tpl');
		// ********
		$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_CALLSIGN']);
		$myTemplate->assign('FIELD_TYPE','text');
		$myTemplate->assign('FIELD_NAME','aircraft_callsign');
		$myTemplate->assign('FIELD_VALUE',$currentAircraft->callsign[$myVar]); 
		$myTemplate->display('template/corpsForm.tpl');
		$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_TYPE']);
		$myTemplate->assign('FIELD_TYPE','text');
		$myTemplate->assign('FIELD_NAME','aircraft_type');
		$myTemplate->assign('FIELD_VALUE',$currentAircraft->type[$myVar]); 
		$myTemplate->display('template/corpsForm.tpl');
		$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_HOURLY_COST']);
		$myTemplate->assign('FIELD_TYPE','text');
		$myTemplate->assign('FIELD_NAME','aircraft_hourlycost');
		$myTemplate->assign('FIELD_VALUE',$currentAircraft->flight_hour_costs[$myVar]); 
		$myTemplate->display('template/corpsForm.tpl');
		$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_MAX_CREW_NUMBER']);
		$myTemplate->assign('FIELD_TYPE','text');
		$myTemplate->assign('FIELD_NAME','aircraft_payload');
		$myTemplate->assign('FIELD_VALUE',$currentAircraft->seats_available[$myVar]); 
		$myTemplate->display('template/corpsForm.tpl');
		$myTemplate->assign('FIELD_ENTRY',$lang['AIRCRAFT_COMMENTS']);
		$myTemplate->assign('FIELD_NAME','aircraft_comments');
		$myTemplate->assign('FIELD_VALUE',$currentAircraft->comments[$myVar]); 
		$myTemplate->display('template/corpsTextForm.tpl');
		$myTemplate->assign('VALIDATION',$lang['VALIDATE']);
		$myTemplate->assign('BACK_LINK','index.php?type=aircraft&ope=manage');
		$myTemplate->assign('BACK_TEXT',$lang['BACK']); 
		$myTemplate->display('template/adminEndForm.tpl');							// ********
	} else {
		include('./admin/aircraft.manage.content.php');
	}
	break;
case "license" :
	$license_list = $currentLicense->getFullLicense(); 					// get all license (allow to display a selection)
	$license_requirements = $currentLicense->getAircraftLicense($ref);  // get license restrictions on aircraft $ref
	$currentAircraft->reference = $ref;
	$currentAircraft->getAircraftFromDatabase();
	$license_keys = array_keys($license_requirements);
	$loopLimit = count($license_keys);
	echo($lang['AIRCRAFT_LICENSE_NEEDED_1'].$currentAircraft->callsign['init'].', '.$lang['AIRCRAFT_LICENSE_NEEDED_2'].'<br /><br />');
	for ($loop = 0; $loop < $loopLimit; $loop++) {		// row 
		$local_checknum = $license_keys[$loop];
		echo('<table border="0">');
		echo('<tr><td>&nbsp;');
		$loopLimit_bis = count($license_requirements[$local_checknum]);
		for ($loop_bis = 0; $loop_bis < $loopLimit_bis; $loop_bis++) {
			echo($license_list[$license_requirements[$local_checknum][$loop_bis]]);
			echo('&nbsp;</td><td>');
			echo('	<form action="index.php" method="post">
					<input type="hidden" name="type" value="aircraft" />
					<input type="hidden" name="ope" value="remove" />
					<input type="hidden" name="ref" value="'.$ref.'" />
					<input type="hidden" name="license_id" value="'.$license_requirements[$local_checknum][$loop_bis].'" />
					<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
					(<input type="image" src="img/destroy.gif" alt="Detruire" onClick="this.submit()" />)
					</form>');
			echo('</td><td>&nbsp;'.$lang['OR'].'&nbsp;</td><td>');
		}
		echo('	<form action="index.php" method="post">
				<input type="hidden" name="type" value="aircraft" />
				<input type="hidden" name="ope" value="add_license" />
				<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
				<input type="hidden" name="ref" value="'.$ref.'" />');
		echo($currentLicense->getSelectLicense('add_license_col'));
		echo('<input type="submit" value="'.$lang['ADD'].'" />');
		echo('	</form></td></tr></table>');
	}
	if (count($license_keys) > 0) { 
		$checked_cheknum = max($license_keys);
		$checked_cheknum++;
	} else {
		$checked_cheknum = 0;
	}
	echo('<table><tr><td>');
	echo('	<form action="index.php" method="post">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="add_license" />');
	echo('<input type="hidden" name="license_checknum" value="'.$checked_cheknum.'" />');
	echo('	<input type="hidden" name="ref" value="'.$ref.'" />');
	echo($currentLicense->getSelectLicense('add_license_row'));
	echo('<input type="submit" value="'.$lang['ADD'].'" />');
	echo('	</form></td></tr></table>');
	echo('<p>
<a href="index.php?ope=manage&type=aircraft" class="dblink"> &nbsp;'.$lang['BACK'].'&nbsp; </a>
</p>');
	break;
case "remove" :
	$currentLicense->removeAircraftLicense($ref, $_POST['license_id'], $_POST['license_checknum']);
	// ******************** SHOW ACFT_LICENSE
	$license_list = $currentLicense->getFullLicense(); 					// get all license (allow to display a selection)
	$license_requirements = $currentLicense->getAircraftLicense($ref); // get license restrictions on aircraft $ref
	$license_keys = array_keys($license_requirements);
	//print_r($license_requirements);
//							print_r($license_list);
	$loopLimit = count($license_keys);
	for ($loop = 0; $loop < $loopLimit; $loop++) {			// row 
		$local_checknum = $license_keys[$loop];
		echo('<table border="0">');
		echo('<tr><td>&nbsp;');
		$loopLimit_bis = count($license_requirements[$local_checknum]);
		for ($loop_bis = 0; $loop_bis < $loopLimit_bis; $loop_bis++) {
			echo($license_list[$license_requirements[$local_checknum][$loop_bis]]);
			echo('&nbsp;</td><td>');
			echo('	<form action="index.php" method="post">
					<input type="hidden" name="type" value="aircraft" />
					<input type="hidden" name="ope" value="remove" />
					<input type="hidden" name="ref" value="'.$ref.'" />
					<input type="hidden" name="license_id" value="'.$license_requirements[$local_checknum][$loop_bis].'" />
					<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
					(<input type="image" src="img/destroy.gif" alt="Detruire" onClick="this.submit()" />)
					</form>');
			echo('</td><td>&nbsp;'.$lang['OR'].'&nbsp;</td><td>');
		}
		echo('	<form action="index.php" method="post">
				<input type="hidden" name="type" value="aircraft" />
				<input type="hidden" name="ope" value="add_license" />
				<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
				<input type="hidden" name="ref" value="'.$ref.'" />');
		echo($currentLicense->getSelectLicense('add_license_col'));
		echo('<input type="submit" value="'.$lang['ADD'].'" />');
		echo('	</form></td></tr></table>');
	}
	$checked_cheknum = max($license_keys);
	$checked_cheknum++;
	echo('<table><tr><td>');
	echo('	<form action="index.php" method="post">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="add_license" />');
	echo('<input type="hidden" name="license_checknum" value="'.$checked_cheknum.'" />');
	echo('	<input type="hidden" name="ref" value="'.$ref.'" />');
	echo($currentLicense->getSelectLicense('add_license_row'));
	echo('<input type="submit" value="'.$lang['ADD'].'" />');
	echo('	</form></td></tr></table>');
	break;
case "add_license"	:	
	$license_checknum = $_POST['license_checknum'];
	if ($license_checknum==0) {
	    $license_checknum=1;
	}
	if (isset($_POST['add_license_col'])) {
		$currentLicense->addAircraftLicense($ref, $license_checknum, $_POST['add_license_col']);
	}
	if (isset($_POST['add_license_row'])) {
		$currentLicense->addAircraftLicense($ref, $license_checknum, $_POST['add_license_row']);
	}
	// ******************** SHOW ACFT_LICENSE
	$license_list = $currentLicense->getFullLicense(); 					// get all license (allow to display a selection)
	$license_requirements = $currentLicense->getAircraftLicense($ref); // get license restrictions on aircraft $ref
	$license_keys = array_keys($license_requirements);
	//print_r($license_requirements);
//							print_r($license_list);
	$loopLimit = count($license_keys);
	for ($loop = 0; $loop < $loopLimit; $loop++) { 			// row 
		$local_checknum = $license_keys[$loop];
		echo('<table border="0">');
		echo('<tr><td>&nbsp;');
		$loopLimit_bis = count($license_requirements[$local_checknum]);
		for ($loop_bis = 0; $loop_bis < $loopLimit_bis; $loop_bis++) {
			echo($license_list[$license_requirements[$local_checknum][$loop_bis]]);
			echo('&nbsp;</td><td>');
			echo('	<form action="index.php" method="post">
					<input type="hidden" name="type" value="aircraft" />
					<input type="hidden" name="ope" value="remove" />
					<input type="hidden" name="ref" value="'.$ref.'" />
					<input type="hidden" name="license_id" value="'.$license_requirements[$local_checknum][$loop_bis].'" />
					<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
					(<input type="image" src="img/destroy.gif" alt="Detruire" onClick="this.submit()" />)
					</form>');
			echo('</td><td>&nbsp;'.$lang['OR'].'&nbsp;</td><td>');
		}
		echo('	<form action="index.php" method="post">
				<input type="hidden" name="type" value="aircraft" />
				<input type="hidden" name="ope" value="add_license" />
				<input type="hidden" name="license_checknum" value="'.$local_checknum.'" />
				<input type="hidden" name="ref" value="'.$ref.'" />');
		echo($currentLicense->getSelectLicense('add_license_col'));
		echo('<input type="submit" value="'.$lang['ADD'].'" />');
		echo('	</form></td></tr></table>');
	}
	$checked_cheknum = max($license_keys);
	$checked_cheknum++;
	echo('<table><tr><td>');
	echo('	<form action="index.php" method="post">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="add_license" />');
	echo('<input type="hidden" name="license_checknum" value="'.$checked_cheknum.'" />');
	echo('	<input type="hidden" name="ref" value="'.$ref.'" />');
	echo($currentLicense->getSelectLicense('add_license_row'));
	echo('<input type="submit" value="'.$lang['ADD'].'" />');
	echo('	</form></td></tr></table>');
	break;
}
$myTemplate->display('./template/footers.tpl');
?>