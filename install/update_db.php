<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * update_db.php
 *
 * Update Database tables
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
 * @category   install
 * @author     Soeren MAIRE
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: update_db.php,v 1.19.2.2 2007/10/03 09:35:58 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Jun 10 2003
 */

// CAUTION 
//Need an opened connection to openflyers database
	
	
	Special_Check_MultiClub();
	
	//****************
	// AIRCRAFTS table updates
	//****************
	
	Create_Or_Update_Field('aircrafts','NUM', 'NUM', 'INT(10) UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT' ,'PRIMARY KEY','FIRST');
	Create_Or_Update_Field('aircrafts','FLIGHT_HOUR_COSTS', 'FLIGHT_HOUR_COSTS', 'VARCHAR(255)', 'NOT NULL', '' ,'','AFTER CLUB_NUM');
	Create_Or_Update_Field('aircrafts','COMMENTS', 'COMMENTS', 'VARCHAR(255)', 'NOT NULL', '' ,'','AFTER SEATS_AVAILABLE');
	Special_Create_Aircrafts_Order_Num();
	Add_Or_Replace_Index('aircrafts','ORDER_NUM',array('ORDER_NUM'));
	Drop_Field('aircrafts','CLUB_NUM');
	Create_Or_Update_Field('aircrafts','non_bookable', 'non_bookable', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','AFTER ORDER_NUM');
	
	//****************
	// AIRCRAFT_QUALIF table updates
	//****************
	Create_Table('aircraft_qualif','REQUESTED QUALIFICATIONS FOR EACH AIRCRAFT', 'AIRCRAFTNUM','INT(10) UNSIGNED', 'NOT NULL', '', '');
	Create_Or_Update_Field('aircraft_qualif','CHECKNUM', 'CHECKNUM', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('aircraft_qualif','QUALIFID', 'QUALIFID', 'INT(10) UNSIGNED', 'NOT NULL', '' ,'','');
	Add_Or_Replace_Primary_Key('aircraft_qualif',array('AIRCRAFTNUM','CHECKNUM','QUALIFID'));
	
	
	//****************
	// AUTHENTIFICATION table updates
	//****************
	
  Create_Or_Update_Field('authentication','NUM', 'NUM', 'INT(10) UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT' ,'PRIMARY KEY','FIRST');
  Create_Or_Update_Field('authentication','VIEW_TYPE', 'VIEW_TYPE', 'INT(10) UNSIGNED', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','AIRCRAFTS_VIEWED', 'AIRCRAFTS_VIEWED', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','INST_VIEWED', 'INST_VIEWED', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','TIMEZONE', 'TIMEZONE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','ADDRESS', 'ADDRESS', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','ZIPCODE', 'ZIPCODE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','CITY', 'CITY', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','STATE', 'STATE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','COUNTRY', 'COUNTRY', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','HOME_PHONE', 'HOME_PHONE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','WORK_PHONE', 'WORK_PHONE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','CELL_PHONE', 'CELL_PHONE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Special_Encrypt_Passwords();
  Create_Or_Update_Field('authentication','LANG', 'LANG', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('authentication','NOTIFICATION', 'NOTIFICATION', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','');
	Special_Set_View_type_Vertical_0();
	Special_Set_Menu_type_1();
	Drop_Field('authentication','CLUB_NUM');
	
	//****************
	// BOOKING table updates
	//****************
	
	Create_Or_Update_Field('booking','AIRCRAFT_NUM', 'AIRCRAFT_NUM', 'INT(10) UNSIGNED', 'NOT NULL', '' ,'','AFTER END_DATE');
	Create_Or_Update_Field('booking','COMMENTS', 'COMMENTS', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
	Special_Remove_Booking_Field();
	Drop_Field('booking','CLUB_NUM');
	Create_Or_Update_Field('booking','ID', 'ID', 'INT UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT' ,'PRIMARY KEY','FIRST');
  	
	//****************
	// CLUB table updates
	//****************
	
	Drop_Index('clubs','NUM');
  Add_Or_Replace_Primary_Key('clubs',array('NUM'));
	Create_Or_Update_Field('clubs','CLUB_SITE_URL', 'CLUB_SITE_URL', 'VARCHAR(255)', 'NOT NULL', 'default \'\'' ,'','');
	//Create_Or_Update_Field('clubs','SUBSCRIPTION_DEFAULT', 'SUBSCRIPTION_DEFAULT', 'DATE', 'NOT NULL', 'default \'2000-12-31\'' ,'','');
	//Create_Or_Update_Field('clubs','OUTDATE_SUBSCRIPTION_PROFILE', 'OUTDATE_SUBSCRIPTION_PROFILE', 'MEDIUMINT(8)', 'NOT NULL', 'default \'0\'' ,'','');
	Create_Or_Update_Field('clubs','DEFAULT_TIMEZONE', 'DEFAULT_TIMEZONE', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('clubs','LANG', 'LANG', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('clubs','ADMIN_NUM', 'ADMIN_NUM', 'INT UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('clubs','MAIL_FROM_ADDRESS', 'MAIL_FROM_ADDRESS', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
	Drop_Field('booking','CLUB_NUM');

	//****************
	// EXCEPTIONNAL_INST_DATES
	//****************
	
	//Nothing
	
	//****************
	// ICAO table updates
	//****************
	
	Add_Or_Replace_Primary_Key('icao',array('ICAO'));
	Create_Or_Update_Field('icao','NOM', 'NAME', 'VARCHAR(64)', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('icao','ALT', 'ALT', 'SMALLINT', 'NOT NULL', '' ,'','');
	
	//****************
	// IP_STOPPED table updates
	//****************
	
	Create_Table('ip_stopped','BLACKLISTED IP', 'IP_NUM','VARCHAR(255)', 'NOT NULL', '', 'PRIMARY KEY');
	Create_Or_Update_Field('ip_stopped','COUNTER', 'COUNTER', 'TINYINT UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('ip_stopped','EXPIRE_DATE', 'EXPIRE_DATE', 'DATETIME', 'NOT NULL', '' ,'','');
	
	//****************
	// INST_PLANNING table updates
	//****************
	
	Drop_Table('inst_planning');
	
	//****************
	// INSTRUCTORS table updates
	//****************
	Add_Or_Replace_Primary_Key('instructors',array('INST_NUM'));
	Special_Create_Instructor_Order_Num();
	Add_Or_Replace_Index('instructors','ORDER_NUM',array('ORDER_NUM'));
	
	//****************
	// LOGIN_STOPPED table updates
	//****************
	
	Create_Table('login_stopped','BLACKLISTED LOGIN', 'LOGIN','VARCHAR(255)', 'NOT NULL', '', 'PRIMARY KEY');
	Create_Or_Update_Field('login_stopped','COUNTER', 'COUNTER', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('login_stopped','EXPIRE_DATE', 'EXPIRE_DATE', 'DATETIME', 'NOT NULL', '' ,'','');
	
	//****************
	// LOGS table updates
	//****************
	
  Drop_Field('logs','CLUB_NUM');
	Create_Or_Update_Field('logs','MESSAGE', 'MESSAGE', 'TEXT', 'NOT NULL', '' ,'','');
	
	//****************
	// MEMBERS table updates
	//****************
	
	Drop_Index('members','NUM');
	Add_Or_Replace_Primary_Key('members',array('NUM'));
	Create_Or_Update_Field('members','SUBSCRIPTION', 'SUBSCRIPTION', 'DATE', 'NOT NULL', 'DEFAULT \'2003-12-31\'' ,'','');
	Create_Or_Update_Field('members','QUALIF_ALERT_DELAY', 'QUALIF_ALERT_DELAY', 'TINYINT(3) UNSIGNED', 'NOT NULL', 'DEFAULT 8' ,'','');
	
	//****************
	// MEMBER_QUALIF table updates
	//****************
	Create_Table('member_qualif','QUALIFICATIONS OF EACH MEMBER', 'MEMBERNUM','INT(10) UNSIGNED', 'NOT NULL', '', '');
	Create_Or_Update_Field('member_qualif','QUALIFID', 'QUALIFID', 'INT(10) UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('member_qualif','EXPIREDATE', 'EXPIREDATE', 'DATE', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('member_qualif','NOALERT', 'NOALERT', 'TINYINT(3) UNSIGNED', 'NOT NULL', 'DEFAULT 0' ,'','');
	Add_Or_Replace_Primary_Key('member_qualif',array('MEMBERNUM','QUALIFID'));
	
	//****************
	// PARAMETER table updates
	//****************
	
	Create_Table('parameter','APPLICATION PARAMETERS', 'CODE','VARCHAR(255)', 'NOT NULL', '', 'PRIMARY KEY');
	Create_Or_Update_Field('parameter','ENABLED', 'ENABLED', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','');
	Create_Or_Update_Field('parameter','INT_VALUE', 'INT_VALUE', 'INT(10) UNSIGNED', 'NULL', '' ,'','');
	Create_Or_Update_Field('parameter','CHAR_VALUE', 'CHAR_VALUE', 'VARCHAR(255)', 'NULL', '' ,'','');
	
	//****************
	// PROFILES table updates
	//****************
	
	Drop_Index('profiles','NUM');
	Add_Or_Replace_Primary_Key('profiles',array('NUM'));
	Create_Or_Update_Field('profiles','PERMITS', 'PERMITS', 'INT(10) UNSIGNED', 'NOT NULL', '' ,'','');
	Special_Set_Permits_Admin_0();
	Special_Set_Permits_OwnLimitation_0();
	Drop_Field('profiles','CLUB_NUM');
	
	//****************
	// QUALIFICATION table updates
	//****************
  Create_Table('qualification','QUALIFICATIONS LIST', 'ID','INT(10) UNSIGNED', 'NOT NULL', 'AUTO_INCREMENT', 'PRIMARY KEY');
  Create_Or_Update_Field('qualification','NAME', 'NAME', 'VARCHAR(255)', 'NOT NULL', '' ,'','');
  Create_Or_Update_Field('qualification','TIME_LIMITATION', 'TIME_LIMITATION', 'TINYINT(3) UNSIGNED', 'NOT NULL', '' ,'','');
  
  	
	//****************
	// REGULAR_PRESENCE_INST_DATES table updates
	//****************
	
	//Nothing
	
	//****************
	// SLOT_TYPES table updates
	//****************
	
	Drop_Table('slot_types');
	
	//****************
	// SR_SS table updates
	//****************
	
	Drop_Table('sr_ss'); 
	
	
	// Updates that need previous updates are allready done to be performed
	
	//Table CLUBS
	Special_set_parameters_subscription();
	Drop_Field('clubs','SUBSCRIPTION_DEFAULT');
	Drop_Field('clubs','OUTDATE_SUBSCRIPTION_PROFILE');
	

// *********************************
function Create_Table($Table_Name,$Table_Comment,$Field_Name,$Field_Type,$Field_Null,$Field_Extra,$Field_Key) {

  echo 'Cr�ation de la table '.$Table_Name.' si n�cessaire<br>';

  $SQL_Query='create table if not exists `'.$Table_Name.'` ('.$Field_Name.' '.$Field_Type.' '.$Field_Null.' '.$Field_Key.' '.$Field_Extra.') COMMENT=\''.$Table_Comment.'\'';
  @mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');
}	
	
// *********************************
function Drop_Table($Table_Name) {
	
	echo 'Destruction de la table '.$Table_Name.'<br>';
	
	$SQL_Query='drop table if exists `'.$Table_Name.'`';
	@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>');
}

// **********************************
function Add_Or_Replace_Primary_Key($Table_Name,$Field_Array) {
	
	echo 'Ajout/Remplacement de cl� primaire sur la table '.$Table_Name.'<br>';
	
	$result=mysql_query ('SHOW INDEX FROM `'.$Table_Name.'`');
	
	$Nb_Key_Found=0;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Key_name=='PRIMARY') {
    		foreach ($Field_Array as $Field_Name) {
    			if ($row->Column_name==$Field_Name ) 
    				$Nb_Key_Found++;	
    		}
    	}
    	    	
    }
    
    
    if ($Nb_Key_Found!=count($Field_Array)) {
		
		Drop_Primary_Key($Table_Name);
	
		$Field_List='';
		foreach ($Field_Array as $Field_Name) {
			$Field_List=$Field_List.''.$Field_Name.',';
		}
		$Field_List=substr($Field_List,0,strlen($Field_List)-1);
		
		$SQL_Query='alter table `'.$Table_Name.'` add primary key('.$Field_List.')';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>');
	}
}

// **********************************
function Drop_Primary_Key($Table_Name)
{
	echo 'Suppression de la cl� primaire de la table '.$Table_Name.'<br>';
		
	$result=mysql_query ('SHOW INDEX FROM `'.$Table_Name.'`');
	
	$Index_Exists=false;
	while ($row=mysql_fetch_object($result) and ! $Index_Exists) {

    	if ($row->Key_name=='PRIMARY') 
    		$Index_Exists=true;
    	
    }
    	
    if ($Index_Exists) {
    	
		$SQL_Query='alter table `'.$Table_Name.'` drop primary key';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>');
		
	}	

}

// **********************************
function Add_Or_Replace_Index($Table_Name,$Index_Name,$Field_Array) {
	
	echo 'Ajout/Remplacement de la cl� unique '.$Index_Name.' sur la table '.$Table_Name.'<br>';
	
	$result=mysql_query ('SHOW INDEX FROM `'.$Table_Name.'`');
	
	$Nb_Key_Found=0;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Key_name==$Index_Name) {
    		foreach ($Field_Array as $Field_Name) {
    			if ($row->Column_name==$Field_Name ) 
    				$Nb_Key_Found++;	
    		}
    	}
    	    	
    }
    
    
    if ($Nb_Key_Found!=count($Field_Array)) {
		
		Drop_Index($Table_Name,$Index_Name);
	
		$Field_List='';
		foreach ($Field_Array as $Field_Name) {
			$Field_List=$Field_List.''.$Field_Name.',';
		}
		$Field_List=substr($Field_List,0,strlen($Field_List)-1);
		
		$SQL_Query='alter table `'.$Table_Name.'` add unique '.$Index_Name.' ('.$Field_List.')';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>');
	}
}

// **********************************
function Drop_Index($Table_Name,$Index_Name) {
	
	echo 'Suppression de l\'index '.$Index_Name.' de la table '.$Table_Name.'<br>';
	
	$result=mysql_query ('SHOW INDEX FROM '.$Table_Name);
	
	$Index_Exists=false;
	while ($row=mysql_fetch_object($result) and ! $Index_Exists) {
    	if ($row->Key_name==$Index_Name) 
    		$Index_Exists=true;
    	
    }
    	
    if ($Index_Exists) {
	
		$SQL_Query='alter table `'.$Table_Name.'` drop index '.$Index_Name;
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>');
	}
}

// **********************************
function Create_Or_Update_Field ($Table_Name,$Old_Field_Name,$New_Field_Name,$Field_Type,$Field_Null,$Field_Extra,$Field_Key, $New_Field_Position)
{
	
	$result=mysql_query ('SHOW FIELDS FROM `'.$Table_Name.'`');
		
	$Old_Field_Exists=false;
	$New_Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field==$Old_Field_Name ) {
    		$Old_Field_Exists=true;
    		
    		if (strtolower($Field_Key)=='primary key' and $row->Key=='PRI') {
    			$Field_Key=''; 
    		}
    	}
    	elseif  ($row->Field==$New_Field_Name ) {
    		$New_Field_Exists=true;
    		
    		if (strtolower($Field_Key)=='primary key' and $row->Key=='PRI') {
    			$Field_Key=''; 
    		}
    	}
      else  {
    		
    		if (strtolower($Field_Key)=='primary key' and $row->Key=='PRI') {
    			
    			Drop_Primary_Key($Table_Name);
    		}
    	}
	}
	
	if ($Old_Field_Exists)
	{  
		echo 'Mise � jour du champ '.$Old_Field_Name.' de la table '.$Table_Name.'<br>';
			
		$SQL_Query='alter table `'.$Table_Name.'` change '.$Old_Field_Name.' '.$New_Field_Name.' '.$Field_Type.' '.$Field_Null.' '.$Field_Key.' '.$Field_Extra;
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');

	
	}
	elseif ($New_Field_Exists)
	{  
		echo 'Mise � jour du champ '.$New_Field_Name.' de la table '.$Table_Name.'<br>';
			
		$SQL_Query='alter table `'.$Table_Name.'` change '.$New_Field_Name.' '.$New_Field_Name.' '.$Field_Type.' '.$Field_Null.' '.$Field_Key.' '.$Field_Extra;
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');

	
	}
	else
	{
		echo 'Ajout d\'un champ '.$New_Field_Name.' � la table '.$Table_Name.'<br>';
		
		$SQL_Query='alter table `'.$Table_Name.'` add '.$New_Field_Name.' '.$Field_Type.' '.$Field_Null.' '.$Field_Key.' '.$Field_Extra.' '.$New_Field_Position;
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');

	}
	
}

// **********************************
function Drop_Field ($Table_Name,$Field_Name) {

	echo 'Suppression du champs '.$Field_Name.' de la table '.$Table_Name.'<br>';
	  
  $result=mysql_query ('SHOW FIELDS FROM `'.$Table_Name.'`');
		
	$Field_Exists=false;

	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field==$Field_Name ) {
    		$Field_Exists=true;
    	}

	}
	
	if ($Field_Exists) {
    
		$SQL_Query='alter table `'.$Table_Name.'` drop `'.$Field_Name.'`';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');
	
	}

}

// **********************************
//fill aircraft.NUM field and drop booking.AIRCRAFT_CALLSIGN field
function Special_Remove_Booking_Field() {
	
	echo 'Mise � jour de la table booking<br>';
	
	$result=mysql_query ('SHOW FIELDS FROM booking');
		
	$Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field=='AIRCRAFT_CALLSIGN' ) 
    		$Field_Exists=true;
   		
	}
	
	if ($Field_Exists) {
		//filling aircraft_num field
		$sql_query="select NUM as num, CALLSIGN as callsign from aircrafts";
		$result   = @mysql_query($sql_query) or die(mysql_error());
		
		for($i=0;$row=mysql_fetch_object($result);$i++)
		{
			$sql_query="update booking set aircraft_num=".$row->num." where aircraft_callsign='".$row->callsign."'";	
			@mysql_query($sql_query) or die('<b>'.mysql_error().'</b>');
					   	
		}	
		
		//delete AIRCRAFT_CALLSIGN field from booking table
		$sql_query="alter table booking drop AIRCRAFT_CALLSIGN";	
		@mysql_query($sql_query) or die('<b>'.mysql_error().'</b>');
	}
}

// **********************************
//set first bit (1 value) of view_type field to 0 in authentification table
function Special_Set_View_type_Vertical_0() {

  echo 'Mise � z�ro du bit vue verticale du champ View_type de la table authentification<br />';
  @mysql_query('update authentication set view_type = view_type -1 where (view_type & 1)=1') or die('<b>'.mysql_error().'</b>');
}

// **********************************
//set 2nd bit (2 value) of view_type field to 0 in authentification table
function Special_Set_Menu_type_1() {

  echo 'Mise � z�ro du bit menu du champ View_type de la table authentification<br />';
  @mysql_query('update authentication set view_type = view_type -2 where (view_type & 2)=2') or die('<b>'.mysql_error().'</b>');
}

// **********************************
//set first bit (1 value) of permits field to 0 in profiles table
function Special_Set_Permits_Admin_0() {

  echo 'Mise � z�ro du bit openflyers_admin du champ Permits de la table profiles et remplacement par un autre bit de niveau admin<br />';
  @mysql_query('update profiles set permits = (permits|256)-1 where (permits & 1)=1') or die('<b>'.mysql_error().'</b>');
}


// **********************************
//set 10th bit (1024 value) of permits field to 0 in profiles table
function Special_Set_Permits_OwnLimitation_0() {

  echo 'Mise � z�ro du bit SetOwnLimitation du champ Permits de la table profiles<br>';
  @mysql_query('update profiles set permits = permits -1024 where (permits & 1024)=1024') or die('<b>'.mysql_error().'</b>');
}

// **********************************
function Special_Create_Aircrafts_Order_Num() {

  $result=mysql_query ('SHOW FIELDS FROM aircrafts');
		
	$Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field=='ORDER_NUM' ) 
    		$Field_Exists=true;
   		
	}
	
	if (!$Field_Exists) {
		echo "Cr�ation du champs Order_Num de Aircrafts<br>";
    
    //Field creation		
		$SQL_Query='alter table `aircrafts` add ORDER_NUM INT(10) UNSIGNED NOT NULL';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');

    //Field initialisation
    $sql_query='update aircrafts set order_num=num';
    @mysql_query($sql_query) or die(mysql_error());
	}
  
}

// **********************************
function Special_Create_Instructor_Order_Num() {

  $result=mysql_query ('SHOW FIELDS FROM instructors');
		
	$Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field=='ORDER_NUM' ) 
    		$Field_Exists=true;
   		
	}
	
	if (!$Field_Exists) {
		echo "Cr�ation du champs Order_Num de Instructors<br>";
    
    //Field creation		
		$SQL_Query='alter table `instructors` add ORDER_NUM INT(10) UNSIGNED NOT NULL';
		@mysql_query($SQL_Query) or die('<b>'.mysql_error().'</b>('.$SQL_Query.')');

    //Field initialisation
    $sql_query='update instructors set order_num=inst_num';
    @mysql_query($sql_query) or die(mysql_error());
	}
	
}

