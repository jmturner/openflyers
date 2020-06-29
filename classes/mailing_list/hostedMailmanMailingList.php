<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * hostedMailmanMailingList.php
 *
 * manage a mailman host-based mailing list interface via automatic database 
 * feeding. This action should be completed by an automatic script (cron)
 * looking at the database.
 * Database tables are:
 *                      mailmanAdd(mail,list)
 *                      mailmanRemove(mail,list)
 * database name, host and password are defined in the conf/connect.php file
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
 * @version    CVS: $Id: hostedMailmanMailingList.php,v 1.1.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu Sep 6 2005
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./classes/mailing_list/genericMailingList.php');

class hostedMailmanMailingList extends genericMailingList
{
    /**
     * add a new mail to the mailing list
     *
     * @access public
     * @param $_email string email to be added
     * @return null
     */
    function subscribe($_email)
    {
		if($_email!='')
		{
		    $this->_db->query('insert into mailmanAdd set mail=\''.$_email.'\', list=\''.$this->_name.'\'');
		    $this->_db->free();
		}
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
		if($_email!='')
		{
		    $this->_db->query('insert into mailmanRemove set mail=\''.$_email.'\', list=\''.$this->_name.'\'');
		    $this->_db->free();
		}
    }
}
?>