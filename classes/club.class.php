<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * club.class.php
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
 * @version    CVS: $Id: club.class.php,v 1.33.2.4 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// **************************************************************************************
// !WARNING! conf file and database connection class MUST be called before this class!!!!
// **************************************************************************************

class club {
// BEGIN 

	/**
	 * Reference of the club (automatically set to 1). Initially, OF 1.1 and lower were supposed to manage as many club as requested. Now it is deprecated.
	 *
	 * @var integer
	 */
	var $reference = 1;
	/**
	 * Error handler - True if an error was encountered
	 *
	 * @var boolean
	 */
	var $error = false;			// dedicated - error handler
	var $resultTab = array();	// dedicated - error or success messages	
	
	var $name;				// name of the club
	var $infoCell;			// the message can be displayed on the first page (not log page)
	var $logo;				// content of a picture file used as logo
	var $logo_name;			// the name of the logo picture when uploaded 
	var $logo_ext;			// MIME type of the logo 
	var $logo_size;			// size of the logo to be be displayed
	var $website_url;		// URL for the website (can be reached by clicking on logo)
	var $stylesheet_URL;	// the stylesheet associated to the club
	var $firstHour;		// club is opening at ...
	var $lastHour;			// club is closing at ...
	var $usual_profiles;	// defaul profile (filing is easier)
	var $icao;				// ICAO designation of the airfield
	var $flags;				// few parameters - coding of these ones is working like UNIX CHMOD command (see database description for further details)
	/**
	 * display a "default slot"
	 *
	 * @var integer
	 */
	var $default_slot;
	var	$min_slot;			// all slots booked are longer than this min_slot

	var	$mlName;			// a mailing list associated to the club - Check first if supported
	var $mlType;			// type of the mailing - Check first if supported
	var $language;			// default club/user language
	var $timezone;			// default timezone of the organisation  (bookings are converted to Z / UT)
	var $mailer;			// e-mail address used to send e-mail notification to users
	/**
	 * ID of the main manager for openflyers users.
	 *
	 * @var integer
	 */
	var $adminNum;
	
	var $objectConnexion;	// connection

/**
 * Club class initialisation
 *
 * @param ressource $databaseId
 * @return club
 */
function club($databaseId)
{
	$this->objectConnexion = $databaseId;
	$this->getClubFromDatabase();	// get the details about club "reference"
}

/**
 * check if club name is empty
 *
 */
function checkClubName()
{
	if (strlen($this->name['form']) == 0) {
		$this->error = true;
		$this->resultTab[] = 'EMPTY_ORGANISATION_NAME';
	}
}

/**
 * Check data integrity if a new airfield is added (it does not exist in the standard list)
 *
 */
function checkICAO()
{
	if ($_POST['icao_place'] == 'other') {
		if (strlen($_POST['airfield_name']) == 0) {
			$this->error = true;
			$this->resultTab[] = 'EMPTY_AIRFIELD_NAME';
		}
		if (strlen($_POST['airfield_oaci']) == 0) {
			$this->error = true;
			$this->resultTab[] = 'EMPTY_ICAO_DESIGNATION';
		}
		if (!is_numeric($_POST['airfield_alt'])) {
			$this->error = true;
			$this->resultTab[] = 'NON_NUMERIC_ALTITUDE';
		}
		if (!is_numeric(substr($_POST['airfield_lat'], 1, strlen($_POST['airfield_lat'])-1 )) || (!strstr('nNsS', $_POST['airfield_lat'][0]))) {
			$this->error = true;
			$this->resultTab[] = 'CHECK_LATITUDE';
		}
		if (!is_numeric(substr($_POST['airfield_long'], 1, strlen($_POST['airfield_long'])-1 )) || (!strstr('eEwW', $_POST['airfield_long'][0]))) {
			$this->error = true;
			$this->resultTab[] = 'CHECK_LONGITUDE';
		}
	}
}

/**
 * get data from the database about the club "reference" 
 *
 */
function getClubFromDatabase()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$club_result = mysql_fetch_object($objectDatabase->query("SELECT * FROM clubs WHERE NUM='".$this->reference."';"));
	// data from db
	$this->name['init'] 			= stripslashes($club_result->NAME);
	$this->infoCell['init'] 		= stripslashes($club_result->INFO_CELL);
	$this->logo['init'] 			= $club_result->LOGO;
	$this->logo_name['init']	 	= $club_result->LOGO_NAME;
	$this->logo_ext['init'] 		= $club_result->LOGO_EXT;
	$this->logo_size['init'] 		= $club_result->LOGO_SIZE;
	$this->website_url['init']		= $club_result->CLUB_SITE_URL;
	$this->stylesheet_URL['init']	= $club_result->STYLESHEET_URL;
	$this->firstHour['init'] 		= $club_result->FIRST_HOUR_DISPLAYED;
	$this->lastHour['init'] 		= $club_result->LAST_HOUR_DISPLAYED;
	$this->usual_profiles['init'] 	= $club_result->USUAL_PROFILES;
	$this->icao['init']		 		= $club_result->ICAO;
	$this->flags['init'] 			= $club_result->FLAGS;
	$this->default_slot['init']		= $club_result->DEFAULT_SLOT_RANGE;
	$this->min_slot['init']		 	= $club_result->MIN_SLOT_RANGE;
	$this->adminNum['init']			= $club_result->ADMIN_NUM;

