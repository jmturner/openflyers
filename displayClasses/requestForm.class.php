<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * requestForm.class.php
 *
 * Build forms
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
 * @category   html engine
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: requestForm.class.php,v 1.7.2.4 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Feb 09 2005
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

class requestForm
{
    var $tagExtension;
    var $id;
   /**
     * Constructor
     *
     * @access public
     * @param $title string displayed at the top of the form box
     * @param $javaFunc string javascript functions
     * @param $tagExtension string css class descriptor extension
     * @param $action string php file name to be called on validation
     * @param $id string form id name
     * @return null
     */
    function requestForm($title='',$javaFunc='',$tagExtension='',$action='index',$id='formId')
    {
        $this->tagExtension=$tagExtension;
        $this->id=$id;
        ?><form id="<?php echo($id);?>" action="<?php echo($action);?>.php" method="post"<?php
        if ($javaFunc!='')
        {
            echo(' '.$javaFunc);
        }
        ?>><div class="centeredForm<?php echo($this->tagExtension);?>"><?php
        if ($title!='')
        {
            $this->addTitle($title);
        }
    }

   /**
     * addTitle
     *
     * @access public
     * @param $title string displayed as a title
     * @return null
     */
    function addTitle($title)
    {
        ?><h1><?php echo($title);?></h1><?php
    }

   /**
     * addBreakForm
     *
     * @access public
     * @param $title string displayed as a title in the new sub-form part
     * @param $tagExtension string css class descriptor extension
     * @return null
     */
    function addBreakForm($title='',$tagExtension='')
    {
        $this->tagExtension=$tagExtension;
        ?><br class="lightSpacer"/></div><div class="centeredForm<?php echo($this->tagExtension);?>"><?php
        if ($title!='')
        {
            $this->addTitle($title);
        }
    }

   /**
     * addHidden
     *
     * @access public
     * @param $name string for the name tag of the hidden element (the variable name)
     * @param $value string value to be set
     * @return null
     */
    function addHidden($name,$value)
    {
        ?><input type="hidden" name="<?php echo($name);?>" value="<?php echo($value);?>"/><?php
    }

   /**
     * addInput
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $password boolean specifying if it's a password input or not
     * @param $size integer specifying input size
     * @param $value string value to be set
     * @return null
     */
    function addInput($title,$name,$password=false,$size=40,$value='')
    {
        $maxlength=$size;
        if ($size>40)
        {
            $size=40;
        }
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>">
        <input id="<?php echo($name);?>" size="<?php echo($size);?>"<?php
        if ($value!='')
        {
            echo(' value="'.$value.'"');
        }
        ?> type="<?php
        if ($password)
        {
            echo('password');
        }
        else
        {
            echo('text');
        }?>" maxlength="<?php echo($maxlength);?>" name="<?php echo($name);?>"/></span></div><?php
    }

   /**
     * addCheckBox
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $value string value to be set
     * @param $checked boolean specifying if it's default checked or not
     * @param $lastSentence string displayed at the right of the checkbox
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if this combo is selectable or not
     * @return null
     */
    function addCheckBox($title,$name,$value='',$checked=false,$lastSentence='',$javaFunc='',$disable=false)
    {
        ?><div class="spacer"><?php echo($title);?>&nbsp;<?php
        $this->addRawCheckBox($name,$value,$checked,$lastSentence,$javaFunc,$disable);
        ?></div><?php
    }

   /**
     * addCheckBoxList
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $list array of array(string name,string value,boolean checked,string label)
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if this combo is selectable or not
     * @return null
     */
    function addCheckBoxList($title,$list,$javaFunc='',$disable=false)
    {
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        foreach ($list as $element)
        {
            $this->addRawCheckBox($element[0],$element[1],$element[2],$element[3],$javaFunc,$disable);
            ?><br /><?php
        }
        ?></span></div><?php
    }

   /**
     * addCheckBox
     *
     * @access private
     * @param $name string for the name tag of the input element (the variable name)
     * @param $value string value to be set
     * @param $checked boolean specifying if it's default checked or not
     * @param $lastSentence string displayed at the right of the checkbox
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if this combo is selectable or not
     * @return null
     */
    function addRawCheckBox($name,$value='',$checked=false,$lastSentence='',$javaFunc='',$disable=false)
    {
        ?><input type="checkbox" name="<?php echo($name);?>"<?php
        if ($value!='')
        {
            ?> value="<?php echo($value);?>"<?php
        }
        if ($checked)
        {
            ?> checked="checked"<?php
        }
        if ($javaFunc!='')
        {
            echo(' '.$javaFunc);
        }
        if ($disable)
        {
            ?> disabled="disabled"<?php
        }
        ?>/>&nbsp;<?php echo($lastSentence);
    }

