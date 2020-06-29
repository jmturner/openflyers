/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * formInstructorsRests.js
 *
 * JavaScript functions used by form_instructors_rest.php
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
 * @version    CVS: $Id: formInstructorsRests.js,v 1.4.4.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 08 2003
 */

function checkExceptionnalSlot()
{
	shorty=document.getElementById('formId');
	shorty.regular_start_day.disabled=shorty.exceptionnalSlot.checked;
	shorty.regular_start_hour.disabled=shorty.exceptionnalSlot.checked;
	shorty.regular_start_minute.disabled=shorty.exceptionnalSlot.checked;
	shorty.regular_end_day.disabled=shorty.exceptionnalSlot.checked;
	shorty.regular_end_hour.disabled=shorty.exceptionnalSlot.checked;
	shorty.regular_end_minute.disabled=shorty.exceptionnalSlot.checked;
	shorty.regSameDay.disabled=shorty.exceptionnalSlot.checked;
	shorty.start_year.disabled=!shorty.exceptionnalSlot.checked;
	shorty.start_month.disabled=!shorty.exceptionnalSlot.checked;
	shorty.start_day.disabled=!shorty.exceptionnalSlot.checked;
	shorty.start_hour.disabled=!shorty.exceptionnalSlot.checked;
	shorty.start_minute.disabled=!shorty.exceptionnalSlot.checked;
	shorty.end_year.disabled=!shorty.exceptionnalSlot.checked;
	shorty.end_month.disabled=!shorty.exceptionnalSlot.checked;
	shorty.end_day.disabled=!shorty.exceptionnalSlot.checked;
	shorty.end_hour.disabled=!shorty.exceptionnalSlot.checked;
	shorty.end_minute.disabled=!shorty.exceptionnalSlot.checked;
	shorty.sameDay.disabled=!shorty.exceptionnalSlot.checked;
	shorty.presence[0].disabled=!shorty.exceptionnalSlot.checked;
	shorty.presence[1].disabled=!shorty.exceptionnalSlot.checked;
	if(shorty.exceptionnalSlot.checked)
	{
		color1="white";
		color2="black";
	}
	else
	{
		color1="black";
		color2="white";
	}
	shorty.regular_start_day.style.color=color1;
	shorty.regular_start_hour.style.color=color1;
	shorty.regular_start_minute.style.color=color1;
	shorty.regular_end_day.style.color=color1;
	shorty.regular_end_hour.style.color=color1;
	shorty.regular_end_minute.style.color=color1;
	shorty.start_year.style.color=color2;
	shorty.start_month.style.color=color2;
	shorty.start_day.style.color=color2;
	shorty.start_hour.style.color=color2;
	shorty.start_minute.style.color=color2;
	shorty.end_year.style.color=color2;
	shorty.end_month.style.color=color2;
	shorty.end_day.style.color=color2;
	shorty.end_hour.style.color=color2;
	shorty.end_minute.style.color=color2;
}

function check_regular_start_day()
{
	shorty=document.getElementById('formId');
	if(shorty.regular_start_hour.value==(first_hour/60))
	{
		shorty.regular_start_minute.value=format2Digits(Math.max((first_hour%60),shorty.regular_start_minute.value));
	}
	else if(((eval(shorty.regular_start_hour.value)*60)+eval(shorty.regular_start_minute.value))>(last_hour-min_slot_range))
	{
		shorty.regular_start_hour.value=format2Digits(Math.floor((last_hour-min_slot_range)/60));
		shorty.regular_start_minute.value=format2Digits((last_hour-min_slot_range)%60);
	}
	if(shorty.regSameDay.checked)
	{
		shorty.regular_end_day.value=shorty.regular_start_day.value;
	}
	start_in_utc=(eval(shorty.regular_start_hour.value)*60)+eval(shorty.regular_start_minute.value)+min_slot_range;
	end_in_utc=(eval(shorty.regular_end_hour.value)*60)+eval(shorty.regular_end_minute.value);
	if((start_in_utc>=end_in_utc)&&(shorty.regular_start_day.value==shorty.regular_end_day.value))
	{
		if(eval(shorty.regular_start_minute.value)<min_slot_range)
		{
			shorty.regular_end_hour.value=shorty.regular_start_hour.value;
			shorty.regular_end_minute.value=format2Digits(eval(shorty.regular_start_minute.value)+min_slot_range);
		}
		else
		{
			shorty.regular_end_hour.value=format2Digits(eval(shorty.regular_start_hour.value)+1);
			shorty.regular_end_minute.value=format2Digits(eval(shorty.regular_start_minute.value)-min_slot_range);
		}
	}
}

<!-- Check end_date combo adjust if necessary and then adjust start_date combo -->
function check_regular_end_day()
{
	shorty=document.getElementById('formId');
	if(shorty.regular_end_hour.value==Math.floor(last_hour/60))
	{
		shorty.regular_end_minute.value=format2Digits(Math.min((last_hour%60),shorty.regular_end_minute.value));
	}
	else if(((eval(shorty.regular_end_hour.value)*60)+eval(shorty.regular_end_minute.value))<(first_hour+min_slot_range))
	{
		shorty.regular_end_hour.value=format2Digits(Math.floor((first_hour+min_slot_range)/60));
		shorty.regular_end_minute.value=format2Digits((first_hour+min_slot_range)%60);
	}
	if(shorty.regSameDay.checked)
	{
		shorty.regular_start_day.value=shorty.regular_end_day.value;
	}
	start_in_utc=(eval(shorty.regular_start_hour.value)*60)+eval(shorty.regular_start_minute.value);
	end_in_utc=(eval(shorty.regular_end_hour.value)*60)+eval(shorty.regular_end_minute.value)-min_slot_range;
	if((start_in_utc>=end_in_utc)&&(shorty.regular_start_day.value==shorty.regular_end_day.value))
	{
		if(eval(shorty.regular_end_minute.value)>=min_slot_range)
		{
			shorty.regular_start_hour.value=shorty.regular_end_hour.value;
			shorty.regular_start_minute.value=format2Digits(eval(shorty.regular_end_minute.value)-min_slot_range);
		}
		else
		{
			shorty.regular_start_hour.value=format2Digits(eval(shorty.regular_end_hour.value)-1);
			shorty.regular_start_minute.value=format2Digits(eval(shorty.regular_end_minute.value)+min_slot_range);
		}
	}
}
