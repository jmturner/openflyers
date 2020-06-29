/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * checkDates.js
 *
 * JavaScript functions used by formBooking.php and formInstructorsRest.php
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
 * @version    CVS: $Id: checkDates.js,v 1.5.4.3 2006/05/21 09:41:18 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 08 2003
 */

var first_hour=0;
var last_hour=1440;

// Check start_date combo adjust if necessary and then adjust end_date combo
function checkStartDate()
{
	shorty=document.getElementById('formId');
    var min_range = min_slot_range;
    if ((typeof(min_instr_slot_range)!="undefined")&&(typeof(shorty.instructor)!="undefined")) {
        if (shorty.instructor.value!=0) {
            min_range = min_instr_slot_range;
        }
    }
	if (shorty.start_hour.value==Math.floor(first_hour/60)) {
		shorty.start_minute.value=format2Digits(Math.max((first_hour%60),shorty.start_minute.value));
	}
	else if(((eval(shorty.start_hour.value)*60)+eval(shorty.start_minute.value))>(last_hour-min_range)) {
		shorty.start_hour.value=format2Digits(Math.floor((last_hour-min_range)/60));
		shorty.start_minute.value=format2Digits((last_hour-min_range)%60);
	}
	local_date=new Date(shorty.start_year.value,(shorty.start_month.value-1),shorty.start_day.value);
	shorty.start_month.value=format2Digits(local_date.getMonth()+1);
	shorty.start_day.value=format2Digits(local_date.getDate());
	if(shorty.sameDay.checked)
	{
		shorty.end_year.value=shorty.start_year.value;
		shorty.end_month.value=shorty.start_month.value;
		shorty.end_day.value=shorty.start_day.value;
	}
    start_in_utc = new Date(shorty.start_year.value,shorty.start_month.value-1,shorty.start_day.value,shorty.start_hour.value,shorty.start_minute.value);
    start_in_utc = new Date(start_in_utc.valueOf()+(min_range*60*1000));
    end_in_utc   = new Date(shorty.end_year.value,shorty.end_month.value-1,shorty.end_day.value,shorty.end_hour.value,shorty.end_minute.value);
	if(start_in_utc>end_in_utc) {
        shorty.end_year.value   = start_in_utc.getYear();
        shorty.end_month.value  = format2Digits(start_in_utc.getMonth()+1);
        shorty.end_day.value    = format2Digits(start_in_utc.getDate());
        shorty.end_hour.value   = format2Digits(start_in_utc.getHours());
        shorty.end_minute.value = format2Digits(start_in_utc.getMinutes());
	}
	if(freeze_flag)
	{
	checkFreezeAircraft();
	}
}

// Check end_date combo adjust if necessary and then adjust start_date combo
function checkEndDate()
{
	shorty=document.getElementById('formId');
    var min_range = min_slot_range;
    if((typeof(min_instr_slot_range)!="undefined")&&(typeof(shorty.instructor)!="undefined")) {
        if (shorty.instructor.value!=0) {
            min_range = min_instr_slot_range;
        }
    }
	if(shorty.end_hour.value==(last_hour/60))
	{
		shorty.end_minute.value=format2Digits(Math.min((last_hour%60),shorty.end_minute.value));
	}
	else if(((eval(shorty.end_hour.value)*60)+eval(shorty.end_minute.value))<(first_hour+min_range))
	{
		shorty.end_hour.value=format2Digits(floor((first_hour+min_range)/60));
		shorty.end_minute.value=format2Digits((first_hour+min_range)%60);
	}
	local_date=new Date(shorty.end_year.value,(shorty.end_month.value-1),shorty.end_day.value);
	shorty.end_month.value=format2Digits(local_date.getMonth()+1);
	shorty.end_day.value=format2Digits(local_date.getDate());
	if(shorty.sameDay.checked)
	{
		shorty.start_year.value=shorty.end_year.value;
		shorty.start_month.value=shorty.end_month.value;
		shorty.start_day.value=shorty.end_day.value;
	}
    start_in_utc = new Date(shorty.start_year.value,shorty.start_month.value-1,shorty.start_day.value,shorty.start_hour.value,shorty.start_minute.value);
    end_in_utc   = new Date(shorty.end_year.value,shorty.end_month.value-1,shorty.end_day.value,shorty.end_hour.value,shorty.end_minute.value);
    end_in_utc   = new Date(end_in_utc.valueOf()-(min_range*60*1000));
	if(start_in_utc>end_in_utc) {
        shorty.start_year.value   = end_in_utc.getYear();
        shorty.start_month.value  = format2Digits(end_in_utc.getMonth()+1);
        shorty.start_day.value    = format2Digits(end_in_utc.getDate());
        shorty.start_hour.value   = format2Digits(end_in_utc.getHours());
        shorty.start_minute.value = format2Digits(end_in_utc.getMinutes());
	}
}
