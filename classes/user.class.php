<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * user.class.php
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
 * @version    CVS: $Id: user.class.php,v 1.74.2.10 2006/06/19 07:49:19 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

/* *************************************************************************************
// WARNING! conf file and database connection class MUST be called before this class!!!!
// *************************************************************************************/

require_once('./classes/profile.class.php');
require_once('./classes/club.class.php');
require_once('./classes/db.class.php');
require_once('./conf/connect.php');

/**
 * contains all properties and methods in order to manage users' data (OOP).
 *
 */
class user	{
//BEGIN

	/**
	 * dedicated - reference of the user (num)
	 *
	 * @var integer
	 */
	var $reference;
	var $memberNum;
	/**
	 * dedicated - new or old user
	 *
	 * @var unknown_type
	 */
	var $status;
	/**
	 * dedicated - error handler
	 *
	 * @var boolean
	 */
	var $error = false;
	/**
	 * dedicated - success and error messages 
	 *
	 * @var array
	 */
	var $resultTab = array();
	
	/**
	 * user's login
	 *
	 * @var string
	 */
	var $name;
	/**
	 * user's password (MD5 crypted in database)
	 *
	 * @var string
	 */
	var $password;
	/**
	 * password length (checked if == 0)
	 *
	 * @var integer
	 */
	var $passwordLength;
	/**
	 * dedicated id number for OpenFlyers
	 *
	 * @var integer
	 */
	var $num;
	/**
	 * user's first name
	 *
	 * @var string
	 */
	var $firstName;
	/**
	 * user's last name
	 *
	 * @var string
	 */
	var $lastName;
	/**
	 * sum of id(s) of profile
	 *
	 * @var integer
	 */
	var $profiles;
	/**
	 * view preferences (menu,bookings)
	 *
	 * @var integer
	 */
	var $viewType;
	/**
	 * width of bookings table cells
	 *
	 * @var integer
	 */
	var $viewWidth;
	/**
	 * e-mail address if available
	 *
	 * @var string
	 */
	var $email;
	/**
	 * e-mail or SMS notification
	 *
	 * @var integer
	 */
	var $notify;
	/**
	 * postal address (zipcode, city, state and country follow)
	 *
	 * @var string
	 */
	var $address;
	/**
	 * user's zipcode
	 *
	 * @var integer
	 */
	var $zipcode;
	/**
	 * user's city
	 *
	 * @var string
	 */
	var $city;
	/**
	 * user's state
	 *
	 * @var string
	 */
	var $state;
	/**
	 * user's country
	 *
	 * @var string
	 */
	var $country;
	
	/**
	 * user's homephone number
	 *
	 * @var integer
	 */
	var $homephone;
    /**
     * user's phone number at work
     *
     * @var integer
     */
    var $workphone;
    /**
     * Cellphone number (Important for SMS or MMS notification)
     *
     * @var integer
     */
    var $cellphone;
    /**
     * user's language
     *
     * @var string
     */
    var $language;
    /**
     * user's timezone (bookings are converted in Z / UT
     *
     * @var unknown_type
     */
    var $timezone;
	/**
	 * database connexion reference
	 *
	 * @var unknown_type
	 */
	var $objectConnexion;
	/**
	 * CSV data to be stored
	 *
	 * @var unknown_type
	 */
	var $csvImport;
	var $csvSeparator;

/**
 * @return void
 * @param resource $databaseId
 * @desc Instanciation (PHP4)
*/
function user($databaseId)
{
	$this->objectConnexion = $databaseId;
}

/**
 * @return void
 * @desc check if first and last name are filled
*/
function checkFirstLastName()
{
	if (strlen($this->firstName['form']) == 0) {
		$this->error = true;
		$this->resultTab[] = 'NO_FIRST_NAME';
	}
	if (strlen($this->lastName['form']) == 0) {
		$this->error = true;
		$this->resultTab[] = 'NO_LAST_NAME';
	}
}

/**
 * @return integer
 * @desc Count users
*/
function countUsers()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM authentication");
	return $userResult;
}

/**
 * @return integer
 * @desc Count members
*/
function countMembers()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM members");
	return $userResult;
}

/**
 * @return integer
 * @desc Count instructors
*/
function countInstructors()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM instructors");
	return $userResult;
}

/**
 * @return void
 * @desc initialise user
*/
function initUser()	
{
	if ($this->isUserExisting()) {
		$this->status = "old"; 			// aircraft already existing
		$this->getUserFromDatabase('init');	// get the details about aircraft reference
		$this->getUserFromForm('form');		// get the details sent by form
	} else {
		$this->status = "new"; 			// new aircraft
		$this->getUserFromForm('form');	// get the details sent by form
	}
}

