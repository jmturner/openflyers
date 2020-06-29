<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * db.class.php
 *
 * Helper class to access a mySQL database
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
 * @category   computation
 * @author     Patrice GODARD <patrice.godard@free.fr>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: db.class.php,v 1.16.2.3 2005/11/26 16:52:07 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon May 31 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

class DBAccessor
{
	var $host;
	var $base;
	var $user;
	var $password;
	var $connected;
	var $result;
	var $connID;
	
	/*
	* Constructor
	* param: host - host
	* param: base - database name
	* param: user - user (login)
	* param: password - password
	*/
	function DBAccessor($host,$base,$user,$password)
	{
		$this->host = $host;
		$this->base = $base;
		$this->user = $user;
		$this->password = $password;
		$this->connected = false;
		$this->result = null;
		$this->connID = null;
	}
	
	/*
	* connect to the database
	*/
	function connect()
	{
		$this->connID = mysql_connect($this->host, $this->user, $this->password) OR die('Error: '.mysql_error());
		mysql_select_db($this->base) or die( 'Error: '.mysql_error());
		$this->connected = true;
	}
	
	/*
	* disconnect from the database and free up resources
	*/
	function disconnect()
	{
		$this->free();
        if($this->connected)
        {
            mysql_close($this->connID);
            $this->connected = false;
        }
	}
	
	/*
	* free up resources
	*/
	function free()
	{
		if(($this->result!=null)and($this->result!=1))
		{
			mysql_free_result($this->result);
			$this->result=null;
		}
	}

	/*
	* Send an SQL request
	* param: query - SQL request
	* return: result depending on request type
	* Be care to free the result with the free function !
	*/
	function query($query)
	{
		if(!$this->connected)
		{
			$this->connect();
		}
		$this->result=mysql_query($query) OR die( 'Error: '.mysql_error());
		return($this->result);
	}	

	function fetch()
	{
	    if ($this->result)
	    {
            return mysql_fetch_object($this->result);
	    }
	    else 
	    {
	        return (false);
	    }
	}	

	function fetchAssoc()
	{
	    if ($this->result)
	    {
            return mysql_fetch_array($this->result, MYSQL_ASSOC);
	    }
	    else 
	    {
	        return (false);
	    }
	}	

	function numRows()
	{
		return mysql_num_rows($this->result);
	}

   /**
     * query, fetch ONE value and free
     *
     * @access public
     * @param $query string
     * @return mixed variable
     */
	function query_and_fetch_single($query)
	{
		if(!$this->connected)
		{
			$this->connect();
		}
		$this->result=mysql_query($query) OR die( 'Error: '.mysql_error());
		$row=mysql_fetch_row($this->result);
		$this->free();
		if($row)
		{
			$row=$row[0];
		}
		return($row);
	}

    /**
     * query, fetch and free (only one row is returned)
     *
     * @access public
     * @param $query string
     * @return array
     */
	function queryAndFetch($query)
	{
        $this->query($query);
		$row=$this->fetch();
		$this->free();
		return($row);
	}

    /**
     * query and free
     *
     * @access public
     * @param $query string
     * @return boolean
     */
	function queryAndFree($query)
	{
	    $returnValue=false;
        if ($this->query($query))
        {
            $returnValue=true;
        }
		$this->free();
		return($returnValue);
	}

	function find_free_num($table_label,$num_label)	// look for a $num_label in $table_label unused
	{
		$result=$this->query('SELECT '.$num_label.' FROM '.$table_label);
		$list_number_table=array();
		while($number=mysql_fetch_row($result))
		{
			$list_number_table[]=$number[0];
		}
		$free_num=-1;
		for($i=1;$free_num<0;$i++)
		{
			if(!(in_array($i,$list_number_table)))
			{
				$free_num=$i;
			}
		}
		return $free_num;
	}
}
?>