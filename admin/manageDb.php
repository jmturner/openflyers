<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * manageDb.php
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
 * @version    CVS: $Id: manageDb.php,v 1.1.2.6 2006/09/18 12:46:28 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

//$optimize_query = 'OPTIMIZE TABLE '.$list_database_tables[$boucle];

require_once("PEAR.php");
require_once("./conf/connect.php");
require_once("./classes/db.class.php");
$myResultString = '';

function trnl($string) {
    $strtmp = str_replace("\r", "", $string);
    return $strtmp = str_replace("<br />", "", $strtmp);
    //return str_replace("\n", "\n", $strtmp);
}

function get_table_def($table, $crlf)
{
    global $drop;
    global $database;

    $schema_create = "";
    if(!empty($drop))

    $schema_create .= "DROP TABLE IF EXISTS $table;$crlf";
    $schema_create .= "CREATE TABLE $table ($crlf";
    $result = $database->query("SHOW FIELDS FROM $table");
    while($row = mysql_fetch_array($result)) {
        $schema_create .= " $row[Field] $row[Type]";
        if(isset($row["Default"]) && (!empty($row["Default"]) || $row["Default"] == "0"))
        $schema_create .= " DEFAULT '$row[Default]'";
        if($row["Null"] != "YES")
        $schema_create .= " NOT NULL";
        if($row["Extra"] != "")
        $schema_create .= " $row[Extra]";
        $schema_create .= ",$crlf";
    }
    $schema_create = ereg_replace(",".$crlf."$", "", $schema_create);
    $result = $database->query("SHOW KEYS FROM $table");
    while($row = mysql_fetch_array($result)) {
        $kname=$row['Key_name'];
        if(($kname != "PRIMARY") && ($row['Non_unique'] == 0))
        $kname="UNIQUE|$kname";
        if(!isset($index[$kname]))
        $index[$kname] = array();
        $index[$kname][] = $row['Column_name'];
    }
    while(list($x, $columns) = @each($index)) {
        $schema_create .= ",$crlf";
        if($x == "PRIMARY")
        $schema_create .= " PRIMARY KEY (" . implode($columns, ", ") . ")";
        elseif (substr($x,0,6) == "UNIQUE")
        $schema_create .= " UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
        else
        $schema_create .= " KEY $x (" . implode($columns, ", ") . ")";
    }
    $schema_create .= "$crlf)";

    return (stripslashes($schema_create));
}

function get_table_content($table, $handler)
{
    global $database;

    $result = $database->query("SELECT * FROM $table");
    $i = 0;
    while($row = mysql_fetch_row($result)) {
        $table_list = "(";
        for($j=0; $j<mysql_num_fields($result);$j++)
        $table_list .= mysql_field_name($result,$j).", ";
        $table_list = substr($table_list,0,-2);
        $table_list .= ")";
        if(isset($GLOBALS["showcolumns"])) {
            $schema_insert = "INSERT INTO $table $table_list VALUES (";
        } else {
            $schema_insert = "INSERT INTO $table VALUES (";
        }
        for($j=0; $j<mysql_num_fields($result);$j++) {
            if(!isset($row[$j])) {
                $schema_insert .= " NULL,";
            } elseif($row[$j] != "") {
                $schema_insert .= " '".addslashes($row[$j])."',";
            } else {
                $schema_insert .= " '',";
            }
        }
        $schema_insert = ereg_replace(",$", "", $schema_insert);
        $schema_insert .= ")";
        $handler(trim($schema_insert));
        $i++;
    }
    return (true);
}

function my_handler($sql_insert)
{
    global $crlf, $asfile;

    echo "$sql_insert;$crlf";
}