/**
 * @return void
 * @param string $whichVar
 * @desc Get user's data from database
*/
function getUserFromDatabase($whichVar) 
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = mysql_fetch_object($objectDatabase->query("SELECT * FROM authentication WHERE NUM='".$this->reference."'"));
	$this->name[$whichVar] 			= $userResult->NAME;
	if ($this->isMember()) {
		$this->memberNum[$whichVar]	= $objectDatabase->query_and_fetch_single('SELECT MEMBER_NUM FROM members WHERE NUM="'.$this->reference.'"');
	} else {
		$this->memberNum[$whichVar] = '';
	}
	$this->password[$whichVar] 		= $userResult->PASSWORD;
	$this->firstName[$whichVar] 		= stripslashes($userResult->FIRST_NAME);
	$this->lastName[$whichVar]	 		= stripslashes($userResult->LAST_NAME);
	$this->profiles[$whichVar] 		= $userResult->PROFILE;
	$this->viewType[$whichVar] 		= $userResult->VIEW_TYPE;
	$this->viewWidth[$whichVar]	 	= $userResult->VIEW_WIDTH;
	$this->email[$whichVar] 			= $userResult->EMAIL;
	$this->address[$whichVar]			= stripslashes($userResult->ADDRESS);
	$this->zipcode[$whichVar]			= $userResult->ZIPCODE;
	$this->city[$whichVar]				= stripslashes($userResult->CITY);
	$this->state[$whichVar]			= stripslashes($userResult->STATE);
	$this->country[$whichVar]			= stripslashes($userResult->COUNTRY);
	$this->homephone[$whichVar]		= $userResult->HOME_PHONE;
	$this->workphone[$whichVar]		= $userResult->WORK_PHONE;
	$this->cellphone[$whichVar]		= $userResult->CELL_PHONE;
	$this->notify[$whichVar]			= $userResult->NOTIFICATION;
	$temporaryClub = new club($this->objectConnexion);
	$temporaryClub->getClubFromDatabase();
	if (strlen($userResult->LANG) > 0) {
		$this->language[$whichVar]			= $userResult->LANG;
	} elseif (strlen($temporaryClub->language['init'])  > 0) {
		$this->language[$whichVar] = $temporaryClub->language['init']; // if clubs.LANG exists, default user language is the same
	} else {
		$this->language[$whichVar] = DEFAULT_LANG; // if clubs.LANG does not exist, DEFAULT_LANG is used for this user.
	}
	if (strlen($userResult->TIMEZONE) > 0) {
		$this->timezone[$whichVar]			= $userResult->TIMEZONE;
	} elseif (strlen($temporaryClub->timezone['init']) > 0) {
		$this->timezone[$whichVar] = $temporaryClub->timezone['init'];
	} else {
		$this->timezone[$whichVar] = 'UTC';
	}
	unset($temporaryClub);
}

/**
 * @return void
 * @param string $whichVar
 * @desc get user's data from a submitted form
*/
function getUserFromForm($whichVar) 
{
	$objectDatabase 					= $this->objectConnexion;
	$default_language 					= $objectDatabase->query_and_fetch_single('SELECT LANG FROM clubs WHERE clubs.NUM="1"');
	$default_timezone 					= $objectDatabase->query_and_fetch_single('SELECT DEFAULT_TIMEZONE FROM clubs WHERE clubs.NUM="1"');
	$user_basic_profile_rq		 		= define_variable('basic_profile_value',0);
	$userColorhelpRq 					= define_variable('user_colorhelp',0);
	$userEnglishdateRq 					= define_variable('user_englishdate',8);
	$userShowInstRq 					= define_variable('user_show_inst',16);
	$userShowAircraftRq 				= define_variable('user_show_aircraft',32);
	$user_show_homephone 				= define_variable('user_show_homephone',0);
	$user_show_workphone 				= define_variable('user_show_workphone',0);
	$user_show_cellphone 				= define_variable('user_show_cellphone',0);
	$user_show_email					= define_variable('user_show_email',0);
	$this->memberNum[$whichVar]		= define_variable('user_member_num', '');
	$this->name[$whichVar] 			= $_POST['user_name'];
	$this->password[$whichVar]		 	= $_POST['user_password'];
	if (isset($_POST['user_first_name'])) { 
		$this->firstName[$whichVar] 	= htmlentities($_POST['user_first_name']); 
	} else {
		$this->firstName[$whichVar] 	= ''; 
	}
	if (isset($_POST['user_last_name'])) { 
		$this->lastName[$whichVar]	= htmlentities($_POST['user_last_name']); 
	} else {
		$this->lastName[$whichVar] 	= ''; 
	}
	$userProfileRq = 0;
	if (isset($_POST['user_profile'])) {
		$userProfileRq = array_sum($_POST['user_profile']);
	}
	$this->profiles[$whichVar] 		= $userProfileRq + $user_basic_profile_rq;
	$this->language[$whichVar]			= define_variable('user_language', $default_language);
	$this->timezone[$whichVar]			= define_variable('user_timezone', $default_timezone);
	$this->viewType[$whichVar]	 		= $userColorhelpRq + $userEnglishdateRq + $userShowInstRq + $userShowAircraftRq + $user_show_cellphone + $user_show_homephone + $user_show_workphone + $user_show_email; 
//	$this->viewWidth[$whichVar] 		= '';
	$this->address[$whichVar]			= setSlashes(htmlentities(nl2br($_POST['user_address'])));
	$this->zipcode[$whichVar]			= $_POST['user_zipcode'];
	$this->city[$whichVar]				= setSlashes(htmlentities(nl2br($_POST['user_city'])));
	$this->state[$whichVar]				= setSlashes(htmlentities(nl2br($_POST['user_state'])));
	$this->country[$whichVar]			= setSlashes(htmlentities(nl2br($_POST['user_country'])));
	$this->homephone[$whichVar]			= $_POST['user_homephone'];
	$this->workphone[$whichVar]			= $_POST['user_workphone'];
	$this->cellphone[$whichVar]			= $_POST['user_cellphone'];
	$this->email[$whichVar] 			= $_POST['user_email']; 
	if (isset($_POST['user_notify'])) {
		$this->notify[$whichVar] = array_sum($_POST['user_notify']);
	} else {
		$this->notify[$whichVar] = 0;
	}
	$this->newmember					= $this->isNewMember();
	$this->newinstructor				= $this->isNewInstructor();
}

