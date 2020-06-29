<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * functions.php
 *
 * functions pool
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
 * @category   various functions
 * @author     Patrick HUBSCHER <chakram@openflyers.org>
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: functions.php,v 1.10.2.4 2005/10/28 17:44:22 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Sept 8 2003
 */

/**
* htmlTransTable list all translation special html chars
* @access private
* @var string
*/
$htmlTransTable=get_html_translation_table(HTML_ENTITIES);
$htmlTransTable=array_flip($htmlTransTable);

function define_global($name,$defaultValue='',$forbiddenValue='')
{
	global $$name;
	if(isset($_REQUEST[$name]))
	{
        if (is_array($_REQUEST[$name]))
        {
            $localTable=array();
            foreach ($_REQUEST[$name] as $key => $ele)
            {
                $requestValue=setSlashes($ele);
		        if($requestValue!=$forbiddenValue)
		        {
			        $localTable[$key]=$requestValue;
		        }
		        else
		        {
			        $localTable[$key]=$defaultValue;
		        }
            }
            $$name=$localTable;
        }
        else
        {
            $requestValue=setSlashes($_REQUEST[$name]);
		    if($requestValue!=$forbiddenValue)
		    {
			    $$name=$requestValue;
		    }
		    else
		    {
			    $$name=$defaultValue;
		    }
		}
        return(true);
	}
	else
	{
		$$name=$defaultValue;
		return(false);
	}
}

function echoFirstLetter($text)
{
    echo strtoupper($text{0});
}

/**
* Crypt password $text
* @access public
* @param $text string
* @return string
*/
function passwordCrypt($text)
{
    return md5($text);
}

/**
* Add slashes if require (ie magic_quote_gpc=Off). Should be use each time we get back a Get, Post or Cookie (G,P,C)
* @access public
* @param $text string
* @return string
*/
function setSlashes($text)
{
    if (get_magic_quotes_gpc())
    {
    	return $text;
    }
    else 
    {
        return addslashes($text);
    }
}

/**
* html2Text
* @access public
* @param $text string
* @return string
*/
function html2Text($text)
{
    global $htmlTransTable;
    return strtr($text,$htmlTransTable);
}

/**
 * display a select field for timezone choice. Current choice is stored in $compareVar
 *
 * @param string $compareVar
 * @return string
 */
function displayTimeZone($compareVar)
{
	global $_DATE_TIMEZONE_DATA;
	$timezoneString = '';
	while($timezone_list = each($_DATE_TIMEZONE_DATA)) { 
		$timezoneString .= '<option value="'.$timezone_list['key'].'"';
		if($timezone_list['key'] == $compareVar) {
			$timezoneString .= ' selected="selected"';
		}
		$timezoneString .= '>'.floor($timezone_list['value']['offset']/3600000).' '.$timezone_list['key'].'</option>';
	}
	return $timezoneString;
}

/**
 * Display a select field to choose "hour" ($hour_var is var name for select field)
 *
 * @param string $hour_var
 * @param integer $hour_selected
 * @return string
 */
function showSelectHour($hour_var, $hour_selected)
{
	$hour_returned='<select name="'.$hour_var.'" size="1">';
	if ($hour_selected == '') {
		for ($boucle = 0; $boucle < 24; $boucle++) {
			if ($boucle < 10) {
				$hour_returned .= '<option value="'.$boucle.'">0'.$boucle.'</option>';
			} else {
				$hour_returned .= '<option value="'.$boucle.'">'.$boucle.'</option>';
			}
		}
	} else {
		for ($boucle = 0; $boucle < 24; $boucle++) {
			if ($boucle == $hour_selected) {
				if ($boucle < 10) {
					$hour_returned .= '<option value="'.$boucle.'" SELECTED>0'.$boucle.'</option>';
				} else {
					$hour_returned .= '<option value="'.$boucle.'" SELECTED>'.$boucle.'</option>';
				}
			} else {
				if ($boucle < 10) {
					$hour_returned .= '<option value="'.$boucle.'">0'.$boucle.'</option>';
				} else {
					$hour_returned .= '<option value="'.$boucle.'">'.$boucle.'</option>';
				}
			}
		}
	}
	$hour_returned .= '</select>';
	return $hour_returned;
}

/**
 * Display a select field to choose "minutes" ($minute_var is var name for select field)
 *
 * @param string $minute_var
 * @param integer $minute_selected
 * @return string
 */
function showSelectMinute($minute_var, $minute_selected)
{
	$minute_returned = '<select name="'.$minute_var.'" size="1">';
	if ($minute_selected =='') {
		for ($boucle = 0; $boucle < 60; $boucle += 15) {
			if ($boucle < 10) {
				$minute_returned.='<option value="'.$boucle.'">0'.$boucle.'</option>';
			} else {
				$minute_returned.='<option value="'.$boucle.'">'.$boucle.'</option>';
			}
		}
	} else {
		for ($boucle = 0; $boucle < 60; $boucle += 15) {
			if ($minute_selected == $boucle) {
				if ($boucle < 10) {
					$minute_returned .= '<option value="'.$boucle.'" SELECTED>0'.$boucle.'</option>';
				} else {
					$minute_returned .= '<option value="'.$boucle.'" SELECTED>'.$boucle.'</option>';
				}
			} else {
				if ($boucle < 10) {
					$minute_returned .= '<option value="'.$boucle.'">0'.$boucle.'</option>';
				} else {
					$minute_returned .= '<option value="'.$boucle.'" >'.$boucle.'</option>';
				}
			}
		}
	}
	$minute_returned .= '</select>';
	return $minute_returned;
}

