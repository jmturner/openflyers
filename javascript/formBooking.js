/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * formBooking.js
 *
 * JavaScript functions used by formBooking.php
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
 * @category   javascript
 * @author     Christophe LARATTE <christophe.laratte@openflyers.org>
 * @copyright  2003-2005 The OpenFlyers Group <devteam@openflyers.org>
 * @license    http://www.gnu.org/licenses/gpl.html  GNU-GPL License
 * @version    CVS: $Id: formBooking.js,v 1.8.2.3 2006/03/07 08:38:46 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 08 2003
 */

function saveStartDate(year,month,day)
{
    var current=document.getElementById('values');
    month=format2Digits(month);
    day=format2Digits(day);
    current.tsStartDate.value=year+month+day+"000000";
//    window.CalendarPopup_targetInput=current.tsStartDate;
    var time = getDateFromFormat(current.tsStartDate.value,"yyyyMMddHHmmss");
    if (time==0)
    {
        startCal.currentDate=null;
    }
    else
    {
        startCal.currentDate=new Date(time);
    }
    startCal.refreshCalendar(0);
}

function saveEndDate(year,month,day)
{
	var current=document.getElementById('formPlace');
	month=format2Digits(month);
	day=format2Digits(day);
	current.tsEndDate.value=year+month+day+"000000";
    var time = getDateFromFormat(current.tsEndDate.value,"yyyyMMddHHmmss");
    if (time==0)
    {
        endCal.currentDate=null;
    }
    else
    {
        endCal.currentDate=new Date(time);
    }
    endCal.refreshCalendar(1);
}

function askIdent()
{
	if(typeof(document.getElementById('formId').bookLogin)=="undefined")
	{
		return true;
	}
	else
	{
		window.open("askIdent.php", "ASK_IDENT", "dependent=yes, Height=320, Width=600, location=no, toolbar=no, scrollbar=yes, resizable=yes, directories=no, status=no");
		return false;
	}
}

function no_twice_num()
{
	shorty=document.getElementById('formId');
	if(shorty.instructor.value==shorty.member.value)
	{
		shorty.instructor.value=0;
	}
}

function checkFreezeAircraft()
{
	if(book_allowed_flag)
	{
		shorty=document.getElementById('formId');
		if(shorty.freeze_aircraft.checked)
		{
			shorty.instructor.disabled=true;
			shorty.instructor.style.color="white";
			shorty.member.disabled=true;
			shorty.member.style.color="white";
			shorty.free_seats.disabled=true;
			shorty.free_seats.style.color="white";
			shorty.slot_type.value="2";
		}
		else
		{
			shorty.instructor.disabled=false;
			shorty.instructor.style.color="black";
			shorty.member.disabled=false;
			shorty.member.style.color="black";
			shorty.free_seats.disabled=false;
			shorty.free_seats.style.color="black";
			shorty.slot_type.value="0";
		}
	}
}
