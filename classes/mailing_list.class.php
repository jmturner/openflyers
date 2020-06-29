<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * mailing_list.class.php
 *
 * manage a mailing list via automatic email functions call
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
 * @category   mail
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: mailing_list.class.php,v 1.15.4.8 2007/04/18 08:09:10 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat May 31 2003
 */

class mailing_list
{
	var $private_ml_class;
	var $db;

	function mailing_list($database)
	{
		$result=$database->query('select MAILING_LIST_NAME, MAILING_LIST_TYPE from clubs where NUM=1');
		if (($row=mysql_fetch_object($result))&&($row->MAILING_LIST_NAME!=''))
		{
			$provider_class=$row->MAILING_LIST_TYPE.'MailingList';
			require_once('./classes/mailing_list/'.$provider_class.'.php');
			$this->db='';
			if (strstr($provider_class,'hosted'))
			{
			    $this->db=new DBAccessor(MAILING_LIST_HOST,MAILING_LIST_BASE,MAILING_LIST_VISITOR,MAILING_LIST_PASSWORD_VISITOR);
			}
			$this->private_ml_class=new $provider_class($row->MAILING_LIST_NAME, $this->db);
		}
		$database->free();
	}

	function free()
	{
	    $this->private_ml_class->free();
	}
	
	function add_email($email)
	{
		$provider_class=$this->private_ml_class;
		if($email!='')
		{
			$provider_class->subscribe($email);
		}
	}

	function remove_email($email)
	{
		$provider_class=$this->private_ml_class;
		if($email!='')
		{
			$provider_class->unsubscribe($email);
		}
	}

	function list_emails()
	{
		$provider_class=$this->private_ml_class;
		$provider_class->list_emails();
	}
}
?>