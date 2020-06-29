<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * recordOwnFile.php
 *
 * save prefs and personnal datas
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
 * @category   database management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: recordOwnFile.php,v 1.15.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Feb 19 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// We assume that $userSession and $database are well defined

require_once('./pool/functions.php');
require_once('./includes/redirect.php');
require_once('./displayClasses/requestForm.class.php');

function displayMessage($title,$continue=false)
{
    global $lang;
	if($continue)
	{
		$menu=0;
		$button_text=$lang['OK'];
	}
	else
	{
		$menu=4;
		$button_text=$lang['BACK_BUTTON'];
	}
	require_once('./includes/header.php');
?></head><body><?php
	$request=new requestForm($title);
	$request->addHidden('menu',$menu);
	$request->close($button_text);
	require_once('./includes/footer.php');
}

////////////////////////////// Main code start here /////////////////////////////////
if((($userSession->isNothingAllowed())AND((!define_global('admin_login'))OR(!define_global('admin_password'))))
OR((!$userSession->isNothingAllowed())AND(!define_global('email')))OR(!define_global('view_height'))
OR(!define_global('view_width'))OR(!define_global('format_date'))
OR(!define_global('old_password'))OR(!define_global('language'))
OR(!define_global('timezone'))OR(!define_global('new1_password'))OR(!define_global('new2_password')))
{
	displayMessage($lang['ERROR_TRANSMIT_DATA']);
}
else
{
	$continue_flag=true;
	if($userSession->isNothingAllowed())
	{
		$dummy_member=0;
		$continue_flag=isSetClubAllowed(getAllPermits($userSession->db,$admin_login,$admin_password,$dummy_member));
	}
	else 
	{
	    define_global('address');
	    define_global('homephone');
	    define_global('workphone');
	    define_global('cellphone');
	    define_global('zipcode');
	    define_global('city');
	    define_global('state');
	    define_global('country');
	}
	if($continue_flag)
	{
	    $userSession->setHomePhone($homephone);
	    $userSession->setWorkPhone($workphone);
	    $userSession->setCellPhone($cellphone);
	    $userSession->setTimezone($timezone);
	    $userSession->setLang($language);
		$userSession->setViewWidth($view_width);
		$userSession->setViewHeight($view_height);
		$userSession->set_inst_on_one_day(define_global('inst_display')=='on');
		$userSession->set_aircraft_on_one_day(define_global('aircraft_display')=='on');
		$userSession->setPublicHomePhone((define_global('homephonepublic')=='on'));
		$userSession->setPublicWorkPhone((define_global('workphonepublic')=='on'));
		$userSession->setPublicCellPhone((define_global('cellphonepublic')=='on'));
		$userSession->setPublicEmail((define_global('emailpublic')=='on'));
		$userSession->setMailNotification((define_global('mailack')=='on'));
		$userSession->set_french_date_display($format_date);
		$userSession->setLegendPopup(define_global('legendPopup'));
		if(!$userSession->isNothingAllowed())
		{
    	    $userSession->setAddress($address);
	        $userSession->setZipcode($zipcode);
	        $userSession->setCity($city);
	        $userSession->setState($state);
            $userSession->setCountry($country);
            $userSession->setEmail($email);
		}

		$aircraftsList='';
		for($i=0;$i<$aircraftsClass->get_complete_size();$i++)
		{
			if(!define_global('ac'.$i))
			{
				$item=$aircraftsClass->get_complete_value($i);
				$aircraftsList=$aircraftsList.'*'.$item->NUM;
			}
		}
		$userSession->setAircraftsViewed($aircraftsList);

		$instructorsList='';
		for($i=0;$i<$instructorsClass->get_complete_size();$i++)
		{
			if(!define_global('inst'.$i))
			{
				$item=$instructorsClass->get_complete_value($i);
				$instructorsList=$instructorsList.'*'.$item->NUM;
			}
		}
		$userSession->setInstructorsViewed($instructorsList);

		$ok_flag=true;
		if(($old_password)OR($new1_password)OR($new2_password))
		{
		    if(($old_password)AND($new1_password)AND($new2_password))
		    {
		        $database->query('select authentication.NUM from authentication
					where authentication.NUM=\''.$userSession->getAuthNum().'\' and authentication.PASSWORD=\''.passwordCrypt($old_password).'\'');
		        $result=$database->fetch();
		        $database->free();
		        if(!($result))
		        {
		            displayMessage($lang['OWN_FILE_BAD_OLD_PWD']);
		            $ok_flag=false;
		        }
		        elseif($new1_password!=$new2_password)
		        {
		            displayMessage($lang['OWN_FILE_TWICE_SAME_PWD']);
		            $ok_flag=false;
		        }
		        else
		        {
		            $result=$database->query('update authentication set PASSWORD=\''.passwordCrypt($new1_password).'\'
						where NUM=\''.$userSession->getAuthNum().'\' and PASSWORD=\''.passwordCrypt($old_password).'\'');
		            if($result)
		            {
		                displayMessage($lang['OWN_FILE_OK_NEW_PWD'],true);
		                $ok_flag=false;
		            }
		            else
		            {
		                displayMessage($lang['OWN_FILE_ERR_CHANGE_PWD']);
		                $ok_flag=false;
		            }
		        }
		    }
		    else
		    {
		        displayMessage($lang['OWN_FILE_ALL_PWD']);
		        $ok_flag=false;
		    }
		}
		if($ok_flag)
		{
            redirect($userSession,$firstDisplayedDate->getTS());
		}
	}
	else
	{
		displayMessage($lang['OWN_FILE_NO_ALLOWED_MOD_PUBLIC']);
	}
}

?>