	$this->mlName['init']	 		= $club_result->MAILING_LIST_NAME;
	$this->mlType['init'] 			= $club_result->MAILING_LIST_TYPE;
	if (strlen($club_result->DEFAULT_TIMEZONE) > 0) {
		$this->timezone['init']		= $club_result->DEFAULT_TIMEZONE;
	} else {
		$this->timezone['init']		= 'UTC';
	}
	if (strlen($club_result->LANG) > 0) {
		$this->language['init']			= $club_result->LANG;
	} else {
		$this->language['init'] 	= DEFAULT_LANG;
	}
	$this->mailer['init']			= $club_result->MAIL_FROM_ADDRESS;
	// completed! 
}

/**
 * get data sent by form
 *
 */
function getClubFromForm()
{
	$this->name['form'] 			= $_POST['club_name'];
	$this->language['form']			= $_POST['organisation_language'];
	$this->infoCell['form'] 		= addslashes($_POST['club_text_cell']);
	$this->firstHour['form'] 		= $_POST['hour_begin'].':'.$_POST['minute_begin'].':00';
	$this->lastHour['form'] 		= $_POST['hour_end'].':'.$_POST['minute_end'].':00';
	if ($this->isNewICAO()) {
		$this->icao['form']			= $_POST['airfield_oaci'];
	} else {
		$this->icao['form']		 	= $_POST['icao_place'];
	}
	$this->website_url['form']		= $_POST['website_URL'];
	$this->flags['form'] 			= define_variable('same_day_box',0) + define_variable('book_comment',0);
	$this->default_slot['form']		= $_POST['default_book_hours'] * 60 + $_POST['default_book_minutes'];
	$this->min_slot['form']		 	= $_POST['min_book'];
	$this->adminNum['form']		= $_POST['organisation_manager'];

	$this->mlName['form']	 		= $_POST['mailing_list_name'];
	$this->mlType['form'] 			= $_POST['mailing_list_type'];
	$this->mailer['form']			= $_POST['mailer'];
	$default_profile_rq = 0;
	if (isset($_POST['user_profile'])) {
		$default_profile_rq = array_sum($_POST['user_profile']);
	}
	$user_basic_profile_rq = $_POST['basic_profile_value'];
	$this->usual_profiles['form'] 	= $default_profile_rq + $user_basic_profile_rq;
	$this->timezone['form']			= $_POST['organisation_timezone'];
}

/**
 * @return int
 * @param string $default_slot_part
 * @desc get DEFAULT_SLOT - $default_slot_part is 'hour' or 'minute' to get the value requested 
*/
function getDefaultSlot($default_slot_part)
{
	switch($default_slot_part)
	{
	CASE "hour" :
		return (floor($this->default_slot['init']/60));
		break;
	CASE "minute" :
		return ($this->default_slot['init'] - $les_heures * 60);
		break;
	}
}

/**
 * save all information to the database about the club "reference". Update if already existing - insert if new one
 *
 */
function saveClub()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$this->getClubFromForm();
	$club_request = 'UPDATE clubs SET 	NAME="'.setSlashes(htmlentities($this->name['form'])).'",
										FLAGS="'.$this->flags['form'].'", 
										INFO_CELL="'.setSlashes($this->infoCell['form']).'",
										CLUB_SITE_URL="'.$this->website_url['form'].'",
										USUAL_PROFILEs="'.$this->usual_profiles['form'].'",
										FIRST_HOUR_DISPLAYED="'.$this->firstHour['form'].'", 
										LAST_HOUR_DISPLAYED="'.$this->lastHour['form'].'", 
										MIN_SLOT_RANGE="'.$this->min_slot['form'].'",	
										DEFAULT_SLOT_RANGE="'.$this->default_slot['form'].'", 
										MAILING_LIST_NAME="'.$this->mlName['form'].'",
										MAILING_LIST_TYPE="'.$this->mlType['form'].'",
										DEFAULT_TIMEZONE="'.$this->timezone['form'].'",
										LANG="'.$this->language['form'].'",
										MAIL_FROM_ADDRESS="'.$this->mailer['form'].'",
										ADMIN_NUM="'.$this->adminNum['form'].'",
										ICAO="'.$this->icao['form'].'" WHERE NUM="'.$this->reference.'"';
	$this->checkClubName();
	$this->checkICAO();
	if (!$this->error) {
		$club_result = $objectDatabase->query($club_request);
		$this->error = !$club_result;
		$club_result = $objectDatabase->query('OPTIMIZE TABLE clubs'); // Optimise table every times data is inserted
		if ($this->error) {
			$this->resultTab[] = 'REQUEST_FAILED';
		} else {
			$this->saveLogo();
			$this->saveICAO();
			$this->resultTab[] = 'PROCESSED';
		}
	}
}

