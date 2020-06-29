<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manage.php
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
 * @category   Admin interface
 * @author     Patrick HUSBCHER <chakram@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: manage.php,v 1.1.2.6 2007/03/19 16:35:41 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

$currentConfig = new APIconfig($database);
$parameter=$userSession->parameter;
if ((($ope=="add") ||  ($ope=="modify")) && $type=="club") { 
	$myTemplate->assign('ONLOAD',' onload="is_new_icao()"');
} else {
	$myTemplate->assign('ONLOAD','');
}
$myTemplate->assign('ADMIN_TITLE',$lang['ADMIN_TITLE']);
$myTemplate->assign('MANAGE_USER_DELETE_CONFIRM',$lang['MANAGE_USER_DELETE_CONFIRM']);
$myTemplate->display('template/headers.tpl');
require_once('./admin/menu.php');
switch ($ope) {
case "fee" :
	if (!isset($_POST['sub_update']))
	{
		switch ($_POST['fee_mode']) {
			case "off" :
				$currentConfig->disableSubscription();
				break;
			case "restricted" :
			 	if ($currentConfig->subscription_enabled) { 
					$currentConfig->switchToRequired();
					$currentConfig->updateSubscriptionConfig();
				} else {
				$currentConfig->enableSubscription(true);
					}
				break;
			case "warning" :
				if ($currentConfig->subscription_enabled) { 
					$currentConfig->switchToWarning();
					$currentConfig->updateSubscriptionConfig();
				} else {
					$currentConfig->enableSubscription(false);
				}
				break;
		}
	} else {
		$currentConfig->updateSubscriptionConfig();
	}
	$currentConfig->getSubscriptionConfig();
	break;
case "licence" :
	switch ($_POST['switchto']) {
		case "off" :
			$currentConfig->disableQualif();
			break;
		case "restricted" :
			$currentConfig->enableQualif(true);
			break;
		case "warning" :
			$currentConfig->enableQualif(false);
			break;
	}
	$currentConfig->getQualifConfig();
	break;
case "bookInstructionMinTime" :
    $BIMT_flag  = define_variable('BIMT_flag',0);
    $BIMT_value = define_variable('BIMT_value',0);
    if (fmod($BIMT_value,15)==0) {
        $parameter->setBookInstructionMinTime($BIMT_flag, $BIMT_value);
    }
	break;
case "noVisitRefresh" :
    $parameter->setNoVisitorRefresh(define_variable('NVR_flag',0));
	break;
case "bookDateLimitation" :
    $parameter->setBookDateLimitation(define_variable('BDateL_flag',0), intval(define_variable('BDateL_value',0)));
	break;
case "bookDurationLimitation" :
    $parameter->setBookDurationLimitation(define_variable('BDL_flag',0), intval(define_variable('BDL_value',0)));
	break;
case "no_callsign_display" :
    $parameter->setNoCallsignDisplay(define_variable('noCallsignDisplay',0));
	break;
case "book_allocating_rule" :
    $parameter->setBookAllocatorRule(define_variable('BAllocating_flag',0), define_variable('BAllocating_flag',0));
	break;
}


include('./admin/manage.content.php');
$myTemplate->display('./template/footers.tpl');
?>
