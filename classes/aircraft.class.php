<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * aircraft.class.php
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
 * @version    CVS: $Id: aircraft.class.php,v 1.30.2.4 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

// *************************************************************************************
// WARNING! conf file and database connection class MUST be called before this class!!!!
// *************************************************************************************

/**
 * This class is designed in order to manage aircrafts in database.
 *
 */
class aircraft {
// BEGIN 

	/**
	 * Aircraft ID (key to process data)
	 *
	 * @var integer
	 */
	var $reference; 			// dedicated - reference of the aircraft
	/**
	 * DEPRECATED
	 *
	 * @var string
	 */
	var $status;				// dedicated - new or old aircraft
	/**
	 * This property reports any processing error - return true if an error is encountered
	 *
	 * @var boolean
	 */
	var $error = false;
	/**
	 * Content error information (each error encountered is stored here)
	 *
	 * @var array
	 */
	var $resultTab = array();	// dedicated - error or success messages

	/**
	 * Aircraft's callsign
	 *
	 * @var string
	 */
	var $callsign;

	/**
	 * Aircraft's non bookable flag
	 *
	 * @var string
	 */
	var $nonBookable;

	/**
	 * This property contains the type of the aircraft
	 *
	 * @var string
	 */
	var $type;
	/**
	 * This property contains the aircraft's flight hour cost
	 *
	 * @var string
	 */
	var $flight_hour_costs;
	/**
	 * Contains how many POB are available in this aircraft
	 *
	 * @var integer
	 */
	var $seats_available;
	/**
	 * It contains any comment regarding the aircraft (use restriction, license requirement, et caetera)
	 *
	 * @var string
	 */
	var $comments;
	/**
	 * Aircrafts rank (used to select aircrafts' order via "ORDER BY" clause in SQL queries
	 *
	 * @var unknown_type
	 */
	var $rank;
	/**
	 * Database connection handler
	 *
	 * @var ressource
	 */
	var $objectConnexion;

/**
 * @return void
 * @param integer $aircraftReference
 * @param integer $databaseId
 * @desc basic initialisation of the class
*/
function aircraft($aircraftReference, $databaseId)
{
	$this->reference = $aircraftReference;
	$this->objectConnexion = $databaseId;
}

/**
 * @return array
 * @desc This method list all aircrafts stored in database
*/
function getAllAircrafts()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$aircraftList = array();
	$result = $objectDatabase->query('select NUM from aircrafts ORDER BY ORDER_NUM');
	while ($row = mysql_fetch_object($result)) {
		$aircraftList[] = $row->NUM;
	}
	return $aircraftList;
}

/**
 * @return int
 * @desc This method count aircrafts stored in database
*/
function countAircrafts()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$userResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM aircrafts");
	return $userResult;
}

/**
 * @return void
 * @desc This method allow to switch the aircraft status is switched (active/asleep). Must be asleep to allow delete function. NOT IMPLEMENTED : Next Version
*/
function switchAircraftStatus()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
}

/**
 * @return void
 * @desc This method checks the aircraft's callsign. It should not be empty or already existing.
 */
function checkCallsign()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if (strlen($this->callsign['form']) == 0) {
		$this->error = true;
		$this->resultTab[] = 'EMPTY_CALLSIGN_FIELD';
	}
	$result = $objectDatabase->query_and_fetch_single('select count(*) from aircrafts WHERE CALLSIGN="'.$this->callsign['form'].'"');
	if (($result != 0) && ($this->status == "new")) {
		$this->error = true;
		$this->resultTab[] = 'CALLSIGN_ALREADY_EXISTING';
	}
}

/**
 * @desc This method checks if number of seats is numeric
 * @return void
*/
function checkSeats()
{
	if (!is_numeric($this->seats_available['form'])) {
		$this->error = true;
		$this->resultTab[] = 'NON_NUMERIC_FORMAT';
	}
}

/**
 * @return void
 * @desc This method is used to initialise the object data
*/
function acft_init()
{
	if ($this->isAircraftExisting()) {
		$this->status = "old"; 		// aircraft already existing
		$this->getAircraftFromDatabase();		// get the details about aircraft reference
		$this->getAircraftFromForm();		// get the details sent by form
	} else {
		$this->status = "new"; 		// new aircraft
		$this->getAircraftFromForm();		// get the details sent by form
	}
}

/**
 * @return void
 * @param string $which_var
 * @desc This method creates a "blank" aircraft
*/
function createBlankAircraft($which_var)
{
	$this->callsign[$which_var] = '';
	$this->nonBookable[$which_var] = 0;
	$this->type[$which_var] = '';
	$this->flight_hour_costs[$which_var] = '';
	$this->seats_available[$which_var] = '';
	$this->comments[$which_var] = '';
}

