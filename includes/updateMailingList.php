<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * updateMailingList.php
 *
 * change own subscribtion or un-subscription to a mailing list
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
 * @category   mailing list management
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: updateMailingList.php,v 1.3.2.3 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat May 31 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}
require_once('./includes/redirect.php');
require_once('./displayClasses/requestForm.class.php');
// We assume that $userSession, $database and $firstDisplayedDate are well defined

if (($sub_menu==8)or($sub_menu==9))
{
	require_once('./classes/mailing_list.class.php');
	$mail_class=new mailing_list($userSession->db);
	if($sub_menu==8)
	{
		$mail_class->add_email($userSession->getEmail());
	}
	else
	{
		$mail_class->remove_email($userSession->getEmail());
	}
    redirect($userSession,$firstDisplayedDate->getTS());
}
else
{
	require_once('./includes/header.php');
?></head><body><?php
	$request=new requestForm($lang['ERROR_TRANSMIT_DATA']);
	$request->addHidden('menu',$menu);
	$request->close($lang['BACK_BUTTON']);
	require_once('./includes/footer.php');
}
?>
