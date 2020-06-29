<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * userSession.class.php
 *
 * centralize session data
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
 * @category   session
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: userSession.class.php,v 1.14.2.12 2007/04/29 19:01:34 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Jan 20 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// Pear RFC822 class
require_once('Mail/RFC822.php');

require_once('./pool/functions.php');
require_once('./classes/permits.php');
require_once('./classes/booking.class.php');
require_once('./classes/mail.class.php');
require_once('./classes/serie.class.php');
require_once('./classes/qualification.class.php');
require_once('./classes/parameter.class.php');

class userSession
{
    // public variables
    var $parameter;
    
    // protected variables
    var $db;    // database object to access... database !
    var $adminMail;
    var $adminName;
    var $clubIsLogo;
    var $clubLang;
    var $clubName;
    var $clubUrl;

   /**
     * Constructor
     *
     * Creates a new userSession
     *
     * @access public
     * @param $database database object
     * @return null
     */
	function userSession($database)
	{
        $this->db=$database;
        $this->parameter=new parameter();
        $this->parameter->getFromDatabase($this->db);
		session_start();
		$this->adminMail='';
		$this->adminName='';
		$this->clubLang='';
		$this->clubName='';
		$this->clubUrl='';
		$this->clubIsLogo=false;
	    $this->db->query('select clubs.LOGO, clubs.NAME, clubs.CLUB_SITE_URL, clubs.LANG, authentication.FIRST_NAME, authentication.LAST_NAME, authentication.EMAIL from clubs left join authentication on authentication.NUM=clubs.ADMIN_NUM');
	    if($this->db->numRows()==1)
	    {
	        $row=$this->db->fetch();
	        $this->clubLang=$row->LANG;
	        $this->clubName=$row->NAME;
	        $this->clubUrl=$row->CLUB_SITE_URL;
	        $this->adminMail=$row->EMAIL;
	        $this->adminName=$row->FIRST_NAME.' '.$row->LAST_NAME;
	        if ($row->LOGO!='')
	        {
	           $this->clubIsLogo=true;
	        }
	    }
        $this->db->free();
	}

	/**
	 * generate an ident to identify the user & the club
	 *
	 * @return string
	 */
	function generateUserSessionId() {
		return 'i:'.$_SERVER['REMOTE_ADDR'].';n:'.$this->clubName.';h:'.$_SERVER['HTTP_HOST'];
	}
	
	/**
	 * set the UserSessionId in the SESSION
	 *
	 */
	function setUserSessionId() {
		$_SESSION['userSessionId'] = $this->generateUserSessionId();
	}
	
	/**
	 * check the UserSessionId
	 *
	 * @return boolean
	 */
	function isValidUserSessionId() {
		return (!isset($_SESSION['userSessionId']) || ($_SESSION['userSessionId'] === $this->generateUserSessionId()));
	}

	function quit()
	{
        if (!session_id())
        {
            session_start();
        }
		$_SESSION=array();
		session_destroy();
	}

   /**
     * getSessionEndLife
     *
     * @access private
     * @param null
     * @return ofDate or false
     */
    function getSessionEndLife()
    {
        if (isset($_SESSION['SESSION_END_LIFE']))
        {
            return ($_SESSION['SESSION_END_LIFE']);
        }
        else
        {
            return false;
        }
    }

   /**
     * setSessionEndLife
     *
     * @access private
     * @param $menu integer used to choose session max time type
     * @return null
     */
    function setSessionEndLife($date,$menu)
    {
        $lifeTime=new ofDateSpan();
        if (($menu==7) or ($this->isNoAutoLogout()))
        {
            $lifeTime->setFromSeconds(ADMIN_SESSION_MAX_TIME);
        }
        else
        {
            $lifeTime->setFromSeconds(USER_SESSION_MAX_TIME);
        }
        $date->addSpan($lifeTime);
        $_SESSION['SESSION_END_LIFE']=$date;
    }

   /**
     * setQualifChecked
     *
     * Set ON qualifChecked in session to remember not to check again... qualif !
     *
     * @access private
     * @param null
     * @return null
     */
    function setQualifChecked()
    {
		$_SESSION['qualifChecked']=1;
    }

   /**
     * unsetQualifChecked
     *
     * @access private
     * @param null
     * @return null
     */
    function unsetQualifChecked()
    {
		unset($_SESSION['qualifChecked']);
    }

   /**
     * isQualifChecked
     *
     * @access private
     * @param null
     * @return boolean
     */
    function isQualifChecked()
    {
		return (isset($_SESSION['qualifChecked']));
    }

   /**
     * try to open and use a lang file in lang/ directory
     *
     * @access private
     * @param $lang string lang name
     * @return null
     */
    function openLangFile($langName)
    {
        global $lang;

        if (file_exists('./lang/'.$langName.'.php'))
        {
            require_once('./lang/'.$langName.'.php');
        }
    }

