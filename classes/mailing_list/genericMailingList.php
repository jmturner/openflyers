<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * genericMailingList.php
 *
 * generic mailing interface, should be overloaded before use
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
 * @category   mailing list manager
 * @author     Christophe Laratte <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: genericMailingList.php,v 1.1.2.5 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Sep 6 2005
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./classes/mail.class.php');

class genericMailingList
{
    /**
    * mailing list name
    * @var string
    * @access private
    */
    var $_mailingListName;

    /**
    * database to access hosted mailing list
    * @var DBAccessor
    * @access private
    */
    var $_db;

    /**
    * domain name of mailing list
    * @var string
    * @access private
    */
    var $_domain;

    /**
    * left-part of mailing list name
    * @var string
    * @access private
    */
    var $_name;

    /**
     * Constructor
     *
     * Creates a new mailman mailing list management Object
     *
     * @access public
     * @param $_name string mailman mailing list name
     * @param $_db DBAccessor object
     * @return null
     */
    function genericMailingList($_name, $_db)
    {
        $this->_mailingListName = $_name;
        $this->_db=$_db;
        list($this->_name, $this->_domain) = explode('@', $this->_mailingListName);
    }

    /**
     * Destructor
     *
     * Close everything
     *
     * @access public
     * @return null
     */
    function free()
    {
        if ($this->_db!='')
        {
            $this->_db->disconnect();
        }
    }

    /**
     * add a new mail to the mailing list
     *
     * @access public
     * @param $_email string email to be added
     * @return null
     */
    function subscribe($_email)
    {
    }

    /**
     * remove a mail from the mailing list
     *
     * @access public
     * @param $_email string email to be removed
     * @return null
     */
    function unsubscribe($_email)
    {
    }

    function list_email($_email)
    {
    }
    
    /**
     * send a mail to the mailing list
     *
     * @access protected
     * @param $_email string email source
     * @param $_recipient string email destination
     * @param $_subject string subject content of the mail
     * @return null
     */
    function notifyByMail($_sender, $_recipient, $_subject='')
    {
        $_mailer=new ofMail($_sender);
        $_mailer->addRecipient($_recipient);
        $_mailer->send($_subject,'');
    }
}
?>