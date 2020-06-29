<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * aircraft.manage.content.php
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
 * @version    CVS: $Id: aircraft.manage.content.php,v 1.1.2.3 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$myTemplate->assign('TITLE_HEADERS',$lang['LIST_AIRCRAFT']);
$myTemplate->display('template/headersDoc.tpl');
$myTemplate->assign('MODIFY',$lang['MODIFY']);
$myTemplate->assign('DELETE',$lang['DELETE']);
$myTemplate->assign('LICENSE',$lang['LICENSE']);
$myTemplate->assign('AIRCRAFT_CREW_MAX',$lang['AIRCRAFT_CREW_MAX']);
$myTemplate->assign('AIRCRAFT_HOURLY_COST_HEADER',$lang['AIRCRAFT_HOURLY_COST_HEADER']);
$myTemplate->assign('AIRCRAFT_TYPE_HEADER', $lang['AIRCRAFT_TYPE_HEADER']);
$myTemplate->assign('AIRCRAFT_CALLSIGN_HEADER', $lang['AIRCRAFT_CALLSIGN_HEADER']);
$myTemplate->assign('AIRCRAFT_NON_BOOKABLE',$lang['AIRCRAFT_NON_BOOKABLE']);
$myTemplate->display('./template/aircraftTableHeader.tpl');		//	first line of the table
$list_of_aircrafts = $currentAircraft->getAllAircrafts();
for ($boucle = 0; $boucle < count($list_of_aircrafts); $boucle++) {
	$currentAircraft->reference = $list_of_aircrafts[$boucle];
	$currentAircraft->getAircraftFromDatabase();
	$myVar = 'init';
	$myTemplate->assign('CALLSIGN', $currentAircraft->callsign[$myVar]);
	$myTemplate->assign('NON_BOOKABLE', $currentAircraft->nonBookable[$myVar]?'X':'');
	$myTemplate->assign('TYPE', $currentAircraft->type[$myVar]);
	$myTemplate->assign('HOURLY_COST', $currentAircraft->flight_hour_costs[$myVar]);
	$myTemplate->assign('SEATS_AVAILABLE', $currentAircraft->seats_available[$myVar]);
	$myTemplate->assign('REFERENCE', $currentAircraft->reference); 
	$myTemplate->assign('JS_CONFIRM_ACFT_DELETION', $lang['JS_CONFIRM_ACFT_DELETION']);
	$myTemplate->assign('MODIFY',$lang['MODIFY']);
	$myTemplate->assign('DELETE',$lang['DELETE']);
	$myTemplate->assign('MANAGE_LICENSE',$lang['MANAGE_LICENSE']);
	$myTemplate->display('./template/aircraftTable.tpl');
}
$myTemplate->display('./template/tableEnd.tpl');
?>