	/*
	* Save a variable in the current session
	*
	* Save a variable global named $name in the session registry.
	*
	* @access public
	* @param string $name input variable name
	*/
	function add($name)
	{
		global $$name;
		$_SESSION[$name]=$$name;
	}
	
	/*
	* Retrieve a variable in the current session and define it
	*
	* Retrieve a variable named $name in the session registry (saved by add()) and define it global.
	*
	* @access public
	* @param string $name input/output variable name
	*/
	function define($name)
	{
		global $$name;
		if(isset($_SESSION[$name]))
		{
			$$name=$_SESSION[$name];
		}
		else
		{
			$$name=null;
		}
	}
	
	function getFromSession($name)
	{
		return($_SESSION[$name]);
	}
	
	function kill($name)
	{
		unset($_SESSION[$name]);
	}
	
   /**
     * isBannedConnexion
     * check if connexion is agreed or not
     *
     * @access private
     * @param $currentDate ofDate
     * @param $expireDate ofDate
     * @return false or banned connexion time
     */
    function isBannedConnexion($currentDate, $expireDate)
    {
        if ($currentDate->before($expireDate))
        {
            return (new ofDateSpan($expireDate,$currentDate));
        }
        return (false);
    }

   /**
     * authenticate user
     *
     * @access public
     * @param $login string
     * @param $password string
     * @param $menu integer used to choose session max time type
     * @return array($first,$second,etc.) $first=type of error, $second,etc.=parameter.
     * $first values :
     * 0 : OK
     * 1 : OK but choose profile
     * 2 : outdate but authorized (and choose profile)
     * 3 : outdate but authorized with outdate profile
     * 4 : outdate subscription, unauthorized
     * 5 : bad Ident, unauthorized
     * 6 : Banned (ip or login), unauthorized
     * 7 : no Ident -> ask one
     */
    function kernelAuth ($login,$password,$menu=0)
    {
        $date=new ofDate();     // we take current date to test with login stopped value and session lifetime

        $ipAddr=$_SERVER['REMOTE_ADDR'];    // we take ip address to test if it's not a banned one
        $badIpCounter=0;
        $row=$this->db->queryAndFetch('select * from ip_stopped where ip_stopped.IP_NUM=\''.$ipAddr.'\'');
        if ($row)
        {
            $badIpCounter=$row->COUNTER;
            $expireDate=new ofDate($row->EXPIRE_DATE);
            $delay=$this->isBannedConnexion($date,$expireDate);
            if ($delay)
            {
                return array(6,$delay);
            }
        }

        if ($login!='')
        {
            $badLoginCounter=0;
            $row=$this->db->queryAndFetch('select * from login_stopped where login_stopped.LOGIN=\''.$login.'\'');
            if ($row)
            {
                $badLoginCounter=$row->COUNTER;
                $expireDate=new ofDate($row->EXPIRE_DATE);
                $delay=$this->isBannedConnexion($date,$expireDate);
                if($delay)
                {
                    return array(6,$delay);
                }
            }

            $row=$this->db->queryAndFetch('select clubs.*, 
            authentication.LANG, authentication.AIRCRAFTS_VIEWED, authentication.INST_VIEWED, 
            authentication.PROFILE, authentication.NUM as AUTH_NUM, 
            members.SUBSCRIPTION 
            from clubs
            left join authentication on authentication.NAME=\''.$login.'\' and authentication.PASSWORD=\''.passwordCrypt($password).'\'
            left join members on members.NUM=authentication.NUM');
            if ((!$row) or (!isset($row->AUTH_NUM)))
            {
                if (OF_DEBUG!='on')
                {
                    $badIpCounter++;
                    $badLoginCounter++;
                }
                $dateBis=$date;

                if ($badIpCounter>3)
                {
                    $dateBis->addSeconds(100);
                }

                if ($badIpCounter>1)
                {
                    $this->db->query('update ip_stopped set COUNTER=\''.$badIpCounter.'\', EXPIRE_DATE=\''.$dateBis->getDate().'\' where IP_NUM=\''.$ipAddr.'\'');
                }
                elseif ($badIpCounter>0)
                {
                    $this->db->query('insert into ip_stopped (IP_NUM,COUNTER,EXPIRE_DATE) values (\''.$ipAddr.'\',\''.$badIpCounter.'\',\''.$dateBis->getDate().'\')');
                }

                if ($badLoginCounter>3)
                {
                    $delay=new ofDateSpan();
                    $delay->setFromMinutes(($badLoginCounter-3)*($badLoginCounter-3));
                    $date->addSpan($delay);
                }

                if ($badLoginCounter>1)
                {
                    $this->db->query('update login_stopped set COUNTER=\''.$badLoginCounter.'\', EXPIRE_DATE=\''.$date->getDate().'\' where LOGIN=\''.$login.'\'');
                }
                elseif ($badLoginCounter>0)
                {
                    $this->db->query('insert into login_stopped (LOGIN,COUNTER,EXPIRE_DATE) values (\''.$login.'\',\''.$badLoginCounter.'\',\''.$date->getDate().'\')');
                }

                return array(5);    // bad Ident
            }

            // Connected is well identified
            // we save his AUTH_NUM in session for further connexions
            $_SESSION['AUTH_NUM']=$row->AUTH_NUM;
			// we store an id for the club in the session to avoid vhosts cross authentification sharing the same php sessions
			$this->setUserSessionId();
            // and we regenerate a new session id to block session fixation
            if (function_exists('session_regenerate_id'))
            {
                session_regenerate_id();
            }

            // if we are here, is that authentication is correct. So we can clear counter on previous bad password
            $this->db->query('delete from login_stopped where login_stopped.LOGIN=\''.$login.'\'');
            $this->db->query('delete from ip_stopped where ip_stopped.IP_NUM=\''.$ipAddr.'\'');

            // Now we can use the lang choosen by the authenticated connected
            $this->openLangFile($row->LANG);

            $this->setFirstLegendPopup(true);

            // we take back admin num to short-cut below the subscriptions tests
            $adminNum=$row->ADMIN_NUM;
            if (isset($row->SUBSCRIPTION))
            {
                $subscription=new ofDate($row->SUBSCRIPTION);
                $subscription->setHour(23);
                $subscription->setMinute(59);
                $subscription->setSecond(59);
            }
            else
            {
                $subscription=new ofDate('2037-12-31T23:59:59');
            }
            $j=0;
            for ($i=0;$i<15;$i++)
            {
                if (($row->PROFILE>>$i)&1)
                {
                    $profileTable[$j]=$i;
                    $j=$j+1;
                }
            }
            $profiles=array();
            for ($i=0;$i<$j;$i++)
            {
                $row=$this->db->queryAndFetch('select * from profiles where profiles.NUM='.(1<<$profileTable[$i]));
                if ($row)
                {
                    $profiles[]=array($profileTable[$i],$row->NAME);
                    $profilePermits[$i]=$row->PERMITS;
                }
            }
            $_SESSION['RIGHTS']=$profilePermits[0];
            $_SESSION['PROFILE_NAME']=$profiles[0][1];
            // We update session time life
            $this->setSessionEndLife($date,$menu);

            // We have to check if subscription is valid (case of a member elsewhise $subscription='9999-12-31')
            // if subscription is not valid, either no connection (outdate_subscription_profile=0) or outdate_subscription_profile profile
            $subscriptionLevel=$this->parameter->isUseSubscription();
            if (($subscriptionLevel==2)and($subscription->isPast()))
            {
                if ($this->getAuthNum()==$adminNum)
                {
                    if($j!=1)
                    {
                        return array(2,$profiles);     // outdate but authorized because it's the admin chief
                    }
                    else
                    {
                        return array(2);
                    }
                }
                $outdateProfile = $this->parameter->getOutdateSubscriptionProfile();
                if ($outdateProfile)
                {
                    $row=$this->db->queryAndFetch('select * from profiles where profiles.NUM='.$outdateProfile);
                    if ($row)
                    {
                        $_SESSION['RIGHTS']=$row->PERMITS;
                        $_SESSION['PROFILE_NAME']=$row->NAME;
                        // We update session time life
                        $this->setSessionEndLife($date,$menu);
                        return array(3,$row->NAME);     // outdate but authorized with default profile
                    }
                }
                $_SESSION['AUTH_NUM']=0;
                return array(4);    // outdate and unauthorized
            }
            elseif (($subscriptionLevel==1)and($subscription->isPast()))
            {
                return array(2);    // oudate but authorized
            }

            if($j!=1)
            {
                return array(1,$profiles);
            }
            else
            {
                return array(0);
            }
		}
        elseif ( isset($_SESSION['AUTH_NUM'])and($_SESSION['AUTH_NUM']!=0) and $this->isValidUserSessionId()) {
            $this->openLangFile($this->getLang());
            if ($this->isNothingAllowed())
            {
                return array(0);
            }

            $endLife=$this->getSessionEndLife();
            if ($endLife)
            {
                if ($date->before($endLife)or(OF_DEBUG=='on'))
                {
                    $this->setSessionEndLife($date,$menu);
                    return array(0);
                }
            }
            $this->quit();
            return array(7);
        }
        else
        {
            $this->quit();
            return array(7);
        }
    }
   
////////////////////////////////// getters

// Navigation record values

	function getOldMenu()
	{
		return($_SESSION['OLD_MENU']);
	}

	function getOldSubMenu()
	{
		return($_SESSION['OLD_SUB_MENU']);
	}

	function isFirstLegendPopup()
	{
		return($_SESSION['OLD_LEGEND']);
	}

    function getMembers()
    {
        $list=array();
        // Database call to know all members
        $this->db->query('select authentication.FIRST_NAME, authentication.LAST_NAME, authentication.NUM 
        from authentication
        left join members on authentication.NUM=members.NUM order by authentication.LAST_NAME, authentication.FIRST_NAME');
        while($row=$this->db->fetch())
        {
            $list[]=array($row->NUM,$row->LAST_NAME.' '.$row->FIRST_NAME);
        }
	    $this->db->free();
	    return $list;
    }
	
	function getAircraftsClass()
	{
        // Database call to know all aircrafts
        $result=$this->db->query('select * from aircrafts order by ORDER_NUM');
	    $list=new aircraft_serie($result);
	    $this->db->free();
	    $list->set_viewed($this->getViewedAircrafts());
	    return ($list);
	}
	
	function getInstructorsClass()
	{
	    // Database call to know all instructors
	    $result=$this->db->query('select instructors.SIGN, authentication.FIRST_NAME, authentication.LAST_NAME, authentication.NUM from instructors
	    left join authentication on authentication.NUM=instructors.INST_NUM order by instructors.ORDER_NUM');
	    $list=new instructor_serie($result);
	    $this->db->free();
	    $list->set_viewed($this->getViewedInstructors());
	    return ($list);
	}
	
//////////////////////// gets attached to the club table

	/**
	* Universal query for the club table
    * @access private
    * @param $field string fied of the club table we're looking after
	* @return string content of the field for current user
	*/
	function queryFromClub($field)
	{
		return $this->db->query_and_fetch_single('select '.$field.' from clubs where clubs.NUM=1');
	}

	/**
	* Get club name from database
    * @access public
    * @param null
	* @return string
	*/
	function getAdminNum() {
		return $this->queryFromClub('ADMIN_NUM');
	}

	/**
	* Get club name from database
    * @access public
    * @param null
	* @return string
	*/
	function getClubName()
	{
		return $this->queryFromClub('NAME');
	}

	/**
	* Get club mail "from" address from database that should be used in the FROM field for automatic mails
    * @access public
    * @param null
	* @return string
	*/
	function getClubMailFromAddress()
	{
        $emailClub = $this->queryFromClub('MAIL_FROM_ADDRESS');
        if (Mail_RFC822::isValidInetAddress($emailClub)) {
            return $emailClub;
        }
        else {
            return DEFAULT_CLUB_EMAIL_ADDRESS;
        }
	}

	/**
	* Get text info from database that should be displayed in the info box on the registry book
    * @access public
    * @param null
	* @return string
	*/
	function getInfoCell()
	{
		return $this->queryFromClub('INFO_CELL');
	}

	/**
	* Get icao code from database
    * @access public
    * @param null
	* @return string
	*/
	function getIcao()
	{
		return $this->queryFromClub('ICAO');
	}

	/**
	* Get interval time viewed
    * @access public
    * @param null
	* @return interval
	*/
	function getIntervalDisplayed()
	{
		$begin=new ofDateSpan($this->queryFromClub('FIRST_HOUR_DISPLAYED'));
		$end=new ofDateSpan($this->queryFromClub('LAST_HOUR_DISPLAYED'));
        return (new interval($begin,$end));
	}

	/**
	* Get the default slot range
    * @access public
    * @param null
	* @return ofDateSpan
	*/
    function getDefaultSlotRange()
    {
        $time=new ofDateSpan();
        $time->setFromMinutes($this->queryFromClub('DEFAULT_SLOT_RANGE'));
        return $time;
    }

	function getMinSlotRange()
	{
		return $this->queryFromClub('MIN_SLOT_RANGE');
	}

	/**
	* Get the standard twilight range
    * @access public
    * @param null
	* @return ofDateSpan
	*/
	function getTwilightRange()
	{
        $time=new ofDateSpan();
        $time->setFromMinutes($this->queryFromClub('TWILIGHT_RANGE'));
        return $time;
	}

	function getMailingListName()
	{
		return $this->queryFromClub('MAILING_LIST_NAME');
	}

	function getMailingListType()
	{
		return $this->queryFromClub('MAILING_LIST_TYPE');
	}

	function getClubUrl()
	{
		return $this->queryFromClub('CLUB_SITE_URL');
	}

	function isSameDayBox()
	{
		return isSameDayBox($this->queryFromClub('FLAGS'));
	}

	function isBookComment()
	{
		return isBookComment($this->queryFromClub('FLAGS'));
	}

	// gets attached to the authentication table
	
	/**
	* Return field content of the authentication table
    * @access private
    * @param $field field name
	* @return string field value
	*/
	function queryFromAuthentication($field)
	{
		return $this->db->query_and_fetch_single('select '.$field.' from authentication where authentication.NUM='.$this->getAuthNum());
	}

/*  should be change according the new use of VIEW_TYPE bit 1

	function isVerticalView()
	{
		return($this->queryFromAuthentication('VIEW_TYPE')&1);
	}
*/

	function isLegendPopup()
	{
		return(($this->queryFromAuthentication('VIEW_TYPE')&4)>>2);
	}

	function isFrenchDateDisplay()
	{
		return(($this->queryFromAuthentication('VIEW_TYPE')&8)>>3);
	}

	function isInstOnOneDay()
	{
	    if (($this->queryFromAuthentication('VIEW_TYPE')&16)>>4)
	    {
	        return (false);
	    }
	    else 
	    {
	        return (true);
	    }
	}

	function isAircraftOnOneDay()
	{
	    if (($this->queryFromAuthentication('VIEW_TYPE')&32)>>5)
	    {
	        return (false);
	    }
	    else 
	    {
	        return (true);
	    }
	}

	/**
	* Return flag saying if current user home phone can be shown to everybody
    * @access public
	* @return boolean
	*/
	function isMailNotify()
	{
		return isMailNotify($this->queryFromAuthentication('NOTIFICATION'));
	}

	/**
	* Return flag saying if current user home phone can be shown to everybody
    * @access public
	* @return boolean
	*/
	function isPublicHomePhone()
	{
		return isPublicHomePhone($this->queryFromAuthentication('VIEW_TYPE'));
	}


	/**
	* Return flag saying if current user work phone can be shown to everybody
    * @access public
	* @return boolean
	*/
	function isPublicWorkPhone()
	{
		return isPublicWorkPhone($this->queryFromAuthentication('VIEW_TYPE'));
	}

	/**
	* Return flag saying if current user cell phone can be shown to everybody
    * @access public
	* @return boolean
	*/
	function isPublicCellPhone()
	{
		return isPublicCellPhone($this->queryFromAuthentication('VIEW_TYPE'));
	}

	/**
	* Return flag saying if current user email can be shown to everybody
    * @access public
	* @return boolean
	*/
	function isPublicEmail()
	{
		return isPublicEmail($this->queryFromAuthentication('VIEW_TYPE'));
	}

	/**
	* Return aircrafts_viewed value of the current user
    * @access public
	* @return string aircrafts_viewed list (in fact non viewed list)
	*/
	function getViewedAircrafts()
	{
		return $this->queryFromAuthentication('AIRCRAFTS_VIEWED');
	}

	/**
	* Return inst_viewed value of the current user
    * @access public
	* @return string inst_viewed list (in fact non viewed list)
	*/
	function getViewedInstructors()
	{
		return $this->queryFromAuthentication('INST_VIEWED');
	}

	/**
	* Return cell_phone value of the current user
    * @access public
	* @return string cell phone
	*/
	function getCellPhone()
	{
		return $this->queryFromAuthentication('CELL_PHONE');
	}

	/**
	* Return work_phone value of the current user
    * @access public
	* @return string work phone
	*/
	function getWorkPhone()
	{
		return $this->queryFromAuthentication('WORK_PHONE');
	}

	/**
	* Return home_phone value of the current user
    * @access public
	* @return string home phone
	*/
	function getHomePhone()
	{
		return $this->queryFromAuthentication('HOME_PHONE');
	}

	/**
	* Return View height value of the current user
    * @access public
	* @return string view height
	*/
	function getViewHeight()
	{
		return $this->queryFromAuthentication('VIEW_HEIGHT');
	}

	/**
	* Return View width value of the current user
    * @access public
	* @return string view width
	*/
	function getViewWidth()
	{
		return $this->queryFromAuthentication('VIEW_WIDTH');
	}

	/**
	* Return time zone choosed by the user
    * @access public
    * @param
	* @return Date_TimeZone object
	*/
	function getTimeZone()
	{
        $tz=new Date_TimeZone($this->queryFromAuthentication('TIMEZONE'));
        return $tz;
	}

	/**
	* Return lang value of the current user
    * @access public
	* @return string home phone
	*/
	function getLang()
	{
		return $this->queryFromAuthentication('LANG');
	}

	/**
	* Return notification value of the current user
    * @access public
	* @return integer notification
	*/
	function getNotification()
	{
		return $this->queryFromAuthentication('NOTIFICATION');
	}

	function getAuthNum()
	{
		return $_SESSION['AUTH_NUM'];
	}

	/**
	* Return login of the user
    * @access public
	* @return string login
	*/
	function getLogin()
	{
		return $this->queryFromAuthentication('NAME');
	}

	/**
	* Return firstname value of the current user
    * @access public
	* @return string firstname
	*/
	function getFirstName()
	{
		return $this->queryFromAuthentication('FIRST_NAME');
	}

	/**
	* Return lastname value of the current user
    * @access public
	* @return string lastname
	*/
	function getLastName()
	{
		return $this->queryFromAuthentication('LAST_NAME');
	}

	/**
	* Return email of the connected user
    * @access public
    * @param 
	* @return string email address
	*/
	function getEmail()
	{
		return $this->queryFromAuthentication('EMAIL');
	}

	/**
	* Return address (street) of the connected user
    * @access public
    * @param 
	* @return string address
	*/
	function getAddress()
	{
		return $this->queryFromAuthentication('ADDRESS');
	}

	/**
	* Return zipcode of the connected user
    * @access public
    * @param 
	* @return string zipcode
	*/
	function getZipcode()
	{
		return $this->queryFromAuthentication('ZIPCODE');
	}

	/**
	* Return city of the connected user
    * @access public
    * @param 
	* @return string city
	*/
	function getCity()
	{
		return $this->queryFromAuthentication('CITY');
	}

	/**
	* Return state or province of the connected user
    * @access public
    * @param 
	* @return string state or province
	*/
	function getState()
	{
		return $this->queryFromAuthentication('STATE');
	}

	/**
	* Return country of the connected user
    * @access public
    * @param 
	* @return string country
	*/
	function getCountry()
	{
		return $this->queryFromAuthentication('COUNTRY');
	}

// Type (according the authentication)
	
	function isInstructor()
	{
		return isInstructor($_SESSION['AUTH_NUM']);
	}

	function isMember()
	{
		return isMember($_SESSION['AUTH_NUM']);
	}

// gets attached to the member table (with escape value if not a member)
	
// universal query for the members table

	function queryFromMember($field)
	{
		return $this->db->query_and_fetch_single('select '.$field.' from members where members.NUM='.$this->getAuthNum());
	}

	function getSubscription()
	{
		$value=$this->queryFromMember('SUBSCRIPTION');
		if(isset($value))
		{
			$subscription=new ofDate($value);
			$subscription->setHour(23);
			$subscription->setMinute(59);
			$subscription->setSecond(59);
		}
		else
		{
			$subscription=new ofDate('9999-12-31T23:59:59');
		}
		return $subscription;
	}

	/**
	* Return QUALIF_ALERT_DELAY value of the current user
    * @access public
	* @return integer QUALIF_ALERT_DELAY
	*/
	function getQualifAlertDelay()
	{
		return $this->queryFromMember('QUALIF_ALERT_DELAY');
	}


// gets attached to the profile table

	function get_profile_name()
	{
		return $_SESSION['PROFILE_NAME'];
	}
	
	function getPermits()
	{
		return $_SESSION['RIGHTS'];
	}

// Permits (according the profile)

	function get_rank()
	{
		$returned_value = 3;
		if ($this->is_set_pilots_file_allowed())
		{
			$returned_value = 2;
		}
		if ($this->is_set_club_parameters_allowed())
		{
			$returned_value = 1;
		}
		return $returned_value;
	}

	function isNothingAllowed()
	{
	    if (isset($_SESSION['RIGHTS']))
	    {
            return isNothingAllowed($_SESSION['RIGHTS']);
	    }
	    else 
	    {
	        return true;
	    }
	}

	function isNoAutoLogout()
	{
		return isNoAutoLogout($_SESSION['RIGHTS']);
	}

	function isAnybodyBookAllowed()
	{
		return isAnybodyBookAllowed($_SESSION['RIGHTS']);
	}

	function isAloneBookAllowed()
	{
		return isAloneBookAllowed($_SESSION['RIGHTS']);
	}

	function isInstructorBookAllowed()
	{
		return isInstructorBookAllowed($_SESSION['RIGHTS']);
	}

	function isFreezeAircraftAllowed()
	{
		return isFreezeAircraftAllowed($_SESSION['RIGHTS']);
	}

	function isFreezeInstructorAllowed()
	{
		return isFreezeInstructorAllowed($_SESSION['RIGHTS']);
	}

	function is_set_pilots_file_allowed()
	{
		return isSetPilotsAllowed($_SESSION['RIGHTS']);
	}
	
	function isSetOwnQualifAllowed()
	{
		return isSetOwnQualifAllowed($_SESSION['RIGHTS']);
	}
	
	function is_set_aircrafts_file_allowed()
	{
		return isSetAircraftsAllowed($_SESSION['RIGHTS']);
	}

	function is_set_club_parameters_allowed()
	{
		return isSetClubAllowed($_SESSION['RIGHTS']);
	}

	function isSetOwnLimitationsAllowed()
	{
		return isSetOwnLimitsAllowed($_SESSION['RIGHTS']);
	}
	

//////////////////////////// setters

// Navigation record values

// sets attached to the authentication table

	/**
	* update $field field from authentication table with $value
    * @access private
    * @param $field string field name of authentication table
    * @param $value string for the field name
	* @return boolean
	*/
	function updateAuthentication($field,$value)
	{
        return $this->db->query('update authentication set '.$field.'=\''.$value.'\'
        where NUM=\''.$this->getAuthNum().'\'');
	}

	/**
	* update work_phone field from authentication table with $value
    * @access public
    * @param $value string for work_phone
	* @return boolean
	*/
    function setWorkPhone($value)
    {
        return $this->updateAuthentication('WORK_PHONE',$value);
    }

	/**
	* update cell_phone field from authentication table with $value
    * @access public
    * @param $value string for cell_phone
	* @return boolean
	*/
    function setCellPhone($value)
    {
        return $this->updateAuthentication('CELL_PHONE',$value);
    }

	/**
	* update home_phone field from authentication table with $value
    * @access public
    * @param $value string for home_phone
	* @return boolean
	*/
    function setHomePhone($value)
    {
        return $this->updateAuthentication('HOME_PHONE',$value);
    }

	/**
	* update address field from authentication table with $value
    * @access public
    * @param $value string for address
	* @return boolean
	*/
    function setAddress($value)
    {
        return $this->updateAuthentication('ADDRESS',$value);
    }

	/**
	* update zipcode field from authentication table with $value
    * @access public
    * @param $value string for zipcode
	* @return boolean
	*/
    function setZipcode($value)
    {
        return $this->updateAuthentication('ZIPCODE',$value);
    }

	/**
	* update city field from authentication table with $value
    * @access public
    * @param $value string for city
	* @return boolean
	*/
    function setCity($value)
    {
        return $this->updateAuthentication('CITY',$value);
    }

	/**
	* update state field from authentication table with $value
    * @access public
    * @param $value string for state
	* @return boolean
	*/
    function setState($value)
    {
        return $this->updateAuthentication('STATE',$value);
    }

	/**
	* update country field from authentication table with $value
    * @access public
    * @param $value string for country
	* @return boolean
	*/
    function setCountry($value)
    {
        return $this->updateAuthentication('COUNTRY',$value);
    }

    function setOldMenus($value,$value2)
	{
		$_SESSION['OLD_MENU']=$value;
		$_SESSION['OLD_SUB_MENU']=$value2;
	}

	function setFirstLegendPopup($flag)
	{
		$_SESSION['OLD_LEGEND']=$flag;
	}
	
	/**
	* update timezone field from authentication table with $value
    * @access public
    * @param $value string of timezone
	* @return boolean
	*/
    function setTimezone($value)
    {
        return $this->updateAuthentication('TIMEZONE',$value);
    }
	
	/**
	* update lang field from authentication table with $value
    * @access public
    * @param $value string of lang
	* @return boolean
	*/
    function setLang($value)
    {
        return $this->updateAuthentication('LANG',$value);
    }
	
	/**
	* update view_height field from authentication table with $value
    * @access public
    * @param $value int view height
	* @return boolean
	*/
    function setViewHeight($value)
    {
        return $this->updateAuthentication('VIEW_HEIGHT',$value);
    }
	
	/**
	* update view_width field from authentication table with $value
    * @access public
    * @param $value int view width
	* @return boolean
	*/
    function setViewWidth($value)
    {
        return $this->updateAuthentication('VIEW_WIDTH',$value);
    }
	
	/**
	* update email field from authentication table with $value
    * @access public
    * @param $value string of email
	* @return boolean
	*/
	function setEmail($value)
	{
        return $this->updateAuthentication('EMAIL',$value);
	}

	/**
	* generic NOTIFICATION update
    * @access private
    * @param $bit int bit number to change according $flag
    * @param $flag boolean to set or not bit
	* @return null
	*/
	function setGenericNotification($bit,$flag)
	{
	    $value=$this->queryFromAuthentication('NOTIFICATION');
		if($flag)
		{
			$value=($value|$bit);
		}
		else
		{
			$value=($value&(255-$bit));
		}
		$this->updateAuthentication('NOTIFICATION',$value);
	}

	/**
	* generic VIEW_TYPE update
    * @access private
    * @param $bit int bit number to change according $flag
    * @param $flag boolean to set or not bit
	* @return null
	*/
	function setGenericViewType($bit,$flag)
	{
	    $value=$this->queryFromAuthentication('VIEW_TYPE');
		if($flag)
		{
			$value=($value|$bit);
		}
		else
		{
			$value=($value&(65535-$bit));
		}
		$this->updateAuthentication('VIEW_TYPE',$value);
	}
	
	/**
	* update mail notification bit from NOTIFICATION from authentication table
    * @access public
    * @param $flag to set or not bit
	* @return null
	*/
	function setMailNotification($flag)
	{
	    $this->setGenericNotification(1,$flag);
	}

	/**
	* update public home phone bit from VIEW_TYPE from authentication table
    * @access public
    * @param $flag to set or not bit
	* @return null
	*/
	function setPublicHomePhone($flag)
	{
	    $this->setGenericViewType(64,$flag);
	}

	/**
	* update public work phone bit from VIEW_TYPE from authentication table
    * @access public
    * @param $flag to set or not bit
	* @return null
	*/
	function setPublicWorkPhone($flag)
	{
	    $this->setGenericViewType(128,$flag);
	}

	/**
	* update public cell phone bit from VIEW_TYPE from authentication table
    * @access public
    * @param $flag to set or not bit
	* @return null
	*/
	function setPublicCellPhone($flag)
	{
	    $this->setGenericViewType(256,$flag);
	}

	/**
	* update public email bit from VIEW_TYPE from authentication table
    * @access public
    * @param $flag to set or not bit
	* @return null
	*/
	function setPublicEmail($flag)
	{
	    $this->setGenericViewType(512,$flag);
	}

	/**
	* update aircrafts viewed : AIRCRAFTS_VIEWED from authentication table
    * @access public
    * @param $list string defining which aircrafts are not viewed (serialization of non-viewed aircrafts. If empty, all aircrafts are viewed. NUM are separated by *. There must be a * as first char.)
	* @return null
	*/
	function setAircraftsViewed($list)
	{
		$this->updateAuthentication('AIRCRAFTS_VIEWED',$list);
	}
	
	/**
	* update instructors viewed : INST_VIEWED from authentication table
    * @access public
    * @param $list string defining which instructors are not viewed (serialization of non-viewed instructors. If empty, all instructors are viewed. NUM are separated by *. There must be a * as first char.)
	* @return null
	*/
	function setInstructorsViewed($list)
	{
		$this->updateAuthentication('INST_VIEWED',$list);
	}

	function set_combo_menu($flag)
	{
	    $this->setGenericViewType(2,$flag);
	}

	function set_vertical_view($flag)
	{
	    $this->setGenericViewType(1,$flag);
	}

	function setLegendPopup($flag)
	{
	    $this->setGenericViewType(4,$flag);
	}

	function set_french_date_display($flag)
	{
	    $this->setGenericViewType(8,$flag);
	}

	function set_inst_on_one_day($flag)
	{
	    $this->setGenericViewType(16,!$flag);
	}

	function set_aircraft_on_one_day($flag)
	{
	    $this->setGenericViewType(32,!$flag);
	}

	/**
	* update $field field from members table with $value
    * @access private
    * @param $field string field name of members table
    * @param $value string for the field name
	* @return boolean
	*/
	function updateMembers($field,$value)
	{
        return $this->db->query('update members set '.$field.'=\''.$value.'\'
        where NUM=\''.$this->getAuthNum().'\'');
	}

	/**
	* update QUALIF_ALERT_DELAY from members table
    * @access public
    * @param $value integer number of weeks for alert delay
	* @return null
	*/
	function setQualifAlertDelay($value)
	{
        return $this->updateMembers('QUALIF_ALERT_DELAY',$value);
	}

	/**
	 * get usable profiles for 1 user
     * @access private
     * @param $_authNum
     * @return array listing usable profile [bit num](name, permits)
     */
    function getUsableProfiles($_authNum) {
        $profileTable = array();

        $row = $this->db->queryAndFetch('SELECT PROFILE, SUBSCRIPTION FROM authentication LEFT JOIN members ON members.NUM=authentication.NUM WHERE authentication.NUM=\''.$_authNum.'\'');
        if (isset($row->SUBSCRIPTION)) {
            $_subscription = new ofDate($row->SUBSCRIPTION);
            $_subscription->setHour(23);
            $_subscription->setMinute(59);
            $_subscription->setSecond(59);
        }
        else {
            $_subscription = new ofDate('2037-12-31T23:59:59');
        }
        $profileByte = $row->PROFILE;

        $j = 0;
        for ($i = 0; $i < 15; $i++) {
            if (($profileByte >> $i) & 1) {
                $profileTable[$j] = $i;
                $j = $j + 1;
            }
        }

        $profiles = array();
        for ($i = 0; $i < $j; $i++) {
            $row = $this->db->queryAndFetch('select * from profiles where profiles.NUM='.(1<<$profileTable[$i]));
            if ($row) {
                $profiles[$profileTable[$i]] = array($row->NAME, $row->PERMITS);
            }
        }

        $_subscriptionLevel = $this->parameter->isUseSubscription();
        if (($_subscriptionLevel == 2) and ($_subscription->isPast())) {
            if ($this->getAdminNum() == $_authNum) {
                return $profiles;     // outdate but authorized because it's the admin chief
            }
            $_outdateProfile = $this->parameter->getOutdateSubscriptionProfile();
            if ($_outdateProfile) {
                $row = $this->db->queryAndFetch('select * from profiles where profiles.NUM='.$outdateProfile);
                if ($row) {
                    return array(floor(log($row->NUM,2)) => array($row->NAME, $row->PERMITS));     // outdate but authorized with default profile
                }
            }
            return array();
        }
        return $profiles;
    }

    function setProfile($profile)
    {
        $_profiles = $this->getUsableProfiles($this->getAuthNum());
        if (array_key_exists($profile, $_profiles)) {
            $_profileDesc = $_profiles[$profile];
            $_SESSION['PROFILE_NAME'] = $_profileDesc[0];
            $_SESSION['RIGHTS'] = $_profileDesc[1];
        }
    }
}
?>