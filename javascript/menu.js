/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * menu.js
 *
 * JavaScript functions used by menu.php
 * include iubito's menu javascript functions
 * (not concerned by this license and this header)
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
 * @version    CVS: $Id: menu.js,v 1.15.4.2 2005/10/28 17:44:06 claratte Exp $
 * @link       http://openflyers.org
 * @since      Wed Oct 08 2003
 */

function submit_menu(menu,sub_menu,tsStartDate)
{
    var current=document.getElementById('values');
	current.menu.value=menu;
	current.sub_menu.value=sub_menu;
	if(arguments.length==3)
	{
		current.tsStartDate.value=tsStartDate;
	}
	current.submit();
}

function nl2br(a)
{
//	return a.replace(/\n/g,"<br />");
}

// iubito's menu - http://iubito.free.fr/prog/menu.php - configuration du javascript

var vertical = false;
var nbmenu = 3;
var centrer_menu = false;
var largeur_menu = 95;
var hauteur_menu = 25;
var largeur_sous_menu = 150;
var largeur_auto_ssmenu = false;
var espace_entre_menus = 1;
var top_menu = 1;
var top_ssmenu = top_menu + 19;
var left_menu = 0;
var left_ssmenu = largeur_menu+2;
var delai = 500;
var marge_en_haut_de_page = "1em";
var marge_a_gauche_de_la_page = largeur_menu + 5;
var suivre_le_scroll=true;
var cacher_les_select=true;
var timeout;
var agt = navigator.userAgent.toLowerCase();
var isMac = (agt.indexOf('mac') != -1);
var isOpera = (agt.indexOf("opera") != -1);
var IEver = parseInt(agt.substring(agt.indexOf('msie ') + 5));
var isIE = ((agt.indexOf('msie')!=-1 && !isOpera && (agt.indexOf('webtv')==-1)) && !isMac);
var isIE5win = (isIE && IEver == 5);
var isIE5mac = ((agt.indexOf("msie") != -1) && isMac);
var blnOk=true;
var reg = new RegExp("px", "g");
window.onscroll = function()
{
	if (blnOk && suivre_le_scroll && (isIE || isIE5mac))
	{
		if (isIE5mac) document.getElementById("conteneurmenu").style.visibility="hidden";
		var cumul=0;
		for(i=1;i<=nbmenu;i++)
		{
			if (!vertical) {
				document.getElementById("menu"+i).style.top = document.body.scrollTop + top_menu + "px";
				if (document.getElementById("ssmenu"+i))//undefined
					document.getElementById("ssmenu"+i).style.top = document.body.scrollTop + top_ssmenu + "px";
			} else {
				document.getElementById("menu"+i).style.top = document.body.scrollTop
							+(((i-1)*espace_entre_menus)+cumul+1+top_menu)+"px";
				if (document.getElementById("ssmenu"+i))//undefined
					document.getElementById("ssmenu"+i).style.top = document.body.scrollTop
							+(((i-1)*espace_entre_menus)+cumul+1+top_menu)+"px";
				cumul += isFinite(hauteur_menu)?hauteur_menu:hauteur_menu[i-1];
			}
		}
		if (isIE5mac) document.getElementById("conteneurmenu").style.visibility="visible";
	}
}

function preChargement()
{
	if (document.getElementById("conteneurmenu"))
	{
		document.getElementById("conteneurmenu").style.visibility="hidden";
		//IE5 mac a un bug : quand un texte est dans un élément de style float, il n'apparait pas.
		/*if (isIE5mac)
		{
			document.getElementById("conteneurmenu").style="";
		}*/
	}
}

function Chargement() {
	if (!blnOk) {
		if(document.body.style.backgroundColor!="") { blnOk=false; }
		if(document.body.style.color!="") { blnOk=false; }
		if(document.body.style.marginTop!="") { blnOk=false; }
		if(document.getElementById) {
			with(document.getElementById("conteneurmenu").style) {
				if(position!="" || top!="" || left!=""
						|| width!="" || height!="" || zIndex!=""
						|| margin!="" || visibility!="") {
					blnOk=false;
				}
			}
		}
		else{
			blnOk=false;
		}
	}

	if(blnOk)
	{
		document.getElementById("conteneurmenu").style.visibility="hidden";
		
		trimespaces();
		
		with(document.body.style) {
			if (!vertical) marginTop=marge_en_haut_de_page;
			else		   marginLeft=marge_a_gauche_de_la_page+"px";
		}
		
		positionne();
		CacherMenus();
	}
	document.getElementById("conteneurmenu").style.visibility='';
}
window.onresize = Chargement;