/**
 * @return void
 * @param string $whichVar
 * @desc Create a blank user with default parameters
*/
function createBlankUser($whichVar) 
{
	$objectDatabase = $this->objectConnexion;
	$default_profiles = $objectDatabase->query_and_fetch_single('SELECT USUAL_PROFILES FROM clubs WHERE clubs.NUM="1"');
	$this->memberNum[$whichVar] = '';
	$this->name[$whichVar] 		= '';
	$this->password[$whichVar]		= '';
	$this->firstName[$whichVar] 	= '';
	$this->lastName[$whichVar]	 	= '';
	$this->profiles[$whichVar] 	= $default_profiles;
	$this->viewType[$whichVar]	 	= 960;
	$this->viewWidth[$whichVar] 	= 20;
	$this->address[$whichVar]		= '';
	$this->zipcode[$whichVar]		= '';
	$this->city[$whichVar]			= '';
	$this->state[$whichVar]		= '';
	$this->country[$whichVar]		= '';
	$this->homephone[$whichVar]	= '';
	$this->workphone[$whichVar]	= '';
	$this->cellphone[$whichVar]	= '';
	$this->email[$whichVar] 		= '';
	$temporaryClub = new club($this->objectConnexion);
	$temporaryClub->getClubFromDatabase();
	if (strlen($temporaryClub->language['init'])  > 0) {
		$this->language[$whichVar] = $temporaryClub->language['init']; // if clubs.LANG exists, default user language is the same
	} else {
		$this->language[$whichVar] = DEFAULT_LANG; // if clubs.LANG does not exist, DEFAULT_LANG is used for this user.
	}
	if (strlen($temporaryClub->timezone['init']) > 0) {
		$this->timezone[$whichVar] = $temporaryClub->timezone['init'];
	} else {
		$this->timezone[$whichVar] = 'UTC';
	}
	unset($temporaryClub);
	$this->notify[$whichVar]		= 0;
}

/**
 * @return array
 * @desc List all instructor
*/
function getAllInstructors()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$result = $objectDatabase->query('select INST_NUM from instructors ORDER BY ORDER_NUM');
	while ($row = mysql_fetch_object($result)) {
		$instructor_list[] = $row->INST_NUM;
	}
	return $instructor_list;
}

/**
 * @return boolean
 * @param string $loginToCheck
 * @desc Check if login is unique or not
*/
function checkLoginUnicity($loginToCheck)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$login_unicity = $objectDatabase->query_and_fetch_single('SELECT count(*) FROM authentication WHERE NAME="'.$loginToCheck.'"');
	if ($login_unicity > 0) {
		return false;
	} else {
		return true;
	}
}

/**
 * @return void
 * @desc Check login and password are compliant to our security rules.
*/
function checkLoginPwdContent()
{
	$mypwd = '';
	$mylogin = '';
	if (strlen($this->name['form']) == 0) {
		$this->error = true;
		$this->resultTab[] = 'LOGIN_IS_EMPTY';
	} else {
		for ($boucle = 0; $boucle < strlen($this->name['form']); $boucle++) {
			if (strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $this->name['form'][$boucle])) {
				$mylogin .= $this->name['form'][$boucle];
			}
		}
		if (strlen($mylogin) != strlen($this->name['form'])) {
			$this->error = true;
			$this->resultTab[] = 'LOGIN_WITH_UNAUTHORIZED_SYMBOL';
		}
	}
	if (isset($_POST['user_password_confirmation']) && ($this->password['form'] != $_POST['user_password_confirmation']))  {
		$this->error = true;
		$this->resultTab[] = 'PASSWORD_OR_LOGIN_ERROR';
	} else {
		$this->passwordLength = strlen($this->password['form']);
		for ($boucle = 0; $boucle < strlen($this->password['form']); $boucle++) {
			if (strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $this->password['form'][$boucle])) {
				$mypwd .= $this->password['form'][$boucle];
			}
		}
		if (strlen($mypwd) != strlen($this->password['form'])) {
			$this->error = true;
			$this->resultTab[] = 'PASSWORD_WITH_UNAUTHORIZED_SYMBOL';
		}
	}
}

/**
 * @return int
 * @desc Get max rank from instructor in database. 
*/
function getMaxRank()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$inst_result = $objectDatabase->query("SELECT * FROM instructors");
	while ($row = mysql_fetch_object($inst_result)) {
		$instructor_rank[] = $row->ORDER_NUM;
	}
	sort($instructor_rank, SORT_NUMERIC);
	$max_rank = $instructor_rank[count($instructor_rank)-1];
	$max_rank++;
	return $max_rank;
}


/**
 * CSV Import or Update Part // NOT IMPLEMENTED
 *
 */
function getCSVConfig()
{
	$this->csv_import = '';
	$field_table = array();
	$this->csv_separator = $_POST['separator_type'];
	if (isset($_POST['LOGIN_TRUE'])) {
		$field_table[] = 'NAME';
	} 
	$field_table[] = 'PASSWORD';
	if (isset($_POST['OFID_TRUE'])) {
		$field_table[] = 'NUM';
	}
	$field_table[] = 'LAST_NAME';
	$field_table[] = 'FIRST_NAME';
	if (isset($_POST['MAIL_TRUE'])) {
		$field_table[] = 'EMAIL';
	}
	if (isset($_POST['ADDRESS_TRUE'])) {
		$field_table[] = 'ADDRESS';
	}
	if (isset($_POST['ZIP_TRUE'])) {
		$field_table[] = 'ZIPCODE';
	}
	if (isset($_POST['CITY_TRUE'])) {
		$field_table[] = 'CITY';
	}
	if (isset($_POST['STATE_TRUE'])) {
		$field_table[] = 'STATE';
	}
	if (isset($_POST['COUNTRY_TRUE'])) {
		$field_table[] = 'COUNTRY';
	}
	if (isset($_POST['PHONE_TRUE'])) {
		$field_table[] = 'HOME_PHONE';
	}
	if (isset($_POST['WORKPHONE_TRUE'])) {
		$field_table[] = 'WORK_PHONE';
	}
	if (isset($_POST['CELL_TRUE'])) {
		$field_table[] = 'CELL_PHONE';
	}
	$this->csv_import = '('.implode(',', $field_table).')';
}

