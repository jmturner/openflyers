<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * logo.php
 *
 * display club logo
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
 * @category   image
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: logo.php,v 1.16.4.1 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Sat Jul 10 2004
 */

// security constant used by others php files to test if they are called within index.php or not
define('SECURITY_CONST',1);

header('Expires: Mon, 10 Jul 2003 05:00:00 GMT');              // Date du passe;
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // toujours modifie
require_once('../classes/db.class.php');
require_once('../conf/connect.php');
$db_image = new DBAccessor(HOST,BASE,VISITOR,PASSWORD_VISITOR);	
$db_image->connect();
$db_image->query('SELECT LOGO_EXT, LOGO FROM clubs WHERE NUM=1');
$my_picture=$db_image->fetch();
$db_image->disconnect();
/* envoyer des headers pour éviter un reload à chaque accès */
if ($my_picture->LOGO != '')
{
	header('content-type: '.$my_picture->LOGO_EXT);
	echo($my_picture->LOGO);
}
else
{
	$image = imagecreatetruecolor(150,90);
	$color_blue = imagecolorallocate($image, 0xdc, 0xe4, 0xf0);
	$color_black = imagecolorallocate($image, 0, 0, 0);
	imagefill($image, 0, 0, $color_blue);
	imagestring($image, 5, 20, 40, 'LOGO ABSENT', $color_black);
	imagecolortransparent($image, $color_blue);
	header('content-type: image/png');
	imagepng($image);
}
?>