switch ($ope) {
// Export CSV (s&eacute;parateur ; et retour &agrave; la ligne);
case "csv" :
	header("Content-type: application/octetstream");
	header("Content-disposition: filename=export.csv");
	header("Expires: Mon, 10 Jul 2003 05:00:00 GMT");              // Date du passe;
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // toujours modifie
	$endOfLine = "\n";
	$fileContent = '';
	$fileContent = 'last name; first name; login; email; address; zipcode; city; state; country; homephone; workphone; cellphone;'.$endOfLine;
	$result = $database->query('select LAST_NAME, FIRST_NAME, NAME, EMAIL, ADDRESS, ZIPCODE, CITY, STATE, COUNTRY, HOME_PHONE, WORK_PHONE, CELL_PHONE from authentication ORDER BY LAST_NAME');
	while ($row = mysql_fetch_object($result)) {
		$fileContent .= '"'.stripslashes(unhtmlentities(trim($row->LAST_NAME))).'";"'.stripslashes(unhtmlentities(trim($row->FIRST_NAME))).'";"'.trim($row->NAME).'";"'.trim($row->EMAIL).'";"'.trnl(stripslashes(unhtmlentities(trim($row->ADDRESS)))).'";"'.$row->ZIPCODE.'";"'.stripslashes(unhtmlentities(trim($row->CITY))).'";"'.stripslashes(unhtmlentities(trim($row->COUNTRY))).'";"'.$row->HOME_PHONE.'";"'.$row->WORK_PHONE.'";"'.$row->CELL_PHONE.'";'.$endOfLine;
	}
	print($fileContent);
	break;
case "excel" :
	include_once "Spreadsheet/Excel/Writer.php";
	$xls =& new Spreadsheet_Excel_Writer();
	$xls->send("export.xls");
	$sheet =& $xls->addWorksheet('Test XLS');
	$sheet->write(0,0,'Last Name');
	$sheet->write(0,1,'Name');
	$sheet->write(0,2,"Login");
	$sheet->write(0,3,'e-mail');
	$sheet->write(0,4,'Address');
	$sheet->write(0,5,'Zipcode');
	$sheet->write(0,6,'City');
	$sheet->write(0,7,'State');
	$sheet->write(0,8,'Country');
	$sheet->write(0,9,'Home phone');
	$sheet->write(0,10,'Work phone');
	$sheet->write(0,11,'Cell phone');
	$ini_loop = 1;
	$result = $database->query('select LAST_NAME, FIRST_NAME, NAME, EMAIL, ADDRESS, ZIPCODE, CITY, STATE, COUNTRY, HOME_PHONE, WORK_PHONE, CELL_PHONE from authentication ORDER BY LAST_NAME');
	while ($row = mysql_fetch_object($result)) {
		$sheet->write($ini_loop,0,stripslashes(unhtmlentities(trim($row->LAST_NAME))));
		$sheet->write($ini_loop,1,stripslashes(unhtmlentities(trim($row->FIRST_NAME))));
		$sheet->write($ini_loop,2,trim($row->NAME));
		$sheet->write($ini_loop,3,trim($row->EMAIL));
		$sheet->write($ini_loop,4,stripslashes(unhtmlentities(trim($row->ADDRESS))));
		$sheet->write($ini_loop,5,$row->ZIPCODE);
		$sheet->write($ini_loop,6,stripslashes(unhtmlentities(trim($row->CITY))));
		$sheet->write($ini_loop,7,stripslashes(unhtmlentities(trim($row->STATE))));
		$sheet->write($ini_loop,8,stripslashes(unhtmlentities(trim($row->COUNTRY))));
		$sheet->write($ini_loop,9,$row->HOME_PHONE);
		$sheet->write($ini_loop,10,$row->WORK_PHONE);
		$sheet->write($ini_loop,11,$row->CELL_PHONE);
		$ini_loop++;
	}
	$xls->close();
	break;
case "manage" :
	$myTemplate->assign('ONLOAD','');
	$myTemplate->assign('ADMIN_TITLE',$lang['ADMIN_TITLE']);
    $myTemplate->assign('MANAGE_USER_DELETE_CONFIRM',$lang['MANAGE_USER_DELETE_CONFIRM']);
	$myTemplate->display('template/headers.tpl');
	require_once('./admin/menu.php');
	$myTemplate->display('./template/footers.tpl');
	break;
case "srss" :
	$myTemplate->assign('ONLOAD','');
	$myTemplate->assign('ADMIN_TITLE',$lang['ADMIN_TITLE']);
    $myTemplate->assign('MANAGE_USER_DELETE_CONFIRM',$lang['MANAGE_USER_DELETE_CONFIRM']);
	$myTemplate->display('template/headers.tpl');
	require_once('./admin/menu.php');
	$result = $database->query('DELETE FROM sr_ss');
	$myResultString ='Table des heures de nuit a&eacute;ronautique purg&eacute;e';
	include('./admin/results.content.php');
	$myTemplate->display('./template/footers.tpl');
	break;
case "backup" :
	@set_time_limit(600);
	header("Content-disposition: filename=".BASE.".sql");
	header("Content-type: application/octetstream");
	header("Pragma: no-cache");
	header("Expires: 0");

	$crlf="\n";
	$strTableStructure = "Table structure for table";
	$strDumpingData = "Dumping data for table";
	$tables = mysql_list_tables(BASE);
	$num_tables = @mysql_numrows($tables);
	$i = 0;
	while($i < $num_tables) { 
		$table = mysql_tablename($tables, $i);
		print $crlf;
		print "# --------------------------------------------------------$crlf";
		print "#$crlf";
		print "# $strTableStructure '$table'$crlf";
		print "#$crlf";
		print $crlf;
		echo get_table_def($table, $crlf).";$crlf$crlf";
		print "#$crlf";
		print "# $strDumpingData '$table'$crlf";
		print "#$crlf";
		print $crlf;
		get_table_content($table, "my_handler");
		$i++;
	}
	$database->disconnect();
	break;
}
?>