// **********************************
function Special_Check_MultiClub() {

  $result=mysql_query ('select count(*) as nbClubs from clubs');
  $row=mysql_fetch_object($result);
  
  if ($row->nbClubs > 1) {
  
  	echo '<font color="red"><b>IMPOSSIBLE DE METTRE A JOUR</b><br>Votre base de donn&eacute;es comporte '.$row->nbClubs.' clubs<br>';
    echo 'OpenFlyers ne g&egrave;re plus le multiclubisme. Vous devez supprimer '.($row->nbClubs-1).' club(s) et relancer la mise � jour</font>';
    die();  
  }
  
}

function Special_Encrypt_Passwords() {

  //Check if LANG Fields exists in authentication
  //If not, it's time to encypt password
  $result=mysql_query ('SHOW FIELDS FROM authentication');
		
	$Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field=='LANG' ) 
    		$Field_Exists=true;
	}
	
	if (!$Field_Exists) {
    echo "Encryption des mots de passe<br>";
    
    $sql_query='update authentication set password = md5(password)';
    @mysql_query($sql_query) or die(mysql_error());
  }
}

function Special_set_parameters_subscription() {

  //Check if OUTDATE_SUBSCRIPTION_PROFILE or SUBSCRIPTION_DEFAULT Fields
  //exists in table clubs 
  $result=mysql_query ('SHOW FIELDS FROM clubs');
		
	$Field_Exists=false;
	while ($row=mysql_fetch_object($result)) {

    	if ($row->Field=='OUTDATE_SUBSCRIPTION_PROFILE' or $row->Field=='SUBSCRIPTION_DEFAULT' ) 
    		$Field_Exists=true;
   		
	}
	
	//If yes, values are copied in parameter table
	if ($Field_Exists) {
    $result=mysql_query ('Select OUTDATE_SUBSCRIPTION_PROFILE as profile,SUBSCRIPTION_DEFAULT as defdate FROM clubs limit 1');
    if ($row=mysql_fetch_object($result)) {
      $SQL_Query='insert into parameter(code,enabled,int_value,char_value) values (\'SUBSCRIPTION\',2,'.$row->profile;
      $SQL_Query=$SQL_Query.',\''.$row->defdate.'\')';
      @mysql_query($SQL_Query) or die(mysql_error());  
    }
    
  
  }

};
?>