/**
 * @return boolean
 * @desc is there a logo to be be uploaded
*/
function isNewLogo()
{
	if (isset($_FILES['club_logo_picture']['tmp_name'])) {
		return is_uploaded_file($_FILES['club_logo_picture']['tmp_name']);
	} else {
		return false;
	}
}

/**
 * @return boolean
 * @param string $whichVar
 * @desc is the "same day" box activated
*/
function sameDayBox($whichVar)
{
	return(($this->flags[$whichVar]&1)>>0);
}

/**
 * @return boolean
 * @param string $whichVar
 * @desc is the flight comment activated
*/
function bookComment($whichVar)
{
	return(($this->flags[$whichVar]&2)>>1);
}

/**
 * @return array
 * @param string $whichVar
 * @desc get the booking start hour
*/
function getStartHour($whichVar)
{
	return explode(":", $this->firstHour[$whichVar]);
}

/**
 * @return array
 * @param string $whichVar
 * @desc get booking end hour 
*/
function getEndHour($whichVar)
{
	return explode(":", $this->lastHour[$whichVar]);
}

/**
 * save club logo after the INSERT request - always an update request
 *
 */
function saveLogo()
{
	$objectDatabase = $this->objectConnexion;
	if ($this->isNewLogo()) { 
		$club_logo_data = 	addslashes(fread(fopen($_FILES['club_logo_picture']['tmp_name'],'rb'), $_FILES['club_logo_picture']['size']));
		$logo_query 	= 	'UPDATE clubs SET 	LOGO="'.$club_logo_data.'", 
												LOGO_NAME="'.$_FILES['club_logo_picture']['name'].'", 
												LOGO_EXT="'.$_FILES['club_logo_picture']['type'].'",
												LOGO_SIZE="'.$_FILES['club_logo_picture']['size'].'" WHERE NUM="'.$this->reference.'"';
		$objectDatabase->connect();
		$objectDatabase->query($logo_query);
		$objectDatabase->query('OPTIMIZE TABLE clubs');
	}
}

/**
 * @return string
 * @param string $whereAmI
 * @desc show the SELECT form for ICAO code
*/
function showICAO($whereAmI)
{
	$objectDatabase = $this->objectConnexion;
	// creation d'un select avec les codes OACI d'a&eacute;rodromes.
	$selectPlace = '<select name="icao_place" size="1" onchange="is_new_icao()">';
	$selectPlace .= '<option value="other"> Autre... </option>';
	$objectDatabase->connect();
	$place_result = $objectDatabase->query('select * from icao order by name');
			while ($row = mysql_fetch_object($place_result)) {
				$selectPlace .= '<option value="'.$row->ICAO.'"';
				if ($row->ICAO == $this->icao[$whereAmI]) {
					$selectPlace .= ' selected';
				}
				$selectPlace .= '>'.$row->ICAO.' - '.stripslashes($row->NAME).'</option>\n';
			}
	$selectPlace .= '</select>';
	return $selectPlace;
}

/**
 * @return boolean
 * @desc is it a new ICAO code
*/
function isNewICAO()
{
	$objectDatabase = $this->objectConnexion;
	if (($_POST['icao_place']=='other') && (isset($_POST['airfield_oaci']))) {
		$objectDatabase->connect();
		$icaoResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM icao WHERE ICAO='".$_POST['airfield_oaci']."';"); // check if ICAO airfield was not in database
		if ($icaoResult == 0) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * save new ICAO airfield
 *
 */
function saveICAO()
{
	$objectDatabase = $this->objectConnexion;
	if ($this->isNewICAO()) {
		$result_place = $objectDatabase->query("INSERT INTO icao SET 	NAME='".addslashes(htmlentities($_POST['airfield_name']))."',
																	  	ICAO='".$_POST['airfield_oaci']."',
																		LAT='".$_POST['airfield_lat']."',
																		LON='".$_POST['airfield_long']."',
																		ALT='".$_POST['airfield_alt']."'");
		$result_place = $objectDatabase->query('OPTIMIZE TABLE icao'); // Optimise table every times data is inserted
	}
}

/**
 * @return boolean
 * @desc check if the club is already existing
*/
function isClubExisting()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$club_result = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM clubs WHERE NUM=".$this->reference);
	if ($club_result == 1) {
		return true; 				// this club is already existing
	} else {
		if ($club_result == 0) {	
			return false; 			// No match 
		} else {
			$this->error = true; 	// Report an error (duplicate identifier in our case)
			return false; 			// More than one match => error 
		}
	}
}

/**
 * list all languages available in lang directory
 *
 * @param string $myVarBis
 */
function listLanguage($myVarBis)
{
	if (!strlen($this->language[$myVarBis]) > 0) {
		$this->language[$myVarBis] == DEFAULT_LANG;
	}
	$langDirectory = './lang/';
	$files = glob($langDirectory."*.php");
	foreach ($files as $filename) {
		$filename = str_replace(".php", "", $filename);
		$filename = str_replace($langDirectory, "", $filename);
		echo('<option value="'.$filename.'"');
		if ($this->language[$myVarBis] == $filename) {
			echo(' selected'); 
		}
		echo('>'.$filename.'</option>');
	}
}

// END
}
?>