<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * profile.class.php
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
 * @category   administration interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: profile.class.php,v 1.19.2.10 2006/07/01 06:32:59 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// *************************************************************************************
// WARNING! conf file and database connection class MUST be called before this class!!!!
// *************************************************************************************
require_once("./pool/functions.php");

class profile {

	/**
	 * Error handler - An error occured if true 
	 *
	 * @var boolean
	 */
	var $error = false;
	/**
	 * Each error encountered is stored in this array. 
	 *
	 * @var array
	 */
	var $resultTab = array();

	/**
	 * Reference of this profile. This reference is unique.
	 *
	 * @var integer
	 */
	var $reference;
	/**
	 * Profile's name
	 *
	 * @var string
	 */
	var $name;
	/**
	 * Sum of all binaries involved in this profile. 
	 *
	 * @var integer
	 */
	var $permit;
	/**
	 * Database connexion handler
	 *
	 * @var ressource
	 */
	var $objectConnexion;
	
/**
 * Profile object initialisation
 *
 * @param object $databaseId
 * @return profile
 */
function profile($databaseId)
{
	$this->objectConnexion = $databaseId;	
}

/**
 * check if profiles exists for the club $club_id
 *
 * @param integer $profileId
 * @return boolean
 */
function isProfileExisting($profileId)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$profileResult = $objectDatabase->query_and_fetch_single('select count(*) from profiles where NUM="'.$profileId.'"');
	if ($profileResult == O) {
		return false;
	} else {
		return true;
	}
}

/**
 * Check if a name is given to this profile
 *
 */
function checkProfileName()
{
	if (strlen($this->name['form']) == 0) {
		$this->resultTab[] = 'Vous n\'avez pas donn&eacute; de nom au profil !';
		$this->error = true;
	}
}

/**
 * extract profile data from database
 *
 * @param integer $profileId
 */
function getProfileFromDatabase($profileId)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$profileResult = mysql_fetch_object($objectDatabase->query('select * from profiles where NUM="'.$profileId.'"'));
	$this->reference 	= $profileId;
	$this->name['init'] = stripslashes($profileResult->NAME);
	$this->permits['init'] = $profileResult->PERMITS;
}

/**
 * extract profile from a submitted form
 *
 */
function getProfileFromForm()
{
	$tampon_permits = define_variable('profile_no_auto_logout',0) + define_variable('profile_book_anytime',0) + define_variable('profile_book_anyduration',0) + define_variable('profile_book',0) + define_variable('profile_book_inst',0) + define_variable('profile_book_unfree_inst',0) + define_variable('profile_freeze_aircraft',0) + define_variable('profile_freeze_inst',0) + define_variable('profile_inst_rest',0) + define_variable('profile_users',0) + define_variable('profile_self',0) + define_variable('profile_club',0) + define_variable('profile_aircraft',0) + define_variable('profile_limits',0) + define_variable('profile_cnl',0);
	$this->permits['form'] = $tampon_permits;
	$this->name['form'] = $_POST['profile_name'];
}

// *********************
// save data to database
function saveProfile()
{
    global $lang;
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$this->checkProfileName();
	if (!$this->error) {
		if ($this->reference < 0) {
			$this->reference = $this->getNewId();
			$profileResult = $objectDatabase->query('INSERT INTO profiles SET NUM="'.$this->reference.'", NAME="'.setSlashes(htmlentities($this->name['form'])).'", PERMITS="'.$this->permits['form'].'"');
		} else {
			$profileResult = $objectDatabase->query('UPDATE profiles SET NAME="'.setSlashes(htmlentities($this->name['form'])).'", PERMITS="'.$this->permits['form'].'" WHERE NUM="'.$this->reference.'"');
		}
		$this->error = !$profileResult;
		if ($this->error) {
			$this->resultTab[] = $lang['REQUEST_FAILED'];
		} else {
			$this->resultTab[] = $lang['PROCESSED'];
		}	
	}
}

/**
 * calculate the new Id needed to create a new profile entry
 *
 * @return integer
 */