/**
 * @return void
 * @desc Import a list of user via a uploaded CSV-formatted file
*/
function importFromCSV()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$separator_type = $_POST['separator_type'];
	$temporaryClub = new club($objectDatabase);
	$temporaryClub->getClubFromDatabase();
	if (strlen($temporaryClub->language['init']) > 0) {
		$languageCSV = $temporaryClub->language['init'];
	} else {
		$languageCSV = DEFAULT_LANG;
	}
	if (strlen($temporaryClub->timezone['init']) > 0) {
		$timezoneCSV = $temporaryClub->timezone['init'];
	} else {
		$timezoneCSV = 'UTC';
	}
    $import_type                = define_variable('import_type',false);
	$user_show_homephone 		= define_variable('user_show_homephone',0);
	$user_show_workphone 		= define_variable('user_show_workphone',0);
	$user_show_cellphone 		= define_variable('user_show_cellphone',0);
	$userColorhelpRq 			= define_variable('user_colorhelp',0);
	$userEnglishdateRq 			= define_variable('user_englishdate',0);
	$userProfileRq 				= array_sum($_POST['user_profile']);
	$userShowInstRq 			= define_variable('user_show_inst',16);
	$userShowAircraftRq 		= define_variable('user_show_aircraft',32);
	$user_show_email			= define_variable('user_show_email',0);
	$clubML 					= $temporaryClub->mlName['init'];
	unset($temporaryClub);
	if (isset($_FILES['filename']['tmp_name']) && ($_FILES['filename']['size'] > 0)) {
		if ($file=fopen($_FILES['filename']['tmp_name'],'r')) {
			while ($userCSVdata = fgetcsv($file,2048,$separator_type)) { // read a line (CSV file)
                if ((!$import_type) or !($this->getMember($userCSVdata[0]))) {
                    // processing last name to create a correct login
                    $mylogin = '';
                    $userCSVdata[1] = strtolower(trim($userCSVdata[1])); // trim and shift to lower case
                    for ($boucle = 0; $boucle < strlen($userCSVdata[1]); $boucle++) {
                        if (strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $userCSVdata[1][$boucle])) {
                            $mylogin .= $userCSVdata[1][$boucle];
                        }
                    }
                    // last name processed, now processing first name to get the first letter
                    $boucle = 0;
                    while ( ($boucle < strlen($userCSVdata[2])) && !strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $userCSVdata[2][$boucle]) ) {
                        $boucle++;
                    }
                    if ($boucle != strlen($userCSVdata[2])) {
                        $mylogin = strtolower($userCSVdata[2][$boucle]).$mylogin;
                    }
                    // we've got our login
                    $rank = 2;
                    $testLogin = $mylogin;
                    while (!$this->checkLoginUnicity($testLogin)) {
                        $testLogin = $mylogin.$rank;
                        $rank++;
                    }
                    $mylogin = $testLogin;
                    // and now we are sure that this login is unique
                    // checking password
                    $mypassword = '';
                    for ($boucle = 0; $boucle < strlen($userCSVdata[0]); $boucle++) {
                        if (strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $userCSVdata[0][$boucle])) {
                            $mypassword .= $userCSVdata[0][$boucle];
                        }
                    }
                    $mypassword = strtolower($mypassword);
                    // password checked
                    $this->viewType['form']		= $userColorhelpRq + $userEnglishdateRq + $userShowInstRq + $userShowAircraftRq + $user_show_cellphone + $user_show_homephone + $user_show_workphone + $user_show_email;
                    $this->name['form'] 		= $mylogin;
                    $this->password['form'] 	= $mypassword;
                    $this->profiles['form'] 	= $userProfileRq;
                    $this->firstName['form'] 	= ucfirst(trim($userCSVdata[2]));
                    $this->lastName['form'] 	= strtoupper(trim($userCSVdata[1]));
                    $this->email['form'] 		= strtolower(trim($userCSVdata[3]));
                    //				test_mail_list($user_club_number_rq,$user_email_rq);
                    $this->address['form']		= trim($userCSVdata[4]);
                    $this->zipcode['form']		= trim($userCSVdata[5]);
                    $this->city['form']			= trim($userCSVdata[6]);
                    $this->state['form']		= trim($userCSVdata[7]);
                    $this->country['form']		= trim($userCSVdata[8]);
                    $this->homephone['form']	= trim($userCSVdata[9]);
                    $this->workphone['form']	= trim($userCSVdata[10]);
                    $this->cellphone['form']	= trim($userCSVdata[11]);
                    $this->language['form']		= $languageCSV;
                    $this->timezone['form']		= $timezoneCSV;
                    $this->saveUser();
                    $this->reference = $objectDatabase->query_and_fetch_single('SELECT NUM FROM authentication WHERE NAME="'.$this->name['form'].'"');
                    $this->setMember();
                    $this->updateMember($userCSVdata[0]);
                    echo($this->firstName['form'].' '.$this->lastName['form'].' => OK <br />');
                    $this->reference = -1;
                    if ((isset($_POST['subscribe'])) && (strlen($this->email['form']) > 0) && (strlen($clubML) > 0)) {
                        require_once('./classes/mailing_list.class.php');
                        $temporaryML = new mailing_list($objectDatabase);
                        $temporaryML->add_email($this->email['form']);
                        unset($temporaryML);
                    }
                }
			}
			$this->resultTab[] = 'CSV_PROCESSED';
		}
	} else {
		$this->resultTab[] = 'FILE_READ_ERROR';
	}	
}



