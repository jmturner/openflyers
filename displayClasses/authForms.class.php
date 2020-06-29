<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * authForms.class.php
 *
 * Build forms to ask ident, further informations or explain why connexion is refused
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
 * @version    CVS: $Id: authForms.class.php,v 1.18.2.4 2006/06/12 12:22:16 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Dec 06 2004
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

require_once('./classes/userSession.class.php');
require_once('./displayClasses/requestForm.class.php');
require_once('./displayClasses/security.class.php');

class authForms extends userSession
{
    /**
     * ask
     * build form asking identification
     *
     * @access public
     * @param $message string
     * @param $expireDate ofDate
     * @return null
     */
    function ask($langMessage)
	{
	    global $lang;
	    if($this->clubLang)
	    {
	        $this->openLangFile($this->clubLang);
	    }

        $this->addHeaderAdminScript('login');
        $this->displayLogo();
        $security=new security($this->db);
        $security->check();
        
        if (defined('WEBMASTER_FILE') and (WEBMASTER_FILE != null)) {
            require_once(WEBMASTER_FILE);
            $result = webmasterAlert();
            if ($result) {
                echo $result;
            }
        }

        // Form asking login and password
        $request=new requestForm($lang[$langMessage]);
        $request->addInput($lang['ASK_LOGIN'],'login');
        $request->addInput($lang['ASK_PWD'],'password',true);
        $request->close($lang['VALIDATE']);

        $this->displayAdminMail();
        
        ?><p class="annotation"><?php echo($lang['USERSESSION_COPYRIGHT']);?> Patrice GODARD, Patrick HUBSCHER, Christophe LARATTE, Soeren MAIRE<br />
        <?php echo($lang['USERSESSION_DOC_COPYRIGHT']);?> Stéphane CROSES<br />
        <?php echo($lang['USERSESSION_SUP_COPYRIGHT']);?> Jean DE PARDIEU, Jo&euml;l TREMBLET<br />
        <?php echo($lang['USERSESSION_TM']);?><br />
        <a href="http://www.gnu.org/copyleft/gpl.html"><?php echo($lang['USERSESSION_GPL']);?></a></p><?php
        require_once('./includes/footer.php');
	}

	function addHeader($focusName)
	{
	    define('NO_MENU.JS',1);
        require_once('./includes/header.php');
        ?></head><body onload="document.getElementById('<?php echo($focusName);?>').focus();"><?php
	}
	
	function addHeaderAdminScript($focusName)
	{
	    define('NO_MENU.JS',1);
	    require_once('./includes/header.php');
        if ($this->adminMail!='')
        {
            list($firstPart,$secondPart)=explode('@',$this->adminMail,2);
            ?><script type="text/javascript">
            function setAdminMail()
            {
                document.getElementById("adminMail").setAttribute("action","mailto:<?php echo($firstPart);?>"+document.getElementById("aro").value+"<?php echo($secondPart);?>");
            }
            </script></head><body onload="setAdminMail(); document.getElementById('<?php echo($focusName);?>').focus();"><?php
        }
        else 
        {
            ?></head><body onload="document.getElementById('<?php echo($focusName);?>').focus();"><?php
	    }
	}

    function displayLogo()
    {
        global $lang;
        // Left align OF logo
        if ($this->clubIsLogo)
        {
            ?><div class="ofLogo"><a href="http://openflyers.org/"><img src="img/logo.gif" alt="OpenFlyers"/></a><h1><a href="http://openflyers.org/">http://openflyers.org/<br /><?php
            echo($lang['RELEASE'].'&nbsp;'.OF_RELEASE);
            ?></a></h1></div><?php
        }

        // Right align online help button
        ?><div><a class="helpButton" href="about.php"><?php echo($lang['USERSESSION_ABOUT']);?></a>
          <a class="helpButton" href="<?php echo(OF_BTS);?>"><?php echo($lang['USERSESSION_REPORT_BUG']);?></a>
          <a class="helpButton" href="<?php echo(OF_HELP);?>"><?php echo($lang['USERSESSION_HELP']);?></a>
          <br class="spacer"/></div><?php

        // Centered club logo
        ?><div class="clubLogo"><?php
        if ($this->clubIsLogo)
        {
            if($this->clubUrl!='')
            {
                ?><a href="<?php echo($this->clubUrl);?>"><?php
            }
            ?><img src="img/logo.php" alt="<?php echo(stripslashes($this->clubName));?>"/><?php
            if($this->clubUrl!='')
            {
                ?></a><?php
            }
            ?><br /><?php
            if($this->clubUrl!='')
            {
                ?><a href="<?php echo($this->clubUrl);?>"><?php
            }
            echo(stripslashes($this->clubName));
            if($this->clubUrl!='')
            {
                ?></a><?php
            }
        }
        else 
        {
	        ?><a href="http://openflyers.org/"><img src="img/biglogo.gif" alt="OpenFlyers"/></a>
	        <br /><a href="http://openflyers.org/"><?php echo('OpenFlyers '.$lang['RELEASE'].'&nbsp;'.OF_RELEASE);?></a><?php
        }
        ?></div><?php
    }
	