function getNewId()
{
	$list_number_table = $this->getAllProfile();
	$new_profile_id = -1;
	$i = 0;
	while ($new_profile_id < 0) {
		if (!(in_array(round(exp($i*log(2))), $list_number_table))) {
			$new_profile_id = round(exp($i*log(2)));
		} else {
			$i++;
		}
	}
	return $new_profile_id;
}

/**
 * fill a profile with default value
 *
 */
function createBlankProfile()
{
	$this->reference		= -1;
	$this->name['init']		= '';
	$this->permits['init']	= 0;
}

/**
 * @return array
 * @param club ID $id_club
 * @desc Return list of all profiles id
*/
function getAllProfile()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$result = $objectDatabase->query('select NUM from profiles ORDER BY NUM');
	while ($row = mysql_fetch_object($result)) {
		$profileList[] = $row->NUM;
	}
	sort($profileList, SORT_NUMERIC);
	return $profileList;
}

/**
 * list profiles name based on profiles num
 *
 * @return array
 */
function getAllProfileName()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$result = $objectDatabase->query('select NUM, NAME from profiles ORDER BY NUM');
	while ($row = mysql_fetch_object($result)) {
		$profileList[$row->NUM] = $row->NAME;
	}
	return $profileList;
}

/**
 * Delete this profile
 *
 */
function deleteProfile()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	// check first if profile is used
	$profileInUse = false;
	$profileResult = $objectDatabase->query('SELECT num FROM authentication where profile&'.$this->reference.'='.$this->reference);
	if ($row = mysql_fetch_object($profileResult)) {
		$profileInUse = true;
	}
	if (!$profileInUse) {
		$profileResult = $objectDatabase->query("DELETE FROM profiles WHERE NUM='".$this->reference."'");
	} else {
		$this->error = 1;
		$this->resultTab[] = 'PROFILE_IS_IN_USE';
	}
}

/**
 * @return boolean
 * @desc return true if this profile allow no auto logout for the user
*/
function isNoAutoLogout()
{
	return (($this->permits['init']&16777216)>>24);
}

/**
 * @return boolean
 * @desc return true if this profile allow user to book anytime
*/
function isBookAnytime()
{
	return ($this->permits['init']&1);
}

/**
 * @return boolean
 * @desc return true if this profile allow user to book any duration time
*/
function isBookAnyduration()
{
	return (($this->permits['init']&8388608)>>23);
}

/**
 * @return boolean
 * @desc return true if this profile allow user to book alone, else return false
*/
function isBookAlone()
{
	return (($this->permits['init']&2)>>1);
}

/**
 * check if allowed to book a slot with an instructor
 *
 * @return boolean
 */
function isBookInstructor()
{
	return (($this->permits['init']&4)>>2);
}

/**
 * check if allowed to ground an aircraft
 *
 * @return boolean
 */
function isFreezeAircraft()
{
	return (($this->permits['init']&8)>>3);
}

/**
 * check if allowed to ground instructors
 *
 * @return boolean
 */
function isFreezeInstructor()
{
	return (($this->permits['init']&16)>>4);
}

/**
 * check if allowed to book a slot with an instructor even if he's not supposed to be available
 *
 * @return boolean
 */
function isBookUnfreeInstructor()
{
	return (($this->permits['init']&32)>>5);
}

/**
 * check if allowed to manage users
 *
 * @return boolean
 */
function isSetPilotsFile()
{
	return (($this->permits['init']&64)>>6);
}

/**
 * check if the user is allowed to file its own parameters
 *
 * @return boolean
 */
function isSetOwnQualifications()
{
	return (($this->permits['init']&128)>>7);
}

/**
 * is club admin 
 *
 * @return boolean
 */
function IsSetClubParameters()
{
	return (($this->permits['init']&256)>>8);
}

/**
 * is allowed to modify aircrafts data 
 *
 * @return boolean
 */
function isSetAircraftsFile()
{
	return (($this->permits['init']&512)>>9);
}

/**
 * Enter description here...
 *
 * @return boolean
 */
function isSetOwnLimitationsAllowed()
{
	return(($this->permits['init']&1024)>>10);
}

/**
 * is allowed to book/ cancel bookings for everybody
 *
 * @return boolean
 */
function isEverybodyBook()
{
	return (($this->permits['init']&2048)>>10);
}

}
?>