/**
 * convert a raw text in html
 *
 * @param string $string
 * @return string
 */
function unhtmlentities($string) 
{
	if (function_exists("html_entity_decode")) {
		return html_entity_decode($string);
	} else {
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		return strtr($string, $trans_tbl);
	}
}

// function define_variable(variable name, variable's default content)
// return the value of the cookie or the value sent by POST (if cookie is not available) or the value sent by GET (if the first and second ones are not available)
// if no VAR is available, it sends a default value or false if default value is an empty string.
/**
 * return the value of the cookie or the value sent by POST (if cookie is not available) or the value sent by GET (if the first and second ones are not available). If no VAR is available, it sends a default value.
 *
 * @param string $var_name
 * @param mixed $default_content
 * @return mixed
 */
function define_variable($var_name, $default_content)
{
	if (isset($_REQUEST[$var_name])) {
		return $_REQUEST[$var_name];
	} else {
		return $default_content;	
	}
} 
	

/**
 * Display profiles choice 
 *
 * @param unknown_type $test_bit
 * @param ressource $database
 * @param object $userSession
 * @param unknown_type $the_default_choice
 * @return string
 */
function showProfiles($test_bit, $database, $userSession, $the_default_choice)
{
	$result_profiling = $database->query('SELECT NUM, NAME, PERMITS FROM profiles ORDER BY NUM');
	$boucle = 0;
	while ($row_profiling = mysql_fetch_object($result_profiling)) {
		$tableau_profil_decimal[$boucle] 	= $row_profiling->NUM;
		$tableau_profil_nom[$boucle] 		= stripslashes($row_profiling->NAME);
		$tableau_profil_permission[$boucle] = $row_profiling->PERMITS;
		$boucle++;
	}
	$max_profil_number= count($tableau_profil_decimal);
	$choixProfile = '<table style="font-size:12px;">';
	$init_profile = true;
	for ($i = 0; $i < $max_profil_number; $i++) {
		if ($test_bit == $tableau_profil_decimal[$i]) {
			$choixProfile .= '<tr><td>'.$tableau_profil_nom[$i].'</td><td><input type="radio" name="user_profile" value="'.$tableau_profil_decimal[$i].'" checked /></td></tr>';
		} else {
			$choixProfile .= '<tr><td>'.$tableau_profil_nom[$i].'</td><td><input type="radio" name="user_profile" value="'.$tableau_profil_decimal[$i].'" /></td></tr>';
		}
	}		
if ($test_bit == 0) {
	$choixProfile .= '<tr><td>'.$the_default_choice.'</td><td><input type="radio" name="user_profile" value="0" checked /></td></tr>';
} else {
	$choixProfile .= '<tr><td>'.$the_default_choice.'</td><td><input type="radio" name="user_profile" value="0" /></td></tr>';
}
$choixProfile .= '</table>';
return $choixProfile;
}

/**
 * Display formatted string according to date format choice (select field)
 *
 * @param string $myDate
 * @param boolean $notEnglishFormat
 * @return string
 */
function showDate($myDate, $notEnglishFormat)
{
	$dateData = explode("-", $myDate); // [0] = YYYY; [1] = DD; [2] = MM
	$returnedValue[0] = '';
	$returnedValue[1] = '';
	$returnedValue[2] = '';
	$returnedValue[2] .= '<select name="dayDate" size="1">';
	for ($boucle = 1; $boucle < 32; $boucle++) {
		if ($dateData[2] == $boucle) {
			$returnedValue[2] .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
		} else {
			$returnedValue[2] .= '<option value="'.$boucle.'">'.$boucle.'</option>';
		}
	}
	$returnedValue[2] .= '</select>';
	// end of days
	$returnedValue[1] .= '<select name="monthDate" size="1">';
	for ($boucle = 1; $boucle < 13; $boucle++) {
		if ($dateData[1] == $boucle) {
			$returnedValue[1] .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
		} else {
			$returnedValue[1] .= '<option value="'.$boucle.'">'.$boucle.'</option>';
		}
	}
	$returnedValue[1] .= '</select>';
	// end of month
	$returnedValue[0] .= '<select name="yearDate" size="1">';
	for ($boucle = 2003; $boucle < 2037; $boucle++) {
		if ($dateData[0] == $boucle) {
			$returnedValue[0] .= '<option value="'.$boucle.'" selected>'.$boucle.'</option>';
		} else {
			$returnedValue[0] .= '<option value="'.$boucle.'">'.$boucle.'</option>';
		}
	}
	$returnedValue[0] .= '</select>';
	if ($notEnglishFormat) {
		$returnedString = $returnedValue[2].$returnedValue[1].$returnedValue[0];
	} else {
		$returnedString = join($returnedValue);
	}
	return $returnedString;
}

/**
 * Display formatted string according to date format choice (txt format)
 *
 * @param string $myDate
 * @param boolean $notEnglishFormat
 * @return string
 */
function showDateTxt($myDate, $notEnglishFormat)
{
	$dateData = explode("-", $myDate); // [0] = YYYY; [1] = MM; [2] = DD
	if ($notEnglishFormat) {
		$returnedString = $dateData[2].'-'.$dateData[1].'-'.$dateData[0];
	} else {
		$returnedString = join($dateData, '-');
	}
	return $returnedString;
}
?>