   /**
     * addRadioBox
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $list array of array(integer num,string label)
     * @param $default integer specifying which have to be shown by default
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if radio boxes are available or not
     * @return null
     */
    function addRadioBox($title,$name,$list,$default='',$javaFunc='',$disable=false)
    {
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        foreach ($list as $element)
        {
            ?><input type="radio" value="<?php echo($element[0]);?>" name="<?php echo($name);?>"<?php
            if ($element[0]==$default)
            {
                ?> checked="checked"<?php
            }
            if ($javaFunc!='')
            {
                echo(' '.$javaFunc);
            }
            if ($disable)
            {
                ?> disabled="disabled"<?php
            }
            ?>/>&nbsp;<?php echo(stripslashes($element[1]));?><br /><?php
        }
        ?></span></div><?php
    }

       /**
     * addButtonList
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $list array of array(integer num,string label)
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if radio boxes are available or not
     * @return null
     */
    function addButtonList($title,$list,$javaFunc='',$disable=false)
    {
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        foreach ($list as $element)
        {
            ?><input type="submit" value="<?php echo($element[1]);?>" onclick="document.getElementById('<?php echo($this->id);?>').sub_menu.value=<?php echo(stripslashes($element[0]));?>; submit();"<?php
            if ($javaFunc!='')
            {
                echo(' '.$javaFunc);
            }
            if ($disable)
            {
                ?> disabled="disabled"<?php
            }
            ?>/>&nbsp;<?php
        }
        ?></span></div><?php
    }

   /**
     * addCombo
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name and id tags of the input element (the variable name)
     * @param $list array of array(integer num,string label)
     * @param $default integer specifying which have to be shown by default
     * @param $javaFunc string javascript functions
     * @return null
     */
	function addCombo($title,$name,$list,$default='',$javaFunc='')
	{
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        $this->addSelect($name,$list,$default,$javaFunc);
        ?></span></div><?php
	}
    
   /**
     * addSelect
     *
     * @access public
     * @param $name string for the name and id tags of the input element (the variable name)
     * @param $list array of array(integer num,string label)
     * @param $default integer specifying which have to be shown by default
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if this combo is selectable or not
     * @return null
     */
	function addSelect($name,$list,$default='',$javaFunc='',$disable=false)
	{
        echo "<select id=\"$name\" name=\"$name\"".($javaFunc!='' ? ' '.$javaFunc:'').(($disable) ? ' disabled="disabled"': '').'>';
        foreach ($list as $element)
        {
            ?><option <?php
            if ($element[0]==$default)
            {
                ?>selected="selected" <?php
            }
            ?>value="<?php echo($element[0]);?>"><?php
            echo(stripslashes($element[1]));
            ?></option><?php
        }
        ?></select><?php
	}
    
   /**
     * addTextArea
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $default string specifying which have to be shown by default
     * @param $javaFunc string javascript functions
     * @return null
     */
    function addTextArea($title,$name,$default='',$javaFunc='')
    {
        ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><textarea id="<?php echo($name);?>" rows="3" cols="30" name="<?php echo($name);?>"<?php
        if ($javaFunc!='')
        {
            echo(' '.$javaFunc);
        }
        ?>><?php echo($default);?></textarea></span></div><?php
    }

