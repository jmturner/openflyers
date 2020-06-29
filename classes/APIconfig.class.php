<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * APIconfig.class.php
 *
 * Allow API configuration (I/O)
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
 * @category   API
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: APIconfig.class.php,v 1.11.2.3 2006/03/23 14:20:12 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

class APIconfig
{

    var $qualif_enabled;
    var $qualif_required;
    var $subscription_enabled;
    var $subscription_required;
    var $subscriptionDate;
    var $subscription_default_profile;
    var $databaseConnection;

    function APIconfig($databaseHandler)
    {
    	$this->databaseConnection = $databaseHandler;
    	$this->getQualifConfig();
    	$this->getSubscriptionConfig();
    }
    
	/***** API Config - License part *****/
    
    function getQualifConfig()
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query('select * from parameter where CODE="QUALIF"');
    	$configurationQueryResult = mysql_fetch_object($configurationQueryResult);
    	if ($configurationQueryResult and $configurationQueryResult->ENABLED==1) {
    		$this->qualif_enabled = true;
    		if ($configurationQueryResult->INT_VALUE==0) {
    			$this->qualif_required = false;
    		} else {
    			$this->qualif_required = true;
    		}
    	} else {
    		$this->qualif_enabled = false;
    	}
    }
    
    function disableQualif()
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query('DELETE FROM parameter where CODE="QUALIF"');
    }
    
    function enableQualif($requiredQualif)
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query_and_fetch_single('SELECT count(*) FROM parameter where CODE="QUALIF"');
    	if ($configurationQueryResult == 0) {
    		if ($requiredQualif) {
    			$configurationQueryResult = $localHandler->query('INSERT INTO parameter SET ENABLED="1", INT_VALUE="1", CODE="QUALIF"');
    		} else {
    			$configurationQueryResult = $localHandler->query('INSERT INTO parameter SET ENABLED="1", INT_VALUE="0", CODE="QUALIF"');
    		}
    	} else {
    		if ($requiredQualif) {
    			$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="1", INT_VALUE="1" where CODE="QUALIF"');
    		} else {
    			$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="1", INT_VALUE="0" where CODE="QUALIF"');
    		}
    	}
    }

	/***** API Config - Subscription date part *****/
	function getSubscriptionConfig()
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query_and_fetch_single('select count(*) from parameter where CODE="SUBSCRIPTION"');
    	if ($configurationQueryResult == 0) {
    		$this->subscription_enabled 	= false;
			$this->subscription_required 	= false;
			$this->subscriptionDate = '0000-00-00';
			$this->subscription_default_profile = 0;
    	} else {
	    	$configurationQueryResult = $localHandler->query('select * from parameter where CODE="SUBSCRIPTION"');
	    	$configurationQueryResult = mysql_fetch_object($configurationQueryResult);
			switch ($configurationQueryResult->ENABLED) {
			case 1	:	$this->subscription_required 	= false;
						$this->subscription_enabled 	= true;
	    				$this->subscriptionDate = $configurationQueryResult->CHAR_VALUE;
	    				$this->subscription_default_profile = $configurationQueryResult->INT_VALUE;
	   					break;
			case 2	:	$this->subscription_required 	= true;
						$this->subscription_enabled 	= true;
	    				$this->subscriptionDate = $configurationQueryResult->CHAR_VALUE;
	    				$this->subscription_default_profile = $configurationQueryResult->INT_VALUE;
	    				break;
	    	}
    	}
    }
    
    function disableSubscription()
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query('DELETE FROM parameter where CODE="SUBSCRIPTION"');
    }
    
    function updateSubscriptionConfig()
    {
    	$localHandler = $this->databaseConnection;
    	$thedate = $_POST['year_of_end'].'-'.$_POST['month_of_end'].'-'.$_POST['day_of_end'];
    	$theprofile = $_POST['user_profile'];
    	$configurationQueryResult = $localHandler->query('UPDATE parameter SET CHAR_VALUE="'.$thedate.'", INT_VALUE="'.$theprofile.'"  where CODE="SUBSCRIPTION"');
    }
    
    function enableSubscription($isRequired)
    {
    	$localHandler = $this->databaseConnection;
    	$configurationQueryResult = $localHandler->query_and_fetch_single('SELECT count(*) FROM parameter WHERE CODE="SUBSCRIPTION"');
    	$init = ($configurationQueryResult == 0);
    	$thedate = $_POST['year_of_end'].'-'.$_POST['month_of_end'].'-'.$_POST['day_of_end'];
    	$theprofile = $_POST['user_profile'];
    	if ($configurationQueryResult == 0) {
    		if ($isRequired) {
    			$configurationQueryResult = $localHandler->query('INSERT INTO parameter SET ENABLED="2", INT_VALUE="'.$theprofile.'", CHAR_VALUE="'.$thedate.'", CODE="SUBSCRIPTION"'); 
    		} else {
    			$configurationQueryResult = $localHandler->query('INSERT INTO parameter SET ENABLED="1", INT_VALUE="'.$theprofile.'", CHAR_VALUE="'.$thedate.'", CODE="SUBSCRIPTION"');	
    		}
   		} else {
			if ($isRequired) {
    			$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="2", INT_VALUE="'.$theprofile.'", CHAR_VALUE="'.$thedate.'"  where CODE="SUBSCRIPTION"');
	    	} else {
    			$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="1", INT_VALUE="'.$theprofile.'", CHAR_VALUE="'.$thedate.'"  where CODE="SUBSCRIPTION"');
    		}
    	}
    }
    
    function switchToRequired()
    {
    	$localHandler = $this->databaseConnection;
		$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="2" where CODE="SUBSCRIPTION"');
    }

    function switchToWarning()
    {
    	$localHandler = $this->databaseConnection;
		$configurationQueryResult = $localHandler->query('UPDATE parameter SET ENABLED="1" where CODE="SUBSCRIPTION"');
    }
    /**
     * get parameters from database $db
     *
     * @access public
     * @param DBAccessor object $db
     * @return null
     */
    function getFromDatabase($db)
    {
        $db->query('select * from parameter');
        $row=$db->fetch();
        while ($row) {
            $code=$row->CODE;
            $this->enabled[$code]=$row->ENABLED;
            $this->intValue[$code]=$row->INT_VALUE;
            $this->charValue[$code]=$row->CHAR_VALUE;
            $row=$db->fetch();
        }
        $db->free();
    }

    /**
     * Say if we use qualification
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isUseQualif()
    {
        if (isset($this->enabled['QUALIF'])) {
            return $this->enabled['QUALIF'];
        } else {
            return false;
        }
    }

    /**
     * Say if we use restriction in cas of outdate or no qualif
     *
     * @access public
     * @param null
     * @return boolean
     */
    function isRestrictiveQualif()
    {
        if (isset($this->intValue['QUALIF'])and($this->isUseQualif())) {
            return $this->intValue['QUALIF'];
        } else {
            return false;
        }
    }

    /**
     * Say if we use subscription date and which level
     *
     * @access public
     * @param null
     * @return false/0 or subscription level (1: we use it, but we do not change profile in case of outdate subscription, 2: we use and change profile)
     */
    function isUseSubscription()
    {
        if (isset($this->enabled['SUBSCRIPTION'])) {
            return $this->enabled['SUBSCRIPTION'];
        } else {
            return false;
        }
    }

    /**
     * In case of subscription level 2 management, return outdate profile
     *
     * @access public
     * @param null
     * @return integer (profile) or false if we are not subscription level 2 management
     */
    function getOutdateSubscriptionProfile()
    {
        if ((isset($this->enabled['SUBSCRIPTION']))and($this->enabled['SUBSCRIPTION']==2)) {
            return $this->intValue['SUBSCRIPTION'];
        } else {
            return false;
        }
    }

    /**
     * In case of subscription level 1 or 2 management, return default subscription date
     *
     * @access public
     * @param null
     * @return ofDate (default subscription date) or false if we are not subscription level 1 or 2 management
     */
    function getDefaultSubscriptionDate()
    {
        if ((isset($this->enabled['SUBSCRIPTION']))and($this->enabled['SUBSCRIPTION']>0)) {
            return new ofDate($this->charValue['SUBSCRIPTION']);
        } else {
            return false;
        }
    }
    
	function showSubscriptionDate()
	{
		if ($this->subscriptionDate == null) {
			$dateData = '2027-12-31';
		}
		$dateData = explode("-", $this->subscriptionDate); // [0] = YYYY; [1] = DD; [2] = MM
		$returned_value = '';
		$returned_value .= '<select name="day_of_end" size="1">';
		for ($boucle = 1; $boucle < 32; $boucle++) {
			if ($dateData[2] == $boucle) {
				if ($boucle < 10) {
					$returned_value .= '<option value="0'.$boucle.'" selected>'.$boucle.'</option>';
				} else {
					$returned_value .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
				}
				
			} else {
				if ($boucle < 10) {
					$returned_value .= '<option value="0'.$boucle.'">'.$boucle.'</option>';
				} else {
					$returned_value .= '<option value="'.$boucle.'">'.$boucle.'</option>';
				}
			}
		}
		$returned_value .= '</select><select name="month_of_end" size="1">';
		for ($boucle = 1; $boucle < 13; $boucle++) {
			if ($dateData[1] == $boucle) {
				if ($boucle < 10) {
					$returned_value .= '<option value="0'.$boucle.'" selected>'.$boucle.'</option>';
				} else {
					$returned_value .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
				}
			} else {
				if ($boucle < 10) {
					$returned_value .= '<option value="0'.$boucle.'">'.$boucle.'</option>';
				} else {
					$returned_value .= '<option value="'.$boucle.'">'.$boucle.'</option>';
				}
				
			}
		}
		$returned_value .= '</select><select name="year_of_end" size="1">';
		$previous_year = date('Y') - 1;
		for ($boucle = $previous_year; $boucle < 2037; $boucle++) {
			if ($dateData[0] == $boucle) {
				$returned_value .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
			} else {
				$returned_value .= '<option value="'.$boucle.'">'.$boucle.'</option>';
			}
		}
		$returned_value .= '</select>';
		return $returned_value;
	}
}
?>