/**
 * Create a login from scraps. It works on first name and last name to display a unique login
 *
 * @param string $firstName
 * @param string $lastName
 * @return string
 */
function createLogin($firstName, $lastName)
{
	// processing last name to create a correct login
	$mylogin = '';
	$lastName = strtolower(trim($lastName)); // trim and shift to lower case
	for ($boucle = 0; $boucle < strlen($lastName); $boucle++) {
		if (strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $lastName[$boucle])) {
			$mylogin .= $lastName[$boucle];
		}
	}
	// last name processed, now processing first name to get the first letter
	$boucle = 0;
	while ( ($boucle < strlen($firstName)) && !strstr('azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890_', $firstName[$boucle]) ) {
		$boucle++;
	}
	if ($boucle != strlen($firstName)) {
		$mylogin .= $firstName[$boucle];
	}
	// we've got our login
	while (!$this->checkLoginUnicity($mylogin)) {
		$mylogin .= mt_rand(0,999);
	}
	// and now we are sure that this login is unique
	return $mylogin;
}

/**
 * Enter description here...
 *
 * @param unknown_type $lastName
 * @param unknown_type $firstName
 * @return unknown
 *
function updateCheck($lastName, $firstName)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$myresult = $objectDatabase->query_and_fetch_single('SELECT count(NUM) FROM authentication WHERE LAST_NAME="'.addslashes($lastName).'" AND FIRST_NAME="'.addslashes($firstName).'"');	
	if ($myresult > 0) { 
		return true;
	} else {
		return false;
	}
}
*/

/**
 * @return void
 * @desc save data to the database
*/
function saveUser() 
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$this->checkFirstLastName();
	if ((!$this->checkLoginUnicity($this->name['form'])) && ($this->name['form'] != $this->name['init'])) {
		$this->error = true;
		$this->resultTab[] = 'LOGIN_ALREADY_EXISTING';
	}
	if ($this->isNewInstructor() && (strlen($_POST['user_inst_trigramme']) == 0)) {
		$this->error = true;
		$this->resultTab[] = 'NO_TRIGRAM';
	}
	$this->checkLoginPwdContent();
	if ($this->isUserExisting()) {
		if ($this->passwordLength == 0) {
			$userQuery = 'UPDATE authentication SET 	NAME="'.strtolower($this->name['form']).'", 
														FIRST_NAME="'.setSlashes($this->firstName['form']).'", 
														LAST_NAME="'.setSlashes($this->lastName['form']).'", 
														PROFILE="'.$this->profiles['form'].'", 
														LANG="'.$this->language['form'].'", 
														TIMEZONE="'.$this->timezone['form'].'",
														VIEW_TYPE="'.$this->viewType['form'].'",
														ADDRESS="'.$this->address['form'].'",
														ZIPCODE="'.$this->zipcode['form'].'",
														CITY="'.$this->city['form'].'",
														STATE="'.$this->state['form'].'",
														HOME_PHONE="'.$this->homephone['form'].'",
														WORK_PHONE="'.$this->workphone['form'].'",
														CELL_PHONE="'.$this->cellphone['form'].'",
														COUNTRY="'.$this->country['form'].'",
														EMAIL="'.$this->email['form'].'",
														NOTIFICATION="'.$this->notify['form'].'"
														WHERE NUM="'.$this->reference.'"';
		} else {
			$userQuery = 'UPDATE authentication SET 	NAME="'.strtolower($this->name['form']).'", 
														PASSWORD="'.md5($this->password['form']).'", 
														FIRST_NAME="'.setSlashes($this->firstName['form']).'", 
														LAST_NAME="'.setSlashes($this->lastName['form']).'", 
														PROFILE="'.$this->profiles['form'].'", 
														LANG="'.$this->language['form'].'", 
														TIMEZONE="'.$this->timezone['form'].'",
														VIEW_TYPE="'.$this->viewType['form'].'",
														ADDRESS="'.$this->address['form'].'",
														ZIPCODE="'.$this->zipcode['form'].'",
														CITY="'.$this->city['form'].'",
														STATE="'.$this->state['form'].'",
														HOME_PHONE="'.$this->homephone['form'].'",
														WORK_PHONE="'.$this->workphone['form'].'",
														CELL_PHONE="'.$this->cellphone['form'].'",
														COUNTRY="'.$this->country['form'].'",
														EMAIL="'.$this->email['form'].'",
														NOTIFICATION="'.$this->notify['form'].'"
														WHERE NUM="'.$this->reference.'"';
		}
	} else {
		$userQuery = 'INSERT INTO authentication SET 	NAME="'.$this->name['form'].'", 
														PASSWORD="'.md5($this->password['form']).'", 
														FIRST_NAME="'.setSlashes($this->firstName['form']).'", 
														LAST_NAME="'.setSlashes($this->lastName['form']).'", 
														PROFILE="'.$this->profiles['form'].'", 
														LANG="'.$this->language['form'].'",
														TIMEZONE="'.$this->timezone['form'].'",
														VIEW_TYPE="'.$this->viewType['form'].'", 
														ADDRESS="'.$this->address['form'].'",
														ZIPCODE="'.$this->zipcode['form'].'",
														CITY="'.$this->city['form'].'",
														STATE="'.$this->state['form'].'",
														COUNTRY="'.$this->country['form'].'",
														HOME_PHONE="'.$this->homephone['form'].'",
														WORK_PHONE="'.$this->workphone['form'].'",
														CELL_PHONE="'.$this->cellphone['form'].'",
														EMAIL="'.$this->email['form'].'",
														NOTIFICATION="'.$this->notify['form'].'"';
	}
	if (!$this->error) {
		$result = $objectDatabase->query($userQuery);
		$this->error = !$result;
		if ($this->error) {
			$this->resultTab[] = 'REQUEST_FAILED';
		} else {
			// if request is successfull, subscribtion to ML is next step - 1st of all we check if it's existing.
			$temporaryClub = new club($objectDatabase);
			$temporaryClub->getClubFromDatabase();
			if ((isset($_POST['subscribe'])) && (strlen($temporaryClub->mlName['init']) > 0) && (strlen($this->email['form']) > 0)) {
				require_once('./classes/mailing_list.class.php');
				$temporaryML = new mailing_list($objectDatabase);
				$temporaryML->add_email($this->email['form']);
				$temporaryML->free();
				unset($temporaryML);
			}
			if ((isset($_POST['unsubscribe'])) && (strlen($temporaryClub->mlName['init']) > 0) && (strlen($this->email['form']) > 0)) {
				require_once('./classes/mailing_list.class.php');
				$temporaryML = new mailing_list($objectDatabase);
				$temporaryML->remove_email($this->email['form']);
				$temporaryML->free();
				unset($temporaryML);
			}
			unset($temporaryClub);
			// something very dirty follow : we disconnect because,
			// previously with database access in mailing_list we may have
			// access to another database
			$objectDatabase->disconnect();
			$this->reference = $objectDatabase->query_and_fetch_single('SELECT NUM FROM authentication WHERE NAME="'.$this->name['form'].'"');
			if ($this->isMember()) {
				if (isset($_POST['user_member_num'])) {
					$this->updateMember($_POST['user_member_num']);
				}
			}
			if ($this->isNewMember()) {
				$this->setMember();
				if (isset($_POST['user_member_num'])) {
					$this->updateMember($_POST['user_member_num']);
				}
			} else {
				$this->deleteMember();
			}
			if ($this->isNewInstructor()) {
				$this->setInstructor();
			} else {
				$this->deleteInstructor();
			}
			$this->resultTab[] = 'PROCESSED';
		}
	}
}