function positionne() {
	var largeur_fenetre = (isIE?document.body.clientWidth:window.innerWidth);
	var hauteur_fenetre = (isIE?document.body.clientHeight:window.innerHeight);
	if (centrer_menu) {
		if (!vertical) {
			var largeur_totale = espace_entre_menus * (nbmenu-1);
			if (isFinite(largeur_menu))
				largeur_totale += largeur_menu * nbmenu;
			else {
				for (i = 1; i <= nbmenu; i++)
					largeur_totale += largeur_menu[i-1];
			}
			left_menu = (largeur_fenetre - largeur_totale)/2;
		} else {
			var hauteur_totale = espace_entre_menus * (nbmenu-1);
			if (isFinite(hauteur_menu))
				hauteur_totale += hauteur_menu * nbmenu;
			else {
				for (i = 1; i <= nbmenu; i++)
					hauteur_totale += hauteur_menu[i-1];
			}
			top_menu = (hauteur_fenetre - hauteur_totale)/2;
		}
	}
	
	var cumul = 0;
	for(i=1;i<=nbmenu;i++) {
		with(document.getElementById("menu"+i).style) {
			if (!vertical) {
				top=top_menu+"px";
				//left=(((i-1)*(largeur_menu+espace_entre_menus))+1+left_menu)+"px";
				left=(((i-1)*espace_entre_menus)+cumul+1+left_menu)+"px";
			} else {
				//top=(((i-1)*(hauteur_menu+espace_entre_menus))+1+top_menu)+"px";
				top=(((i-1)*espace_entre_menus)+cumul+1+top_menu)+"px";
				left=left_menu+"px";
			}
			if (!suivre_le_scroll || isIE || isIE5mac)
				position="absolute";
			else position="fixed";
			//if (vertical) height=hauteur_menu+"px";
			margin="0";
			zIndex="2";
			if (vertical || isFinite(largeur_menu))
				width=largeur_menu+"px";
			else
				width=largeur_menu[i-1]+"px";
			if ((!vertical && isFinite(largeur_menu)) || (vertical && isFinite(hauteur_menu))) {
				cumul += (!vertical?largeur_menu:hauteur_menu);
			}
			else {
				cumul += (!vertical?largeur_menu[i-1]:hauteur_menu[i-1]);
				if (vertical) height=hauteur_menu[i-1]+"px";
			}
		}
	}
	
	cumul = 0;
	for(i=1;i<=nbmenu;i++) {
		if (document.getElementById("ssmenu"+i))//undefined
		{
			with(document.getElementById("ssmenu"+i).style) {
				if (!suivre_le_scroll || isIE || isIE5mac)
					position="absolute";
				else position="fixed";
				if (!vertical) {
					top=top_ssmenu+"px";
					//left=(((i-1)*(largeur_menu+espace_entre_menus))+1+left_menu)+"px";
					left=(((i-1)*espace_entre_menus)+cumul+1+left_menu)+"px";
				} else {
					left=left_ssmenu+"px";
					//top=(((i-1)*(hauteur_menu+espace_entre_menus))+1+top_menu)+"px";
					top=(((i-1)*espace_entre_menus)+cumul+1+top_menu)+"px";
				}
				if (isIE || isOpera || isIE5mac || !largeur_auto_ssmenu) {
					if (isFinite(largeur_sous_menu))
						width = largeur_sous_menu+(largeur_sous_menu!="auto"?"px":"");
					else
						width = largeur_sous_menu[i-1]+(largeur_sous_menu[i-1]!="auto"?"px":"");
				}
				else width = "auto";
				if (!vertical && !isIE5mac) {
					if ((width != "auto")
						&& ((left.replace(reg,'').valueOf()*1 + width.replace(reg,'').valueOf()*1) > largeur_fenetre))
						left = (largeur_fenetre-width.replace(reg,'').valueOf())+"px";
				}
				margin="0";
				zIndex="3";
			}
		}
		if ((!vertical && isFinite(largeur_menu)) || (vertical && isFinite(hauteur_menu))) {
			cumul += (!vertical?largeur_menu:hauteur_menu);
		}
		else {
			cumul += (!vertical?largeur_menu[i-1]:hauteur_menu[i-1]);
		}
	}
}


function MontrerMenu(strMenu) {
	if(blnOk) {
		AnnulerCacher();
		CacherMenus();
		if (document.getElementById(strMenu))//undefined
			with (document.getElementById(strMenu).style)
				visibility="visible";
	}
	SelectVisible("hidden",document.getElementsByTagName('select'));
}

function CacherDelai() {
	if (blnOk) {
		timeout = setTimeout('CacherMenus()',delai);
	}
}
function AnnulerCacher() {
	if (blnOk && timeout) {
		clearTimeout(timeout);
	}
}
function CacherMenus() {
	if(blnOk) {
		for(i=1;i<=nbmenu;i++) {
			if (document.getElementById("ssmenu"+i))//undefined
				with(document.getElementById("ssmenu"+i).style)
					visibility="hidden";
		}
	}
	SelectVisible("visible",document.getElementsByTagName('select'));
}

function trimespaces() {
	if(blnOk&&isIE5win) {
		for(i=1;i<=nbmenu;i++) {
			if (document.getElementById("ssmenu"+i))//undefined
				with(document.getElementById("ssmenu"+i))
					innerHTML = innerHTML.replace(/<LI>|<\/LI>/g,"");
		}
	}
}

function SelectVisible(v,elem) {
	if (blnOk && cacher_les_select && (isIE||isIE5win))
		for (var i=0;i<elem.length;i++) elem[i].style.visibility=v;
}