	function displayAdminMail()
    {
        global $lang;

        if ($this->adminMail!='')
        {
            ?><form id="adminMail" action="http://openflyers.org/"><div>
            <input name="aro" type="hidden" id="aro" value="@"/></div></form>
            <p class="annotation"><?php echo($lang['USERSESSION_CONTACT_ADMIN'].'&nbsp;:&nbsp;');?>
            <a href="javascript:document.getElementById('adminMail').submit();">
            <?php echo($this->adminName);?></a></p><?php
        }
    }
	
	function chooseProfile($profiles,$langMessage)
    {
        global $lang;

        $this->addHeader('profile');
        $this->displayLogo();
        $request=new requestForm($lang[$langMessage]);
        $request->addCombo($lang['PROFILE'],'profile',$profiles);
        $request->close($lang['VALIDATE']);
        require_once('./includes/footer.php');
    }
	
	function outdateWarning($langMessage,$messageRest='')
	{
        global $lang;

        $this->addHeader('validation');
        $this->displayLogo();
        $request=new requestForm($lang[$langMessage].$messageRest);
        $request->close($lang['OK']);
        require_once ('./includes/footer.php');
	}
	
	function bannedForm($delay)
	{
        global $lang;

        $this->addHeaderAdminScript('validation');
        $this->displayLogo();
        $request=new requestForm($lang['USERSESSION_TOO_MUCH_BAD_LOGIN'].$delay->getClock());
        $request->close($lang['OK']);
        $this->displayAdminMail();
        require_once ('./includes/footer.php');
	}
	
   /**
     * authenticate user
     *
     * @access public
     * @param $login string
     * @param $password string
     * @param $menu integer used to choose session max time type
     * @return null
     */
    function authenticate($login,$password,$menu=0)
    {
        global $lang;

        $resultArray=$this->kernelAuth($login,$password,$menu);
        switch ($resultArray[0])
        {
            case 0:
                return true;
                break;
            case 1:
                $this->chooseProfile($resultArray[1],'USERSESSION_ASK_PROFILE');
                break;
            case 2:
                if (isset($resultArray[1]))
                {
                    $this->chooseProfile($resultArray[1],'USERSESSION_OUT_SUBSCRIPTION_WARNING');
                }
                else
                {
                    $this->outdateWarning('USERSESSION_OUT_SUBSCRIPTION_WARNING');
                }
                break;
            case 3:
                $this->outdateWarning('USERSESSION_OUT_SUBSCRIPTION',$resultArray[1]);
                break;
            case 4:
                $this->ask('USERSESSION_RAW_OUT_SUBSCRIPTION');
                break;
            case 5:
                $this->ask('BAD_LOGIN');
                break;
            case 6:     // banned connexion
                $this->bannedForm($resultArray[1]);
                break;
            case 7:     // no login
                $this->ask('USERSESSION_ASK_IDENT');
                break;
        }
        return false;
    }

   /**
     * checkQualif
     *
     * @access public
     * @return null
     */
    function checkQualif()
    {
        global $lang;

        // we test if we have already checked qualifications
        if ($this->isQualifChecked())
        {
            return (true);
        }
        $this->setQualifChecked();
        
        // we test if we have to really check qualifications
        if (!$this->parameter->isUseQualif())
        {
            return (true);
        }

        // we check qualifications and if some are outdated or about to be, we display a warning
        $qualifChecker=new qualifChecker($this->db,$this->getTimeZone(),$this->isFrenchDateDisplay());
        $qualifAlertList=$qualifChecker->nearLTList($this->getAuthNum(),$this->getQualifAlertDelay());
        if (sizeof($qualifAlertList)==0)
        {
            return (true);
        }

        $this->addHeader('validation');
        $this->displayLogo();
        $request=new requestForm($lang['WARNING'].'&nbsp;!');
        foreach ($qualifAlertList as $key => $string)
        {
            $request->addCheckBox($string,'noalert['.$key.']',1,false,$lang['USERSESSION_NOT_REMIND']);
        }
        $request->close($lang['VALIDATE']);
        require_once('./includes/footer.php');
        return (false);
    }
}
?>