/**
 * @return integer
 * @desc This method allows to get the highest aircraft's rank in database. 
*/
function getMaxRank()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$aircraftQueryResult = $objectDatabase->query("SELECT * FROM aircrafts");
	while ($row = mysql_fetch_object($aircraftQueryResult)) {
		$aircraft_rank[] = $row->ORDER_NUM;
	}
	sort($aircraft_rank, SORT_NUMERIC);
	$maxRank = $aircraft_rank[count($aircraft_rank)-1];
	$maxRank++;
	return $maxRank;
}

/**
 * @return void
 * @desc This method allows to get data from the database about the aircraft "reference" 
*/
function getAircraftFromDatabase()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$aircraftQueryResult = mysql_fetch_object($objectDatabase->query("SELECT * FROM aircrafts WHERE NUM='".$this->reference."';"));
	$this->callsign['init'] 			= stripslashes($aircraftQueryResult->CALLSIGN); 
	$this->nonBookable['init']          = $aircraftQueryResult->non_bookable?1:0;
	$this->type['init'] 				= stripslashes($aircraftQueryResult->TYPE);
	$this->flight_hour_costs['init']	= stripslashes($aircraftQueryResult->FLIGHT_HOUR_COSTS);
	$this->seats_available['init']	 	= $aircraftQueryResult->SEATS_AVAILABLE;
	$this->comments['init'] 			= stripslashes($aircraftQueryResult->COMMENTS);
}

/**
 * @return void
 * @desc This method allows to get data submitted by form
*/
function getAircraftFromForm()
{
	$this->callsign['form'] 			= $_POST['aircraft_callsign']; 
	$this->nonBookable['form'] 		    = define_variable('aircraft_non_bookable',0);
	$this->type['form'] 				= $_POST['aircraft_type'];
	$this->flight_hour_costs['form'] 	= $_POST['aircraft_hourlycost'];
	$this->seats_available['form'] 		= $_POST['aircraft_payload'];
	$this->comments['form'] 			= $_POST['aircraft_comments'];
}

/**
 * @return void
 * @desc This method saves all information in the database about the aircraft "reference" : update data if already existing, insert data if a new one
*/
function saveAircraft()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	if ($this->status == "old") {
		$acft_request = "UPDATE aircrafts SET CALLSIGN='".setSlashes(htmlentities($this->callsign['form']))."', non_bookable='".$this->nonBookable['form']."', TYPE='".addslashes(htmlentities($this->type['form']))."', FLIGHT_HOUR_COSTS='".setSlashes(htmlentities($this->flight_hour_costs['form']))."', SEATS_AVAILABLE='".$this->seats_available['form']."', COMMENTS='".setSlashes(htmlentities($this->comments['form']))."' WHERE NUM='".$this->reference."'";
	}
	if ($this->status == "new") {
		if ($this->countAircrafts() == 0) {
			$my_rank = 0;
		} else {
			$my_rank = $this->getMaxRank();
			$my_rank++;
		}
		$acft_request = "INSERT INTO aircrafts SET CALLSIGN='".setSlashes(htmlentities($this->callsign['form']))."', non_bookable='".$this->nonBookable['form']."', TYPE='".addslashes(htmlentities($this->type['form']))."', FLIGHT_HOUR_COSTS='".setSlashes(htmlentities($this->flight_hour_costs['form']))."', SEATS_AVAILABLE='".$this->seats_available['form']."', COMMENTS='".setSlashes(htmlentities($this->comments['form']))."', ORDER_NUM='".$my_rank."'";
	}
	$this->checkCallsign();
	$this->checkSeats();
	if (!$this->error) {
		$aircraftQueryResult = $objectDatabase->query($acft_request);
		$this->error = !$aircraftQueryResult;
		if ($this->error) {
			$this->resultTab[] = 'ERROR_WITH_MYSQL'; // create a new local var => error
		} else {
			$this->resultTab[] = 'PROCESSED';
		}
	}
}

/**
 * @return boolean
 * @param boolean $delete_bookings
 * @desc This method delete aircraft's entry in database. If true is sent, all bookings with this aircraft are deleted
*/
function deleteAircraft($delete_bookings)
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$aircraftQueryResult = $objectDatabase->query("DELETE FROM aircrafts WHERE NUM='".$this->reference."';");
	if ($aircraftQueryResult) {
		$this->resultTab[] = 'PROCESSED';
	} else {
		$this->error = true;
		$this->resultTab[] = ''; // check!!!
	}
	return $aircraftQueryResult;
}

/**
 * @return boolean
 * @desc This method checks if the aircraft reference is already existing
*/
function isAircraftExisting()
{
	$objectDatabase = $this->objectConnexion;
	$objectDatabase->connect();
	$aircraftQueryResult = $objectDatabase->query_and_fetch_single("SELECT count(*) FROM aircrafts WHERE NUM='".$this->reference."'");
	if ($aircraftQueryResult == 1) {
		return true; // aircraft is already existing
	} else {
		if ($aircraftQueryResult == 0) {	
			return false; // No match 
		} else {
			$this->error = true; 	// Report an error (a duplicate identifier in our case)
			return false; 		// More than one match => error 
		}
	}
}

// END
}
?>