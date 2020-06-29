/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * navigation.js
 *
 * JavaScript functions used by navigation.php
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
 * @version    CVS: $Id: navigation.js,v 1.21.2.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 08 2003
 */

function submitDate(year,month,day)
{
	var current=document.getElementById('values');
    current.menu.value=return_menu;
	current.sub_menu.value=return_sub_menu;
	month=format2Digits(month);
	day=format2Digits(day);
	current.tsStartDate.value=year+month+day+"000000";
	current.submit();
}

function fill()
{
    var regPattern=/'(\w)',(\d+),(\d+),(\d+)/;
    var original=document.getElementById("fillMarker").firstChild;
    var tds=document.getElementsByTagName("td");
    var node=tds[0];
    for(var i=1; i<tds.length; i++)
    {
        node=tds[i];
        if (!node.hasChildNodes())
        {
            var copy=original.cloneNode(true);
            node.appendChild(copy);
            var colspan=node.getAttribute("colspan");
            var className=node.getAttribute("class");
            if (!className)
            {
                className=node.getAttribute("className");
            }
            var onclick=node.getAttribute("onclick");

            if ((colspan>1)&&((className=="d")||(className=="t")||(className=="n"))&&(browser.isSafari==false)&&(browser.isKonqueror==false))
            {
                var onclickType=typeof onclick;
                var splitArray=regPattern.exec(onclick.toString());
                var number=splitArray[3];
                node.removeAttribute("colspan");
                node.colSpan="1";
                for(var j=1; j<colspan; j++)
                {
                    var tempNum=number-colspan+j;
                    var nodeCopy=node.cloneNode(true);
                    var newclick="b('"+splitArray[1]+"',"+splitArray[2]+","+tempNum+','+splitArray[4]+")";
                    if (onclickType=="string")
                    {
                        var func=newclick;
                    }
                    else
                    {
                        var func=new Function(newclick);
                    }
                    nodeCopy.setAttribute("onclick",func);
                    node.parentNode.insertBefore(nodeCopy,node);
                }
            }
        }
    }
}

// display the OverLib Popup (tag that describes the slot)
function d(text,startText,endText,title,comments)
{
    if(text!='')
    {
        text=text+"<br />";
    }
	text=text+startText+"<br />"+endText;
	if(typeof(comments)!="undefined")
	{
		text=text+"<br />"+comments;
	}
	overlib(text,CAPTION,title,RIGHT);
}

// b like book
function b(type,num,hour,day)
{
	var current=document.getElementById('values');
	if(type=='A')
	{
		current.aircraft.value=num;
	}
	if(type=='I')
	{
		current.instructor.value=num;
	}
	current.offset_hour.value=hour;
	current.offset_day.value=day;
	current.sub_menu.value='1';
	current.submit();
}

function modify(i)
{
	var current=document.getElementById('values');
	current.bookId.value=i;
	current.sub_menu.value='2';
	current.submit();
}

function previousDays(delta)
{
	current=document.getElementById('values').tsStartDate.value;
	year=eval(current.substr(0,4));
	month=eval(current.substr(4,2));
	day=eval(current.substr(6,2));
	delta=eval(delta);
	var daysinmonth=new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	if(((year%4==0)&&(year%100!=0))||(year%400==0))
	{
		daysinmonth[2]=29;
	}
	day=day-delta;
	if(day<1)
	{
		month=month-1;
		if(month==0)
		{
			month=12;
			year=year-1;
		}
		day=daysinmonth[month]+day;
	}
	submitDate(year,month,day);
}

function nextDays(delta)
{
	current=document.getElementById('values').tsStartDate.value;
	year=eval(current.substr(0,4));
	month=eval(current.substr(4,2));
	day=eval(current.substr(6,2));
	var daysinmonth=new Array(0,31,28,31,30,31,30,31,31,30,31,30,31);
	if(((year%4==0)&&(year%100!=0))||(year%400==0))
	{
		daysinmonth[2]=29;
	}
	day=day+delta;
	if(day>daysinmonth[month])
	{
		day=day-daysinmonth[month];
		month=month+1;
		if(month==13)
		{
			month=1;
			year=year+1;
		}
	}
	submitDate(year,month,day);
}

// Display the clock at the div location where id="clock"
function displayClock()
{
    var now = new Date();
    var hour=now.getHours();
    var minute=now.getMinutes();
    var second=now.getSeconds();

    if (second<10)
    {
        second="0"+second;
    }
    if (minute<10)
    {
        minute="0"+minute;
    }
    if (hour<10)
    {
        hour="0"+hour;
    }

    var clock=hour+":"+minute+":"+second;
    document.getElementById("clock").innerHTML=clock;
    setTimeout("displayClock()",1000);
}
