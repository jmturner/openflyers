<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * header.php
 *
 * construct html header
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
 * @version    CVS: $Id: header.php,v 1.44.2.4 2006/07/01 06:32:59 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sun Jan 5 2003
 */

// security constant test to know if we are called within index.php or not
if(!defined('SECURITY_CONST'))
{
    die('This file should be required from index.php'); // we stop the script now
}

if (!isset($title))
{
	$title='OpenFlyers';
}
echo('<?xml version="1.0" encoding="iso-8859-1"?>');
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo($title);?></title>
<script type="text/javascript">
if (window!=top)
{
    top.location=window.location;
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<?php
if (isset($userSession)) // we do not try to refresh if we are not connected
{
    $now=new ofDate();
    if ( (!$userSession->isNothingAllowed()) // if we are not a visitor, we always refresh to logout
         or (!$userSession->parameter->isNoVisitorRefresh()) // we are a visitor but the NoVisitorRefresh is not set, so we refresh
         or ( // if we are here, so we are a visitor and the noVisitorRefresh parameter is set
            isset($menu)
            and (
                ($menu!=0) // but we refresh anywhere if we are not on the main page
                or (
                   (isset($firstDisplayedDate))
                   and (!$firstDisplayedDate->isSameDay($now))
                   ) // at least we refresh if we are not on the current day
                )
            )
       )
    {
        // we do not refresh if we are on DEBUG mode or that this window is a legend Popup (CLOSE_WINDOW)
        if (!defined('CLOSE_WINDOW')and(OF_DEBUG!='on'))
        {
            ?><meta http-equiv="refresh" content="<?php echo(USER_SESSION_MAX_TIME);?>; URL=index.php<?php
            if(isset($userSession)and(!$userSession->isNothingAllowed())and(!$userSession->isNoAutoLogout()))
            {
                ?>?menu=6<?php
            }?>"/><?php
        }
    }
}?>
<link rel="stylesheet" type="text/css" media="screen" href="stylesheet.css"/>
<link rel="stylesheet" type="text/css" media="print" href="print.css"/>
<link rel="stylesheet" type="text/css" href="menu.css"/>
<?php
if (!defined('NO_MENU.JS'))
{
    ?><script type="text/javascript" src="javascript/menu.js"></script><?php
}
?>