/**
 * @return boolean
 * @param unknown $clear_all
 * @desc Remove user entry from database
*/
function deleteUser($clear_all, $user_acting)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if ($this->reference != $user_acting->getAuthNum()) {
		$userResult = $objectDatabase->query("DELETE FROM authentication WHERE NUM='".$this->reference."'");
		if($userResult) {
			$this->resultTab[] = 'PROCESSED';
		}
	} else {
		$this->error = true;
		$this->resultTab[] = 'CANNOT_DELETE_YOURSELF';
		$userResult = false;
	}
	return $userResult;
}

/**
 * Check if SMS notification is activated for this user
 *
 * @param string $whichVar
 * @return boolean
 */
function notifySMS($whichVar)
{
	return (($this->notify[$whichVar]&2)>>1);
}

/**
 * Check if mail notification is activated for this user
 *
 * @param string $whichVar
 * @return boolean
 */
function notifyMail($whichVar)
{
	return (($this->notify[$whichVar]&1)>>0);
}

/**
 * Update subscription date for users listed in $memberList
 *
 * @param array $membersList
 */
function updateMembersSubscriptionDate($membersList)
{
	$boucle = 0;
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$temporaryConfig = new APIconfig($objectDatabase);
	$temporaryConfig->getSubscriptionConfig();
	$validationDate = $temporaryConfig->subscriptionDate;
	unset($temporaryConfig);
	while (isset($membersList[$boucle])) {
		$isSuccess = $objectDatabase->query('UPDATE members SET SUBSCRIPTION =\''.$validationDate.'\' WHERE NUM=\''.$membersList[$boucle].'\'');
		$this->resultTab[] = 'PROCESSED';
		if (!$isSuccess) {
			$this->error = true;
			$this->resultTab[] = 'PROBLEM_ENCOUNTERED_CHECK_DATA';
		}
		$boucle++;
	}
}

/**
 * @return boolean
 * @desc check if the user is a new or an old one
*/
function isUserExisting()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM authentication WHERE NUM='".$this->reference."'");
	if ($userResult == 1) {
		return true; // user is already existing
	} else {
		if ($userResult == 0) {	
			return false; // No match 
		} else {
			$this->error = 1; 	// Report an error (a duplicate identifier in our case)
			return false; 		// More than one match => error 
		}
	}
}

/**
 * @return authentication Num
 * @desc check if the user is a new or an old one
*/
function getMember($memberNum)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$result = $objectDatabase->query_and_fetch_single('SELECT NUM FROM members WHERE MEMBER_NUM=\''.$memberNum.'\' LIMIT 1');
	if ($result) {
		return $result; // user is already existing
	} else {
		return false;
	}
}

/**
 * @return boolean
 * @param integer $compared_permits
 * @desc check if the current user has a higher rank than the user logged
*/
function isHigherRank($compared_permits)
{
	$objectDatabase = $this->objectConnexion;
	$list_of_profiles = $this->getUserProfiles();
	$club_admin = false;
	$profile_list = new profile($objectDatabase);
	$maxOfProfiles = count($list_of_profiles);
	for ($i = 0; $i < $maxOfProfiles; $i++) {
		$profile_list->getProfileFromDatabase($list_of_profiles[$i]);
		if ($profile_list->isSetClubParameters()) {
			$club_admin = true;
		}
	}
	switch ($compared_permits) {
		case 0 :
			$valueToBeReturned = false;
			break;
		case 1 :
			$valueToBeReturned = false;
			break;
		case 2 :
			if ($club_admin) {
				$valueToBeReturned = true;
			} else {
				$valueToBeReturned = false;
			} 
			break;
	}
	return $valueToBeReturned;
}

