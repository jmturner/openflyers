<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * connect.php
 *
 * Ask database connection informations
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
 * @category   install
 * @author     Soeren MAIRE
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: connect.php,v 1.6.4.2 2005/10/30 10:59:26 claratte Exp $
 * @link       http://openflyers.org
 * @since      Mon Feb 7 2005
 */

require_once('../conf/config.php');
require_once('../lang/'.DEFAULT_LANG.'.php');
?>
<html><head><title><?php echo $lang['INSTALL_CONNECTION_PARAMETERS'];?></title></head>
<body onload="document.getElementById('base').focus();">
<script type="text/javascript">
function validate(f)
{
    var res = true;
    if(f.elements["base"].value.length==0)
    {
        alert("<?php echo $lang['INSTALL_FORM_WARNING'];?>");
        f.elements["base"].focus();
        res =false;
    }
    else if(f.elements["host"].value.length==0)
    {
        alert("<?php echo $lang['INSTALL_FORM_WARNING'];?>");
        f.elements["host"].focus();
        res =false;
    }
    else if(f.elements["user"].value.length==0)
    {
        alert("<?php echo $lang['INSTALL_FORM_WARNING'];?>");
        f.elements["user"].focus();
        res =false;
    }
    else if(f.elements["password"].value.length==0)
    {
        if(!confirm("<?php echo $lang['INSTALL_NO_PASSWORD']; ?>"))
        {
            f.elements["password"].focus();
            res =false;
        }
        else
        {
            //raz password2 au cas où
            f.elements["password2"].value="";
        }
    }
    else if(f.elements["password"].value != f.elements["password2"].value)
    {
        alert("<?php echo $lang['INSTALL_CHECK_PASSWORD']?>");
        f.elements["password"].focus();
        res =false;
    }
    return res;
}
</script>
<h1><?php echo $lang['INSTALL_TITLE'];?></h1>
<div align="center"><?php echo $lang['INSTALL_CONNECTION_PARAMETERS'];?>
<form method="post" action="updateconnect.php" onsubmit="return validate(this);">
<table border="0">
<tr><td><?php echo $lang['INSTALL_DATABASE_NAME']; ?></td><td><input id="base" type="text" name="base"/></td></tr>
<tr><td><?php echo $lang['INSTALL_HOST_NAME']; ?></td><td><input type="text" name="host" value="localhost"/></td></tr>
<tr><td><?php echo $lang['INSTALL_USER_NAME']; ?></td><td><input type="text" name="user" value="root"/></td></tr>
<tr><td><?php echo $lang['INSTALL_USER_PASSWORD']; ?></td><td><input type="password" name="password"/></td></tr>
<tr><td><?php echo $lang['INSTALL_USER_PASSWORD2']; ?></td><td><input type="password" name="password2"/></td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="OK"/></td></tr>
</table>
</form></div>
</body></html>
