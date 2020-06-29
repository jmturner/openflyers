<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * basicTemplate.class.php
 *
 * administration interface
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
 * @category   administration interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: basicTemplate.class.php,v 1.11.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

class basicTemplate
{
	
	/**
	 * This array contains all data to be displayed
	 *
	 * @var array
	 */
	var $tableOfVar = array();
	/**
	 * Default filepath for templates
	 *
	 * @var string
	 */
	var $filePath = "template/";
	
	/**
	* @return basicTemplate
	* @desc initialisation
	*/
	function basicTemplate()
	{
	}
	
	/**
	* @return void
	* @param string $varReference
	* @param string $varContent
	* @desc assign a value to a TAG included in a template. 
	These values are stored in an array awaiting to be displayed (see display method)
	*/
	function assign($varReference, $varContent)
	{
		$this->tableOfVar[$varReference] = $varContent;
	}
	
	/**
	* @return void
	* @param string $templateFileName
	* @desc Read the template file, replace assigned tag and display the result to screen
	*/
	function display($templateFileName)
	{
		if (file_exists($templateFileName)) {
			$fileContent = file_get_contents($templateFileName);
			if (!$fileContent) {
				die($lang['FILE_READ_ERROR']);
			}
			foreach ($this->tableOfVar as $templateVar => $templateVarContent) {
					$fileContent = str_replace("{".$templateVar."}", $templateVarContent, $fileContent);
				unset($this->tableOfVar[$templateVar]);
			}
			if (get_magic_quotes_runtime()) {
				$fileContent = stripslashes($fileContent);
			}
			print($fileContent);
		}
	}
}
?>