/**
 * @return array
 * @param string $order_by
 * @desc List all user num from a club ordered by the parameters order_by
*/
function getAllUser($order_by)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userList = array();
	switch ($order_by) {
	case "INSTRUCTORS" :
		$result = $objectDatabase->query('select authentication.NUM from instructors left join authentication on authentication.NUM=instructors.INST_NUM ORDER BY LAST_NAME');
		break;
	case "MEMBERS" :
		$result = $objectDatabase->query('select authentication.NUM from members left join authentication on authentication.NUM=members.NUM ORDER BY LAST_NAME');
		break;
	default :
		$result = $objectDatabase->query('select NUM from authentication ORDER BY '.$order_by);
		break;
	}
	while ($row = mysql_fetch_object($result)) {
		$userList[] = $row->NUM;
	}
	return $userList;
}

/**
 * @return array
 * @desc List profiles ids of this user
*/
function getUserProfiles()
{
	$returnedArray = array();
	$my_binary_profile = strrev(decbin($this->profiles['init']));
	for ($newLoop = 0; $newLoop < strlen($my_binary_profile); $newLoop ++) {
		if ($my_binary_profile[$newLoop] == 1) {
			$returnedArray[] = pow(2, $newLoop);
		}
	}
	return $returnedArray;
}

/**
 * @return boolean
 * @desc check if this user is a member
*/
function isMember() {
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM members WHERE NUM='".$this->reference."'");
	if ($userResult == 1) {
		return true; // user is a member
	} else {
		if ($userResult == 0) {	
			return false; // No match - user is not a member
		} else {
			$this->error = 1; 	// Report an error (a duplicate identifier in our case)
			return false; 		// More than one match => error 
		}
	}
}

/**
 * @return boolean
 * @desc check if member box is checked
*/
function isNewMember() {
	return isset($_POST['user_type_member']);
}

/**
 * @return boolean
 * @desc check if instructor box is checked
*/
function isNewInstructor() {
	return isset($_POST['user_type_instructor']);
}

/**
 * @return boolean
 * @desc  check if this user is an instructor
*/
function isInstructor() {
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM instructors WHERE INST_NUM='".$this->reference."'");
	if ($userResult == 1) {
		return true; // user is an instructor
	} else {
		if ($userResult == 0) {	
			return false; // No match - user is not an instructor
		} else {
			$this->error = 1; 	// Report an error (a duplicate identifier in our case)
			return false; 		// More than one match => error 
		}
	}
}

/**
 * @return void
 * @desc save or update data if user is a member
*/
function setMember() 
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$subscription_on = $objectDatabase->query_and_fetch_single('SELECT ENABLED FROM parameter WHERE CODE=\'SUBSCRIPTION\'');
	if ($subscription_on != 0) {
		$validationDate = $objectDatabase->query_and_fetch_single('SELECT CHAR_VALUE FROM parameter WHERE CODE=\'SUBSCRIPTION\'');
		if ($this->isMember()) {
//			$userResult = $objectDatabase->query('UPDATE members SET WHERE NUM="'.$this->reference.'", SUBSCRIPTION="'.$validationDate.'"');
		} else {
			$userResult = $objectDatabase->query('INSERT INTO members SET NUM="'.$this->reference.'", SUBSCRIPTION="'.$validationDate.'"');
		}
	} else {
	    $temporaryConfig = new APIconfig($objectDatabase);
	    $temporaryConfig->getSubscriptionConfig();
	    $validationDate = $temporaryConfig->subscriptionDate;
	    if ($this->isMember()) {
//			$userResult = $objectDatabase->query('UPDATE members SET WHERE NUM="'.$this->reference.'", SUBSCRIPTION="'.$validationDate.'"');
		} else {
			$userResult = $objectDatabase->query('INSERT INTO members SET NUM="'.$this->reference.'", SUBSCRIPTION="'.$validationDate.'"');
		}
	}
}

/**
 * Update member num in members table
 *
 * @param string $newMemberNum
 */
function updateMember($newMemberNum)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if ($this->isMember()) {
		$userResult = $objectDatabase->query('UPDATE members SET MEMBER_NUM="'.$newMemberNum.'" WHERE NUM="'.$this->reference.'"');
	}
}

/**
 * Get all licenses from a member. All data are included in the returned array
 *
 * @param integer $localReference
 * @return array
 */
function getQualifOfMember($localReference)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$localLoop = 0;
	$userLicenses = array();
	$local_license = new license(-1, $this->objectConnexion);
	$userResult = $objectDatabase->query('SELECT * FROM member_qualif WHERE MEMBERNUM="'.$localReference.'"');
	while ($row = mysql_fetch_object($userResult)) {
		$userLicenses[$localLoop]['id'] 		= $row->QUALIFID;
		$userLicenses[$localLoop]['expire'] 	= $row->EXPIREDATE;
		$userLicenses[$localLoop]['alert'] 	= $row->NOALERT;
		$local_license->reference = $row->QUALIFID;
		$local_license->getLicenseFromDatabase();
		$userLicenses[$localLoop]['license_name'] = $local_license->name;
		$userLicenses[$localLoop]['license_endless']= $local_license->time_limit;
		$localLoop++;
	}
	return $userLicenses;
}

/**
 * @return void
 * @desc save or update data if user is an instructor
*/
function setInstructor() {
	if ($this->isInstructor()) {
		$objectDatabase = $this->objectConnexion;
		$objectDatabase->connect();
		$userResult = $objectDatabase->query('UPDATE instructors SET SIGN="'.$_POST['user_inst_trigramme'].'" WHERE INST_NUM="'.$this->reference.'"');
	} else {
		$objectDatabase = $this->objectConnexion;
		$objectDatabase->connect();
		$userResult = $objectDatabase->query('INSERT INTO instructors SET INST_NUM="'.$this->reference.'", SIGN="'.$_POST['user_inst_trigramme'].'", ORDER_NUM="'.$this->getMaxRank().'"');
	}
}

