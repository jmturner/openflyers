<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * addmod_profile.content.php
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
 * @version    CVS: $Id: importCSV.php,v 1.11.2.2 2005/11/30 21:21:00 claratte Exp $
 * @link       http://openflyers.org
 * @since      Thu May 12 2004
 */

?>
<br />
<h1><?php echo($lang['IMPORT_USER_TITLE']); ?></h1>
<br />
<div align="center">
<form action="index.php" method="post" name="user_manager" enctype="multipart/form-data">
<input type="hidden" name="type" value="user">
<input type="hidden" name="ope" value="csvimport">
<table class="form_gen" style="font-size: 12px;" cellspacing="2">
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>1/ Avant
		    de commencer: Attention au formatage de votre fichier!</strong></td>
	</tr>
	<tr style="text-align:justify">
		<td colspan="3"> Le fichier CSV doit utiliser des s&eacute;parateurs du 
          type <strong>&quot;;&quot; (Points Virgules) ou &quot;,&quot; (Virgules)</strong> 
          et des <strong>Retours &agrave; la ligne</strong>. Les virgules (ou 
          le point virgule) servent de s&eacute;parateurs de donn&eacute;es et 
          les retours &agrave; la ligne de s&eacute;parateurs d'enregistrement. 
          Basiquement, une ligne correspond &agrave; un utilisateur et les donn&eacute;es 
          de l'utilisateur sont s&eacute;par&eacute;es par une virgule ou un point 
          virgule. <br /> 
	      Tout autre format entra&icirc;nerait
		  une erreur de r&eacute;cup&eacute;ration. Le format de chaque ligne est donc le suivant:
		  <em><strong>n&deg; de
		  membre;nom;pr&eacute;nom;email; </strong></em>. S'il manque un &eacute;l&eacute;ment, deux
		  points virgules doivent se suivrent. Par exemple, un utilisateur sans
	  e-mail donnerait la ligne suivante: <em><strong>num&eacute;ro;nom;pr&eacute;nom;;</strong></em> .</td>
	</tr>
	<tr>
		<td>Type de s&eacute;parateur:</td>
		<td>Virgule <input type="radio" name="separator_type" value="," checked=="checked" /></td>
		<td>Point-virgule <input type="radio" name="separator_type" value=";" /></td>
	</tr>
	<tr style="text-align:justify">
		<td rowspan="14"> Veuillez s&eacute;lectionner les donn&eacute;es qui seront 
          contenues dans votre fichier de donn&eacute;es CSV. Attention, l'ordre 
          des donn&eacute;es doit &ecirc;tre le m&ecirc;me que celui des cat&eacute;gories 
          propos&eacute;es. Un champ vide doit &ecirc;tre caract&eacute;ris&eacute; 
          (deux s&eacute;parateurs cons&eacute;cutifs indiquant que le champ est 
          vide. </td>
	    <td>Login</td>
		<td><input type="checkbox" name="LOGIN_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Mot de passe</td>
		<td><span style="color: red">0bligatoire</span></td>
	</tr>
    <tr>
		<td>id OF</td>
		<td><input type="checkbox" name="OFID_TRUE" checked="checked"></td>
	</tr>
	<tr>
		<td>Nom</td>
		<td><span style="color: red">Obligatoire</span></td>
	</tr>
    <tr>
		<td>Pr&eacute;nom</td>
		<td><span style="color: red">Obligatoire</span></td>
	</tr>
    <tr>
		<td>E-Mail</td>
		<td><input type="checkbox" name="MAIL_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Adresse postale</td>
		<td><input type="checkbox" name="ADDRESS_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Code postal</td>
		<td><input type="checkbox" name="ZIP_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Ville</td>
		<td><input type="checkbox" name="CITY_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Etat</td>
		<td><input type="checkbox" name="STATE_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>Pays</td>
		<td><input type="checkbox" name="COUNTRY_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>T&eacute;l&eacute;phone fixe domicile</td>
		<td><input type="checkbox" name="PHONE_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>T&eacute;l&eacute;phone travail</td>
		<td><input type="checkbox" name="WORKPHONE_TRUE" checked="checked"></td>
	</tr>
    <tr>
		<td>T&eacute;l&eacute;phone portable</td>
		<td><input type="checkbox" name="CELL_TRUE" checked="checked"></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>2/ Indiquez
		    &agrave; l'aide du bouton Parcourir, le fichier CSV contenant vos donn&eacute;es</strong></td>
	</tr>
	<tr>
		<td class="form_cell">Fichier contenant les donn&eacute;es</td>
		<td class="form_cell"><input type="file" name="filename" ></td>
		<td class="form_cell">Le login cr&eacute;&eacute; automatiquement correspondra &agrave; la
		  premi&egrave;re lettre du pr&eacute;nom suivi du nom et le mot de passe au
		  n&deg; de membre. <strong>Tout &eacute;l&eacute;ment
	  non alphanum&eacute;rique sera &eacute;limin&eacute; du login ou du mot de
	  passe</strong>. Les login sont form&eacute;s de la premi&egrave;re lettre
	  du pr&eacute;nom suivie du nom</td>
	</tr>

	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>Remarque:
		    Tous les utilisateurs que vous ins&eacute;rez par l'import sont consid&eacute;r&eacute;s
		    comme des membres. Pour supprimer ce statut et/ou rajouter un statut
		    instructeur, il faudra passer par une modification individuelle.</strong></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;
		</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>3/ Vous
		    allez indiquer, en fonction des profils que vous avez cr&eacute;&eacute;,
		    le profil
		    &quot;type&quot; du membre &quot;type&quot;. Les modifications seront ensuite apport&eacute;es
		    de mani&egrave;re individuelle.</strong></td>
	</tr>
	<tr>
		<td class="form_cell">Profil d'utilisateurs</td>
		<td class="form_cell"><?php echo($choixProfile); ?></td>
		<td class="form_cell">.</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>4/ La
		    d&eacute;finition
	    du mode de visualisation par d&eacute;faut de vos utilisateurs. Chaque utilisateur
		    pourra personnaliser ult&eacute;rieurement.</strong></td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">&nbsp;</td>
		<td class="form_cell" style="text-align:center">Menu en ligne</td>
		<td class="form_cell" style="text-align:center">Menu d&eacute;roulant</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">&nbsp;</td>
		<td class="form_cell" style="text-align:center"><input type="radio" name="user_combo" value="0" checked="checked" /></td>
		<td class="form_cell" style="text-align:center"><input type="radio" name="user_combo" value="2" /></td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">Visualisation des Avions sur le cahier de r&eacute;servations</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_aircraft" value="0" checked="checked" /></td>
		<td class="form_cell" style="text-align:center">Si vous souhaitez affichez les r&eacute;servations sur la page principale, laissez cette case coch&eacute;e.</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">Visualisation des Instructeurs sur le cahier de r&eacute;servation</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_inst" value="0" checked="checked" /></td>
		<td class="form_cell" style="text-align:center">Si vous souhaitez que l'utilisateur visualise les disponibilt&eacute;s des instructeurs directement sur la page cahier de r&eacute;servations, cochez cette case.</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">Popup L&eacute;gende</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_colorhelp" value="4" checked="checked" /></td>
		<td class="form_cell" style="text-align:center">Si vous souhaitez que l'utilisateur dispose d'un popup avec la l&eacute;gende des couleurs utilis&eacute;es, assurez vous que cette case soit coch&eacute;e.</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">Format de date</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_englishdate" value="8" checked="checked" /></td>
		<td class="form_cell" style="text-align:center">Par d&eacute;faut, le format de date est JJ/MM/AAAA. Si vous souhaitez utiliser le format anglo-saxon AAAA/MM/JJ plus pratique pour trier les dates, cochez cette case.</td>
	</tr>
<?php if(isset($mailing_list))
	{?>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>5/ La
		    liste de diffusion. Indiquez si vous souhaitez inscrire vos membres
		    &agrave; la liste de diffusion.</strong></td>
	</tr>
	<tr>
		<td class="form_cell">Abonner les membres</td>
		<td class="form_cell"><input type="checkbox" name="subscribe" /></td>
		<td class="form_cell">Vous avez sp&eacute;cifiez dans votre param&eacute;trage club l'existence
		  d'une liste de diffusion (		  <?php echo($mailing_list) ?>		  ); Avant de cocher cette
		  case, veuillez vous assurer que vos membres n'y sont pas d&eacute;j&agrave; abonn&eacute;s
		  et que le type de mailing list est bien support&eacute;e par l'application.</td>
	</tr>
<?php }?>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>Validez pour
		  lancer le processus... L'import peut prendre un certain temps si la
		  liste d'utilisateurs est longue.</strong></td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;"><input type="submit" value="<?php echo($lang['VALIDATE']); ?>" /></td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;"><strong>NB: 
          Il est tout &agrave; fait possible, en fonction de la qualit&eacute; 
          de l'h&eacute;bergement utilis&eacute; (de sa charge instantann&eacute;e, 
          de son param&eacute;trage), que le processus ne se termine pas normalement... 
          Avant de relancer tout nouvel import, veuillez v&eacute;rifiez si une 
          partie de l'import n'a pas &eacute;t&eacute; effectu&eacute;. Dans l'affirmative, 
          v&eacute;rifiez l'int&eacute;grit&eacute; des donn&eacute;es du dernier 
          utilisateur import&eacute; et supprimez le si toutes les donn&eacute;es 
          n'ont pas &eacute;t&eacute; int&eacute;gr&eacute;es. Reprenez votre 
          fichier CSV en &eacute;liminant les membres correctement import&eacute;s 
          et relancez la proc&eacute;dure d'import.</strong></td>
	</tr>
</table>
</form>
</div>
<p><a href="index.php?type=db&ope=manage" class="dblink"> &nbsp;RETOUR&nbsp; </a></p>