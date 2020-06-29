<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * serie.class.php
 *
 * Store a serie and say which must be viewed
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
 * @category   virtual serie management
 * @author     christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: serie.class.php,v 1.19.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 9 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

// little class designed to transmit popup display for menu

class popup_menu
{
	var $label='';
	var $popup='';

	function popup_menu($label,$popup)
	{
		$this->label=$label;
		$this->popup=$popup;
	}

	function getLabel()
	{
		return $this->label;
	}

	function get_popup()
	{
		return $this->popup;
	}
}

// generic class should be derivated in instructorSerie or aircraftSerie
// its goal is to store all informations on all instructors or aircrafts 
// to get viewed ones
// and labels for display

class serie
{
	var $complete_serie=array();
	var $viewed_serie=array();
	var $complete_size=0;
	var $viewed_size=0;
	var $view_list='';				// just add * between each item for serializing

	function serie($result)
	{
		for($i=0;$row=mysql_fetch_object($result);$i++)
		{
			$this->complete_serie[$i]=$row;
		}
		$this->complete_size=$i;
	}
	
	function get_complete_size()
	{
		return $this->complete_size;
	}
	
	function get_complete()
	{
		return $this->complete_serie;
	}

	function get_complete_value($i)
	{
		return $this->complete_serie[$i];
	}

	function get_viewed_list()
	{
		return $this->view_list;
	}
	
	function get_viewed()
	{
		return $this->viewed_serie;
	}

	function get_viewed_size()
	{
		return $this->viewed_size;
	}
	
// In : number in the array
// Out : real number in the database
	function get_viewed_value($i)
	{
		return $this->viewed_serie[$i];
	}

// In : real number in the database
// Out : number in the array
	function get_viewed_array_value($value)
	{
		$return=0;
		for($i=0;($i<$this->viewed_size)and($return==0);$i++)
		{
			$dummy=$this->viewed_serie[$i];
			if($dummy->NUM==$value)
			{
				$return=$i;
			}
		}
		return $return;
	}

	function is_viewed($value)
	{
		if((!$this->view_list)OR($value==''))
		{
			return true;
		}
		else
		{
			$result=ereg('\*'.$value.'$|'.'\*'.$value.'\*',$this->view_list);
			if($result==false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function set_viewed($list)
	{
		$this->view_list=$list;
		$this->viewed_serie=array();
		$j=0;
		for($i=0;$i<$this->complete_size;$i++)
		{
			$dummy=$this->complete_serie[$i];
			if($this->is_viewed($dummy->NUM))
			{
				$this->viewed_serie[$j]=$this->complete_serie[$i];
				$j=$j+1;
			}
		}
		$this->viewed_size=$j;
	}

	function get_num_array()
	{
		$local_array=array();
		for($i=0;$i<$this->get_viewed_size();$i++)
		{
			$item=$this->get_viewed_value($i);
			$local_array[$i]=$item->NUM;
		}
		return $local_array;
	}

	function get_popup_array()
	{
		$local_array=array();
		for($i=0;$i<$this->get_viewed_size();$i++)
		{
			$local_array[$i]=$this->get_popup_menu($i);
		}
		return $local_array;
	}
}

class aircraft_serie extends serie
{
	function get_popup_menu($i)
	{
        global $lang;
		$item=$this->get_viewed_value($i);
		return new popup_menu($item->CALLSIGN,$lang['AIRCRAFT_TYPE'].' : '.$item->TYPE.' - '.$lang['SEATS'].' : '.$item->SEATS_AVAILABLE.' - '.$lang['PRICE'].' : '.$item->FLIGHT_HOUR_COSTS.' '.$lang['MONEY'].'/h. '.$item->COMMENTS);
	}

	function getList()
	{
		$list=array();
		foreach ($this->complete_serie as $element)
		{
			$list[]=array($element->NUM,$element->CALLSIGN);
		}
		return $list;
	}
}

class instructor_serie extends serie
{


	function get_popup_menu($i)
	{
		$item=$this->get_viewed_value($i);
		return new popup_menu($item->SIGN,$item->FIRST_NAME.' '.$item->LAST_NAME);
	}

	function getList()
	{
		$list=array();
		foreach ($this->complete_serie as $element)
		{
			$list[]=array($element->NUM,$element->LAST_NAME.' '.$element->FIRST_NAME.' ('.$element->SIGN.')');
		}
		return $list;
	}
}

?>