/**
 * @return string
 * @desc Get instructor's data if this user is an instructor
*/
function getInstructor() {
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single('select SIGN from instructors where INST_NUM="'.$this->reference.'"');
	return $userResult;
}

/**
 * @return void
 * @desc Remove member status for this user
*/
function deleteMember() 
{
	if ($this->isMember()) {
		$objectDatabase = $this->objectConnexion;
		$objectDatabase->connect();
		$userResult = $objectDatabase->query("DELETE FROM members WHERE NUM='".$this->reference."'");
	}
}

/**
 * @return void
 * @desc Remove the user from instructors list
*/
function deleteInstructor() 
{
	if ($this->isInstructor()) {
		$objectDatabase = $this->objectConnexion;
		$objectDatabase->connect();
		$userResult = $objectDatabase->query("DELETE FROM instructors WHERE INST_NUM='".$this->reference."'");
	}
}

/**
 * @return date
 * @desc Get subscription date from a member
*/
function getOutOfDate()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT SUBSCRIPTION FROM members WHERE NUM='".$this->reference."'");
	return $userResult;
}

/**
 * Add a license to a specified member
 *
 */
function addMemberLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if (isset($_POST['warn_limit'])) {
		$alert_license = 1;
	} else {
		$alert_license = 0;
	}
	$localDate = $_POST['yearDate'].'-'.$_POST['monthDate'].'-'.$_POST['dayDate'];
	$userResult = $objectDatabase->query('INSERT INTO member_qualif SET MEMBERNUM="'.$this->reference.'", QUALIFID="'.$_POST['newone'].'", EXPIREDATE="'.$localDate.'", NOALERT="'.$alert_license.'"');
}

/**
 * Remove a license for a user
 *
 */
function removeMemberLicense()
{
	$objectDatabase = $this->objectConnexion;
	$userResult = $objectDatabase->query('DELETE FROM member_qualif WHERE MEMBERNUM="'.$this->reference.'" AND QUALIFID="'.$_POST['qualif_id'].'"');
}

/**
 * Update a member's license
 *
 */
function updateMemberLicense()
{
	$objectDatabase = $this->objectConnexion;
	$localDate = $_POST['yearDate'].'-'.$_POST['monthDate'].'-'.$_POST['dayDate'];
	if (isset($_POST['alert_license'])) {
		$alert_license = 1;
	} else {
		$alert_license = 0;
	}
	$userResult = $objectDatabase->query('UPDATE member_qualif SET EXPIREDATE="'.$localDate.'", NOALERT="'.$alert_license.'" WHERE MEMBERNUM="'.$this->reference.'" AND QUALIFID="'.$_POST['qualif_id'].'"');
}

/**
 * Display a SELECT content to choose language
 *
 * @param string $localVar
 * @return string
 */
function listLanguage($localVar)
{
	$languageList = '';
	if (!(strlen($this->language[$localVar]) > 0)) {
		$temporaryClub = new club($this->objectConnexion);
		$temporaryClub->getClubFromDatabase();
		if (strlen($temporaryClub->language['init'])  > 0) {
			$this->language[$localVar] = $temporaryClub->language['init'];
		} else {
			$this->language[$localVar] = DEFAULT_LANG;
		}
	}
	$langDirectory = './lang/';
	$files = glob($langDirectory."*.php");
	foreach ($files as $filename) {
		$filename = str_replace(".php", "", $filename);
		$filename = str_replace($langDirectory, "", $filename);
		$languageList .='<option value="'.$filename.'"';
		if ($this->language[$localVar] == $filename) {
			$languageList .= ' selected'; 
		}
		$languageList .= '>'.$filename.'</option>';
	}
	return $languageList;
}

/**
 * list all admin from the database (returned value is a formatted string)
 *
 * @param integer $currentAdminID
 * @return string
 */
function listAdmin($currentAdminID)
{
	// initialisation
	$returnedArray = '';
	// get all user and count them
	$adminList = $this->getAllUser('NAME');
	$adminListSize = count($adminList);
	$adminProfile = array();
	// get all profile with admin or super admin rights (filtered profiles)
	$temporaryProfile = new profile($this->objectConnexion);
	$profiles_tab = $temporaryProfile->getAllProfile();
	$maxProfiles = count($profiles_tab);
	for ($userLoop = 0; $userLoop < $maxProfiles; $userLoop++) {
		// Get permits from profiles
		$temporaryProfile->getProfileFromDatabase($profiles_tab[$userLoop]);
		if ($temporaryProfile->IsSetClubParameters()) {
			$adminProfile[] = $temporaryProfile->reference; // stored IDs are profiles with admin or/and super admin rights
		}
	}
	// now we've got all admin profiles
	if(count($adminProfile) > 1) { 
		$sumOfAdminProfiles = array_sum($adminProfile);
	} else {
		$sumOfAdminProfiles = $adminProfile;
	}
	for ($userLoop = 0; $userLoop < $adminListSize; $userLoop++) {
		$this->reference = $adminList[$userLoop];
		$this->getUserFromDatabase('init');
		if (($this->profiles['init'] & $sumOfAdminProfiles) != 0) {
			$returnedArray .= '<option value="'.$this->reference.'"';
			if ($this->reference == $currentAdminID) {
				$returnedArray .= ' selected'; 
			}
			$returnedArray .= '>'.$this->lastName['init'].'</option>'; 
		}
	}
	return $returnedArray;
}

// END
}
?>