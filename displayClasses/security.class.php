<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * security.class.php
 *
 * Perform some security checks and alert the user
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
 * @category   html engine and security
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: security.class.php,v 1.2.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat May 21 2005
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    return; // we stop the script now
}

require_once('./displayClasses/requestForm.class.php');

class security
{
    /**
    * database access
    * @var DBAccessor object
    * @access private
    */
    var $db;

    /**
    * used to display alerts
    * @var request requestForm object
    * @access private
    */
    var $request;
    
    /**
    * Constructor
    * @access public
    * @param $db DBAccessor object
	* @return null
	*/
    function security($db)
    {
        $this->db=$db;
        $this->request=null;
    }

    
    /**
    * check
    * perform some checks
    * @access public
    * @return null
    */
    function check()
    {
        $this->checkDir();
        $this->checkPassword();
        $this->closeAlert();
    }

    /**
    * checkDir
    * alert if some directories are not removed (install and sql)
    * @access private
    * @return null
    */
    function checkDir()
    {
        if (is_dir('install'))
        {
            $this->alert('SECURITY_INSTALL_DIR');
        }
        if (is_dir('sql'))
        {
            $this->alert('SECURITY_SQL_DIR');
        }
    }

    /**
    * checkPassword
    * alert if some passwords are not removed
    * @access private
    * @return null
    */
    function checkPassword()
    {
        $result=$this->db->queryAndFetch('select * from authentication where NAME=\'admin\'');
        if ($result)
        {
            $this->alert('SECURITY_ADMIN');
        }
    }

    /**
    * alert
    * Display an alert
    * @access private
    * @param $sentence string corresponding to a lang array entry
    * @return null
    */
    function alert($sentence)
    {
        global $lang;
        if ($this->request==null)
        {
        	$this->request=new requestForm($lang['SECURITY_ALERT'],'','','index','alertId');
        }
        $this->request->addTitle($lang[$sentence]);
    }

    /**
    * close Alert display if necessary
    * @access private
    * @param $sentence string
    * @return null
    */
    function closeAlert()
    {
        if ($this->request!=null)
        {
            $this->request->close();
        }
    }
}
?>