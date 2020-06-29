<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * index.php
 *
 * Common entry point to all OpenFlyers commands and display
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
 * @category   authentication and command management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: index.php,v 1.112.2.12 2007/04/29 19:01:35 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */
$counterStart = microtime( 1 );

// set the zend engine 1 compatibility On for zend engine 2 (ie php 5)
ini_set('zend.ze1_compatibility_mode',1);

// security constant used by others php files to test if they are called within index.php or not
define('SECURITY_CONST',1);

require_once('./conf/config.php');

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.PEAR_DIRECTORY);
}

require_once('./lang/'.DEFAULT_LANG.'.php');		// default language file necessary for correct initialisation of class definitions

// Connexion to database with initialisation of $database class
require_once('./conf/connect.php');		// DataBase parameters
require_once('./classes/db.class.php');	// MySQL database connector

// Warning : $database is used as GLOBAL variable everywhere (ie : in functions)
// update : some times $database is passed has argument to object. It should be the right way.
$database=new DBAccessor(HOST,BASE,VISITOR,PASSWORD_VISITOR);

// Serie manager class
require_once('./classes/serie.class.php');

// OF Date class based on PEAR one
require_once('./classes/Date.class.php');
Date_TimeZone::setDefault('UTC');

// pool functions
require_once('./pool/functions.php');

// authentification and session opening with initialisation of $userSession class
require_once('./classes/userSession.class.php');
require_once('./displayClasses/authForms.class.php');

PEAR::setErrorHandling(PEAR_ERROR_PRINT);   // useful to debug : display the error message

define_global('menu',0);
define_global('ope');

// $ope variable is used by admin menus. So, if is defined, we set menu=7 which means "admin part"
if ($ope!='')
{
    $menu=7;
}
$userSession=new authForms($database);		// Warning $userSession is used as GLOBAL everywhere

// $login variable is posted by connexion form otherwise is set ''
define_global('login');
define_global('password');
if($menu==6)
{
	$userSession->quit();
	$login='';
	$password='';
	$menu=0;
	$submenu=0;
}

