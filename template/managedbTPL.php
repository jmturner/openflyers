<br />
<h1><?php echo($lang['BDD_TITLE']); ?></h1>
<br />

<div align="center" style="margin:1em;">
<table class="warn" style="width: 70%; text-align: justify; font-size: 12px;">
	<tr>
	  <td>Cette interface vous permet d'agir sur la base de donn&eacute;es en l'optimisant,
	    la sauvegardant, la modifiant ou encore  en purgeant les tables susceptibles
	    de poser des probl&egrave;mes. Nous conseillons
		un usage r&eacute;gulier de l'optimisation.<br /><br />
		Si vous obtenez des heures de lever de coucher du soleil aberrantes,
		purger	la table peut r&eacute;soudre le probl&egrave;me.<br />
		<br />
	Nous souhaitons insister aussi sur l'importance d'effectuer <em>r&eacute;guli&egrave;rement</em> des 
	sauvegardes de la base de donn&eacute;es.</td>
	</tr>
</table><br />
<a href="index.php?type=db&ope=optimize" class="dblink"> &nbsp;OPTIMISATION DES TABLES&nbsp; </a><br /><br />
<a href="index.php?type=db&ope=srss" class="dblink"> &nbsp;PURGER LES HEURES DE LEVER ET DE COUCHER DU SOLEIL&nbsp; </a><br /><br />
<a target="_blank" href="index.php?type=db&ope=csv" class="dblink"> &nbsp;EXPORT CSV&nbsp; </a><br /><br />
<a target="_blank" href="index.php?type=db&ope=backup" class="dblink"> &nbsp;BACKUP BASE DE DONNEES&nbsp; </a><br /><br />
<a href="index.php?type=user&ope=import" class="dblink"> &nbsp;IMPORTATION D'UNE LISTE D'UTILISATEURS&nbsp; </a><br /><br />
</div>