   /**
     * addDateTime
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $default string timestamp format date
     * @param $frenchDateDisplay boolean
     * @param $javaFunc string javascript functions
     * @param $disable boolean specifying if this combo is selectable or not
     * @return null
     */
	function addDateTime($title,$name,$default,$frenchDateDisplay=false,$javaFunc='',$disable=false)
	{
        $year=substr($default,0,4);
        $month=substr($default,4,2);
        $day=substr($default,6,2);
        $hour=substr($default,8,2);
        $minute=substr($default,10,2);
	    
	    $dayList=array();
        for ($i=1;$i<=31;$i++)
        {
            $dayList[]=array(sprintf("%'02d",$i),sprintf("%'02d",$i));
        }

        $monthList=array();
        for ($i=1;$i<=12;$i++)
        {
            $monthList[]=array(sprintf("%'02d",$i),sprintf("%'02d",$i));
        }

        $yearList=array();
        for ($i=($year-1);$i<=($year+1);$i++)
        {
            $yearList[]=array($i,sprintf("%'04d",$i));
        }

        $hourList=array();
        for ($i=0;$i<=23;$i++)
        {
            $hourList[]=array(sprintf("%'02d",$i),sprintf("%'02d",$i));
        }

        $minuteList=array();
        for ($i=0;$i<=3;$i++)
        {
            $minuteList[]=array(sprintf("%'02d",$i*15),sprintf("%'02d",$i*15));
        }

	    ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        if ($frenchDateDisplay)
        {
            $this->addSelect($name.'_day',$dayList,$day,$javaFunc,$disable);
            ?>&nbsp;/&nbsp;<?php
            $this->addSelect($name.'_month',$monthList,$month,$javaFunc,$disable);
            ?>&nbsp;/&nbsp;<?php
            $this->addSelect($name.'_year',$yearList,$year,$javaFunc,$disable);
        }
        else
        {
            $this->addSelect($name.'_year',$yearList,$year,$javaFunc,$disable);
            ?>&nbsp;/&nbsp;<?php
            $this->addSelect($name.'_month',$monthList,$month,$javaFunc,$disable);
            ?>&nbsp;/&nbsp;<?php
            $this->addSelect($name.'_day',$dayList,$day,$javaFunc,$disable);
        }
        ?>&nbsp;&nbsp;&nbsp;<?php
        $this->addSelect($name.'_hour',$hourList,$hour,$javaFunc,$disable);
        ?>&nbsp;:&nbsp;<?php
        $this->addSelect($name.'_minute',$minuteList,$minute,$javaFunc,$disable);
        ?></span></div><?php
    }

   /**
     * addDayHour
     *
     * @access public
     * @param $title string displayed at the left of input box
     * @param $name string for the name tag of the input element (the variable name)
     * @param $default string NNSV format time (ie: D,HH:MM)
     * @param $javaFunc string javascript functions
     * @return null
     */
	function addDayHour($title,$name,$default,$javaFunc='')
	{
	    global $lang;
        $weekDays=array($lang['SUNDAY'],$lang['MONDAY'],$lang['TUESDAY'],$lang['WEDNESDAY'],$lang['THURSDAY'],$lang['FRIDAY'],$lang['SATURDAY']);
        $day=substr($default,0,1);
        $hour=substr($default,2,2);
        $minute=substr($default,5,2);
	    
        $dayList=array();
        for ($i=0;$i<=6;$i++)
        {
            $dayList[]=array($i,$weekDays[$i]);
        }

        $hourList=array();
        for ($i=0;$i<=23;$i++)
        {
            $hourList[]=array(sprintf("%'02d",$i),sprintf("%'02d",$i));
        }

        $minuteList=array();
        for ($i=0;$i<=3;$i++)
        {
            $minuteList[]=array(sprintf("%'02d",$i*15),sprintf("%'02d",$i*15));
        }

	    ?><div class="spacer"><span class="label<?php echo($this->tagExtension);?>"><?php echo($title);?>&nbsp;:</span>
        <span class="formw<?php echo($this->tagExtension);?>"><?php
        $this->addSelect($name.'_day',$dayList,$day,$javaFunc);
        ?>&nbsp;&nbsp;&nbsp;<?php
        $this->addSelect($name.'_hour',$hourList,$hour,$javaFunc);
        ?>&nbsp;:&nbsp;<?php
        $this->addSelect($name.'_minute',$minuteList,$minute,$javaFunc);
        ?></span></div><?php
    }

    /**
     * addButton
     *
     * @access public
     * @param $title string displayed in the button
     * @param $name string for the name tag of the input element (the variable name)
     * @param $javaFunc string javascript functions
     * @return null
     */
    function addButton($title,$name,$javaFunc='')
    {
        ?><div class="spacer">
        <input name="<?php echo($name);?>" type="button" value="<?php echo($title);?>"<?php
        if ($javaFunc!='')
        {
            echo(' '.$javaFunc);
        }
        ?>/></div><?php
    }

    /**
     * close
     *
     * @access public
     * @param $title string displayed in the validation box
     * @return null
     */
    function close($title='')
    {
        if ($title!='')
        {
            ?><div class="spacer"><input id="validation" type="submit" value="<?php echo($title);?>"/></div><?php
        }
        ?></div></form><?php
    }
}
?>