if($userSession->authenticate($login,$password,$menu))
{
    define_global('profile');
    if ($profile!='')
    {
        $userSession->setProfile($profile);
    }

    if ($userSession->checkQualif())
    {
        $aircraftsClass=$userSession->getAircraftsClass();
        $instructorsClass=$userSession->getInstructorsClass();

        if($menu)
        {
            if(($menu==2)and($userSession->isInstructor()))
            {
                define_global('sub_menu',$instructorsClass->get_viewed_array_value($userSession->getAuthNum()),'');
            }
            else
            {
                define_global('sub_menu',0,'');
            }
        }
        else
        {
            $default_sub_menu=0;
            if($userSession->isInstOnOneDay())
            {
                $default_sub_menu=$default_sub_menu|2;
            }
            if($userSession->isAircraftOnOneDay())
            {
                $default_sub_menu=$default_sub_menu|1;
            }
            if($default_sub_menu==3)
            {
                $default_sub_menu=0;
            }
            define_global('sub_menu',$default_sub_menu,'');
        }
	
        // we look if it has been send by javascript a tsStartDate or ts_old_start_date value
        define_global('ts_old_start_date','');
        define_global('tsStartDate',$ts_old_start_date);
        /* $firstDisplayedDate represents the first date which is displayed on the book,
        *  by default : today (if ts_old_start_date and tsStartDate are null).
        */
        $firstDisplayedDate=new ofDate($tsStartDate);
        if(($menu!=3)and($menu!=4))
        {
            $firstDisplayedDate->clearClock();
        }
        $firstDisplayedDate->setSecond(0);
        // offset_hour is set by navigation.php to define the beginning of a slot.
        define_global('offset_hour',0);
        // offset_day is set by navigation.php to define the beginning of a slot from a
        define_global('offset_day',0);
        $time=new ofDateSpan($offset_day.' '.floor($offset_hour/4).' '.(($offset_hour%4)*15).':00');
        $firstDisplayedDate->addSpan($time);

        if ($ope!='')
        {
            include ('admin/index.php');
            $menu=-1;
            $sub_menu=-1;
        }

        $menuNames=array($lang['MENU_DAY_BOOK'],$lang['MENU_AIRCRAFT_BOOK'],$lang['MENU_INSTRUCTOR_BOOK'],$lang['MENU_BOOKING'],$lang['MENU_OWN_DATA'],$lang['MENU_MEMBERS_LIST'],$lang['MENU_DISCONNECT']);
        $menuOpe=array(0,1,2,3,4,5,6);
        if($userSession->is_set_club_parameters_allowed()OR$userSession->is_set_pilots_file_allowed()OR$userSession->is_set_aircrafts_file_allowed())
        {
            $menuNames[]=$lang['MENU_ADMIN'];
            $menuOpe[]=7;
        }
        if($userSession->parameter->getFlightProcess()!=-1)
        {
            $menuNames[]=$lang['MENU_FLIGHT'];
            $menuOpe[]=8;
        }
        $menuSize=sizeof($menuNames);
        for ($i=0;$i<$menuSize;$i++)
        {
            $subMenuNames[$i]=array();
            switch ($menuOpe[$i])
            {
            case 0:
                $subMenuNames[$i][0]=new popup_menu($lang['SUBMENU_AIRCRAFTS_INSTRUCTORS'],'');
                $subMenuNames[$i][1]=new popup_menu($lang['SUBMENU_AIRCRAFTS'],'');
                $subMenuNames[$i][2]=new popup_menu($lang['SUBMENU_INSTRUCTORS'],'');
                break;
            case 1:
                $subMenuNames[$i]=$aircraftsClass->get_popup_array();
                break;
            case 2:
                $subMenuNames[$i]=$instructorsClass->get_popup_array();
                break;
            case 3:
                $subMenuNames[$i][0]=new popup_menu($lang['SUBMENU_LIST'],'');
                $subMenuNames[$i][1]=new popup_menu($lang['SUBMENU_ADD_BOOK'],'');
                if (($menu==3)and($sub_menu==2))
                {
                    $subMenuNames[$i][2]=new popup_menu($lang['SUBMENU_MOD_BOOK'],'');
                }
                break;
            case 4:
                $subMenuNames[$i][0]=new popup_menu($lang['SUBMENU_DISPLAY_AND_FILE'],'');
                if (($userSession->isMember())and($userSession->parameter->isUseQualif()))
                {
                    $subMenuNames[$i][1]=new popup_menu($lang['SUBMENU_QUALIF'],'');
                }
                if ($userSession->isInstructor()or$userSession->isFreezeInstructorAllowed())
                {
                    $subMenuNames[$i][2]=new popup_menu($lang['SUBMENU_AVAIL'],'');
                    $subMenuNames[$i][3]=new popup_menu($lang['SUBMENU_ADD_AVAIL'],'');
                    if (($sub_menu==4)and($menu==4))
                    {
                        $subMenuNames[$i][4]=new popup_menu($lang['SUBMENU_MOD_AVAIL'],'');
                    }
                }
                break;
            case 5:
                $subMenuNames[$i][0]=new popup_menu($lang['SUBMENU_LIST_BY_NAME'],'');
                $subMenuNames[$i][1]=new popup_menu($lang['SUBMENU_LIST_BY_PROFILE'],'');
                break;
            }
        }
        
        switch($menu)
        {
        case 0:
            define_global('noalert',0);
            if (is_array($noalert))
            {
                foreach ($noalert as $qualifID => $flag)
                {
                    if ($flag)
                    {
                        $database->query('UPDATE member_qualif SET NOALERT=\'1\' WHERE MEMBERNUM=\''.$userSession->getAuthNum().'\' and QUALIFID=\''.$qualifID.'\'');
                    }
                }
            }
            require_once('./includes/navigation.php');
            break;
        case 1:
            require_once('./includes/navigation.php');
            break;
        case 2:
            require_once('./includes/navigation.php');
            break;
        case 3:
            switch ($sub_menu)
            {
            case 0:
                // Display slots
                require_once ('./includes/manageBooking.php');
                break;
            case 1:
                // Wait for a new slot
                require_once ('./includes/formBooking.php');
                break;
            case 2:
                // Wait for cancelling a slot
                require_once ('./includes/formBooking.php');
                break;
            case 11:
                // record a slot
                require_once ('./includes/recordBooking.php');
                break;
            case 12:
                // destroy a slot
                require_once ('./includes/recordBooking.php');
                break;
            case 13:
                // change a slot
                require_once ('./includes/recordBooking.php');
                break;
            }
            break;
        case 4:
            switch ($sub_menu)
            {
            case 0:
                require_once('./includes/manageOwnFile.php');
                break;
            case 1:
                require_once('./includes/manageQualif.php');
                break;
            case 2:
                require_once('./includes/manageInstructorsRests.php');
                break;
            case 3:
                require_once('./includes/formInstructorsRests.php');
                break;
            case 4:
                require_once('./includes/formInstructorsRests.php');
                break;
            case 8:
                require_once('./includes/updateMailingList.php');
                break;
            case 9:
                require_once('./includes/updateMailingList.php');
                break;
            case 10:
                require_once('./includes/recordOwnFile.php');
                break;
            case 11:
                require_once('./includes/recordQualif.php');
                break;
            case 12:
                require_once('./includes/recordInstructorsRests.php');
                break;
            case 13:
                require_once('./includes/recordInstructorsRests.php');
                break;
            case 14:
                require_once('./includes/recordInstructorsRests.php');
                break;
            }
            break;
        case 5:
            require_once('./includes/listMembers.php');
            break;
        //	CASE 6 is managed at the begin
        case 7:
            require_once('./admin/index.php');
            break;
        case 8:
            require_once('./flights/index.php');
            break;
        }
    }
}
$database->disconnect();

/* Record statistics */
/* TODO : print another $menuAction if no user is logged, if a qualifications check warning is displayed of if the user is choosing his profile */
$counterStop    = microtime( 1 );
$ellapsedTime   = sprintf( '%.02f', ($counterStop - $counterStart));
$dates          = explode('-', date('H:i:s-Ymd'));
define ('URL', $_SERVER['SERVER_NAME']);
$output         = URL.','.$dates[0].','.$menu.','.$ellapsedTime."\n";
file_put_contents('logs/'.$dates[1].'.log', $output, FILE_APPEND);
?>