<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * About.php
 *
 * Display "About" OpenFlyers informations
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
 * @category   Initialization
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: about.php,v 1.5.2.12 2007/10/03 11:47:26 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2005
 */

// security constant used by others php files to test if they are called within index.php or not
define('SECURITY_CONST',1);

require_once('./conf/config.php');

// set the default include path
if (defined('PEAR_DIRECTORY') and (PEAR_DIRECTORY!='')) {
    ini_set('include_path','.'.PATH_SEPARATOR.PEAR_DIRECTORY);
}

require_once('./lang/'.DEFAULT_LANG.'.php');		// default language file necessary for correct initialisation of class definitions

// Connexion to database with initialisation of $database class
require_once('./conf/connect.php');		// DataBase parameters
require_once('./classes/db.class.php');	// MySQL database connector

// Warning : $database is used as GLOBAL variable everywhere (ie : in functions)
// update : some times $database is passed has argument to object. It should be the right way.
$database=new DBAccessor(HOST,BASE,VISITOR,PASSWORD_VISITOR);

// Serie manager class
require_once('./classes/serie.class.php');

// OF Date class based on PEAR one
require_once('./classes/Date.class.php');

// pool functions
require_once('./pool/functions.php');

// Customized forms
require_once('./displayClasses/requestForm.class.php');

// authentification and session opening with initialisation of $userSession class
require_once('./classes/userSession.class.php');

$userSession=new userSession($database);		// Warning $userSession is used as GLOBAL everywhere
if($userSession->clubLang)
{
    $userSession->openLangFile($userSession->clubLang);
}

require_once('./includes/header.php');
?></head><body>
<div class="clubLogo">
<a href="http://openflyers.org/"><img src="img/biglogo.gif" alt="OpenFlyers"/></a>
</div>
<?php
$request=new requestForm($lang['ABOUT_TITLE'],'','Large');
?>
<ul>
<li><?php echo($lang['ABOUT_WHAT_QUESTION']);?>
<ul>
<li><?php echo($lang['ABOUT_WHAT_ANSWER_1']);?><a href="http://www.gnu.org/copyleft/gpl.html"><?php echo($lang['ABOUT_WHAT_ANSWER_1_2']);?></a>.</li>
<li><?php echo($lang['ABOUT_WHAT_ANSWER_2']);?></li>
</ul></li>
<li><?php echo($lang['ABOUT_WHATFOR_QUESTION']);?>
<ul><li><?php echo($lang['ABOUT_WHATFOR_ANSWER']);?></li></ul>
</li>
<li><?php echo($lang['ABOUT_HOW_QUESTION']);?>
<ul><li><?php echo($lang['ABOUT_HOW_ANSWER_1']);?></li>
<li><?php echo($lang['ABOUT_HOW_ANSWER_2']);?></li>
<li><?php echo($lang['ABOUT_HOW_ANSWER_3']);?></li></ul>
</li>
<li><?php echo($lang['ABOUT_WHO_QUESTION']);?>
<ul>
<li><?php echo($lang['ABOUT_WHO_DEV']);?>Patrice GODARD, Patrick HUBSCHER, Christophe LARATTE, Soeren MAIRE.</li>
<li><?php echo($lang['ABOUT_WHO_DOC']);?>St&eacute;phane CROSES, Jean DE PARDIEU, Denis ROUSSEAUX.</li>
<li><?php echo($lang['ABOUT_ENGLISH_TRANSLATORS']);?>Patrice GODARD, Patrick HUBSCHER, Christophe LARATTE.</li>
<li><?php echo($lang['ABOUT_ITALIANO_TRANSLATORS']);?>Stefano MARCHESIN.</li>
<li><?php echo($lang['ABOUT_BASQUE_TRANSLATORS']);?>Xan ARKONDO.</li>
<li><?php echo($lang['ABOUT_SPANISH_TRANSLATORS']);?>Xavier SERRA.</li>
<li><?php echo($lang['ABOUT_GERMAN_TRANSLATORS']);?>Heinrich NAGEL.</li>
<li><?php echo($lang['ABOUT_WHO_SPEC']);?>Jean DE PARDIEU, Jo&euml;l TREMBLET.</li>
<li><?php echo($lang['ABOUT_WHO_BETA']);?>Kamel BELHOUCHET, Jean BOSSY, bernard CAUX, David DEMAILLY, Jean-Philippe FEVE, Philippe KUHN, Anthony LEBAILLY, Christophe MILIAN, S&eacute;bastien OUSTRIC, Alain PROVOST, Herv&eacute; THEPAUT.</li>
</ul>
</li>
</ul>
<?php
$request->close($lang['ABOUT_BACK']);
?>
<p>
<a href="http://validator.w3.org/check?uri=referer">
<img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0!" height="31" width="88"/>
</a>
<a href="http://jigsaw.w3.org/css-validator/">
<img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!"/>
</a>
</p>
<?php
require_once('./includes/footer.php');
?>