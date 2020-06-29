<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * license.class.php
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
 * @version    CVS: $Id: license.class.php,v 1.12.2.3 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// *************************************************************************************
// WARNING! conf file and database connection class MUST be called before this class!!!!
// *************************************************************************************

/**
 * License object
 *
 */
class license {
// BEGIN 

	/**
	 * License reference (unique)
	 *
	 * @var integer
	 */
	var $reference;
	/**
	 * Error handler - true if an error occured
	 *
	 * @var boolean
	 */
	var $error = false;
	/**
	 * All error messages are stored in this array
	 *
	 * @var array
	 */
	var $resultTab = array();
	
	/**
	 * License's name
	 *
	 * @var string
	 */
	var $name;
	/**
	 * Is set to true if the license could expiry
	 *
	 * @var boolean
	 */
	var $time_limit;

	/**
	 * Database connexion handler
	 *
	 * @var ressource
	 */
	var $objectConnexion;

/**
 * Object initialisation
 *
 * @param integer $licenseID
 * @param ressource $databaseId
 * @return license
 */
function license($licenseID, $databaseId)
{
	$this->reference = $licenseID;
	$this->objectConnexion = $databaseId;
}

/**
 * Get all licenses' ID stored in database
 *
 * @return array
 */
function getAllLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$licenseList = array();
	$result = $objectDatabase->query('select ID from qualification');
	while ($row = mysql_fetch_object($result)) {
		$licenseList[]	= $row->ID;
	}
	return $licenseList;
}

/**
 * Add a license to requirements for an aircrafts
 *
 * @param integer $aircraftID
 * @param integer $checknum_involved
 * @param integer $license_ID
 */
function addAircraftLicense($aircraftID, $checknum_involved, $license_ID)
{
	$objectDatabase = $this->objectConnexion;
	$result = $objectDatabase->query('INSERT INTO aircraft_qualif SET AIRCRAFTNUM="'.$aircraftID.'", CHECKNUM="'.$checknum_involved.'", QUALIFID="'.$license_ID.'"');
}

/**
 * Get all ID and Names of licenses stored in database
 *
 * @return array
 */
function getFullLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$licenseList = array();
	$result = $objectDatabase->query('select ID, NAME from qualification');
	while ($row = mysql_fetch_object($result)) {
		$licenseList[$row->ID] 	= $row->NAME;
	}
	return $licenseList;
}

/**
 * Count all licenses in database
 *
 * @return integer
 */
function countLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$license_result = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM qualification");
	return $license_result;
}


/**
 * Create a blank license
 *
 */
function createBlankLicense()
{
	$this->name = '';
	$this->time_limit = 0;
}


/**
 * Get all license's data from database
 *
 */
function getLicenseFromDatabase()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$license_result = mysql_fetch_object($objectDatabase->query("SELECT * FROM qualification WHERE ID='".$this->reference."'"));
	$this->name 		= stripslashes($license_result->NAME); 
	$this->time_limit 	= $license_result->TIME_LIMITATION;
}

/**
 * Get all license's data from submitted form
 *
 */
function getLicenseFromForm()
{
	$this->name 			= addslashes(htmlentities($_POST['license_name'])); 
	if (isset($_POST['license_timelimit'])) {
		$this->time_limit		= $_POST['license_timelimit'];
	} else {
		$this->time_limit		= 0;
	}
}

/**
 * Save a license in database
 *
 */
function saveLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if ($this->reference != "-1") {
		$license_request = "UPDATE qualification SET NAME='".setSlashes($this->name)."', TIME_LIMITATION='".$this->time_limit."' WHERE ID='".$this->reference."'";
	} else {
		$license_request = "INSERT INTO qualification SET NAME='".setSlashes($this->name)."', TIME_LIMITATION='".$this->time_limit."'";
	}
	if (!$this->error) {
		$license_result = $objectDatabase->query($license_request);
		$this->error = !$license_result;
		if ($this->error) {
			$this->resultTab[] = 'REQUEST_FAILED';
		} else {
			$this->resultTab[] = 'PROCESSED';
		}
	}
}

/**
 * Delete this license (return true if completed)
 *
 * @return boolean
 */
function deleteLicense()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	// 1st : remove this license from members license list
	$license_result = $objectDatabase->query("DELETE FROM member_qualif WHERE QUALIFID='".$this->reference."';");
	// 2nd : remove this license from aircraft requirements
	$license_result = $objectDatabase->query("DELETE FROM aircraft_qualif WHERE QUALIFID='".$this->reference."';");
	// 3rd : remove the license
	$license_result = $objectDatabase->query("DELETE FROM qualification WHERE ID='".$this->reference."';");
	if ($license_result) {
		$this->resultTab[] = 'LICENSE_WAS_DELETED';
	} else {
		$this->error = 1;
		$this->resultTab[] = 'LICENSE_WAS_NOT_DELETED';
	}
	return $license_result;
}

/**
 * Get all licenses' requirements for an aircraft
 *
 * @param integer $aircraftID
 * @return array
 */
function getAircraftLicense($aircraftID)
{
	$license_table = array();
	$objectDatabase = $this->objectConnexion;
	$list_of_licenses = $objectDatabase->query('SELECT * FROM aircraft_qualif WHERE AIRCRAFTNUM="'.$aircraftID.'" ORDER BY CHECKNUM');
	while ($licenseList = mysql_fetch_object($list_of_licenses)) {
		$license_table[$licenseList->CHECKNUM][] = $licenseList->QUALIFID;
	}
	return $license_table;
}

/**
 * Display a "select" field in order to choose a license
 *
 * @param string $myVarName
 */
function getSelectLicense($myVarName)
{
	$list_of_licence = $this->getFullLicense();
	echo('<select name="'.$myVarName.'" size="1">');
	foreach ($list_of_licence as $license_id => $licence_checknum) {
		echo('<option value="'.$license_id.'">'.$licence_checknum.'</option>');
	}
	echo('</select>');
}

/**
 * Display a "select" field in order to choose a license
 *
 * @param string $myVarName
 */
function getUpdatedSelectLicense($myVarName, $licenseListAlreadyUsed = array())
{
	$list_of_licence = $this->getFullLicense();
	echo('<select name="'.$myVarName.'" size="1">');
	foreach ($list_of_licence as $license_id => $licence_checknum) {
		if (!in_array($license_id, $licenseListAlreadyUsed)) {
			echo('<option value="'.$license_id.'">'.$licence_checknum.'</option>');
		}
	}
	echo('</select>');
}

/**
 * Remove a license form aircraft's requirements
 *
 * @param integer $aircraftID
 * @param integer $target_ID
 * @param integer $target_checknum
 */
function removeAircraftLicense($aircraftID, $target_ID, $target_checknum)
{
	$objectDatabase = $this->objectConnexion;
	$remove_license = $objectDatabase->query('DELETE FROM aircraft_qualif WHERE AIRCRAFTNUM="'.$aircraftID.'" AND CHECKNUM="'.$target_checknum.'" AND QUALIFID="'.$target_ID.'"');
}

// END
}
?>