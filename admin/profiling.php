<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * profiling.php
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
 * @version    CVS: $Id: profiling.php,v 1.10.2.3 2005/10/28 17:44:05 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$result_profiling = $database->query('SELECT NUM, NAME, PERMITS FROM profiles ORDER BY NUM');
$loop = 0;
while ($row_profiling = mysql_fetch_object($result_profiling)) {
	$tabProfilDecimal[$loop] 	= $row_profiling->NUM;
	$tabProfilNom[$loop] 		= stripslashes($row_profiling->NAME);
	$tabProfilePermits[$loop] = $row_profiling->PERMITS;
	$loop++;
}
$maxProfilNumber= count($tabProfilDecimal)-1;
// calcule pour chaque user les profils utilis&eacute;s - ou comment tuer une souris avec un Tank!
$basicProfileValue = 0;
$choixProfile = '<table style="font-size:12px;">';
$initProfile = true;
$testBit = $currentUser->profiles[$myVar];
for ($i = $maxProfilNumber; $i >= 0; $i--) {
    if (($testBit - $tabProfilDecimal[$i]) >= 0) {
        if ($userSession->is_set_club_parameters_allowed()) {
            // si admin de club, il peut affecter tous les profils sauf ceux associ&eacute;s au super admin
            $choixProfile .= '<tr><td>'.$tabProfilNom[$i].'</td><td><input type="checkbox" name="user_profile[]" value="'.$tabProfilDecimal[$i].'" CHECKED ></td></tr>';
        }
        else {
            if (!(isSetClubAllowed($tabProfilePermits[$i]))) {
                // affiche le champ s'il est possible &agrave; choisir
                $choixProfile .= '<tr><td>'.$tabProfilNom[$i].'</td><td><input type="checkbox" name="user_profile[]" value="'.$tabProfilDecimal[$i].'" CHECKED ></td></tr>';
            }
            else {
                $basicProfileValue += $tabProfilDecimal[$i]; // stocke les profils non autoris&eacute;s mais pr&eacute;sents pour l'utilisateur concern&eacute; - permet une modification du profil mais mod&eacute;r&eacute;e
            }
        }
        $testBit = $testBit - $tabProfilDecimal[$i];
    }
    else {
        // fin du cas o&ugrave; le profil est inclus
        if ($userSession->is_set_club_parameters_allowed()) {
            // si admin de club, il peut affecter tous les profils
            $choixProfile .= '<tr><td>'.$tabProfilNom[$i].'</td><td><input type="checkbox" name="user_profile[]" value="'.$tabProfilDecimal[$i].'"></td></tr>';
        }
        else {
            if (!(isSetClubAllowed($tabProfilePermits[$i]))) {
                // affiche le champ s'il est possible &agrave; choisir
                $choixProfile .= '<tr><td>'.$tabProfilNom[$i].'</td><td><input type="checkbox" name="user_profile[]" value="'.$tabProfilDecimal[$i].'"></td></tr>';
            }
        }
    }
}
$choixProfile .= '</table>';
$choixProfile .= '<input type="hidden" name="basic_profile_value" value="'.$basicProfileValue.'">';
// fin du calcul
?>