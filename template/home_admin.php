<?php
require_once("./classes/club.class.php");
$currentClub = new club($database);
$myVar ='init';
$currentClub->getClubFromDatabase();
?>
<p style="margin:1em;"><span class="insiston"><?php echo($lang['WELCOME_ADMIN']); ?></span>
</p>
<div style="width : 100%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="bookInstructionMinTime"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr><td>
			<input type="checkbox" name="BIMT_flag" value="1"<?php if($parameter->isBookInstructionMinTime()) { ?> checked="checked"<?php }?>/>
			<?php echo($lang['ADMIN_BOOK_INSTRUCTION_MIN_TIME']); ?>
	</td></tr>
	<tr><td><select name="BIMT_value" size="1">
		<?php for ($minSlotI=1; $minSlotI<13; $minSlotI++) {
            ?><option value="<?php echo($minSlotI*15);?>" <?php
            if ($parameter->getBookInstructionMinTime()==($minSlotI*15))	{
                echo('selected="selected"');
            }
            echo('>'.($minSlotI*15).' '.$lang['MINUTES'].'</option>');
		}
		?></select></td></tr>
	<tr><td align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 100%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="noVisitRefresh"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr><td>
			<input type="checkbox" name="NVR_flag" value="1"<?php if($parameter->isNoVisitorRefresh()) { ?> checked="checked"<?php }?>/><?php echo($lang['ADMIN_NO_VISIT_REFRESH']); ?>
	</td></tr>
	<tr><td align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 100%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="bookDateLimitation"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr><td>
			<input type="checkbox" name="BDateL_flag" value="1"<?php if($parameter->isBookDateLimitation()) { ?> checked="checked"<?php }?>/><?php echo($lang['ADMIN_BOOK_DATE_LIMITATION']); ?>
	</td></tr>
	<tr><td>
			<?php echo($lang['ADMIN_BDATEL_VALUE']); ?><input type="text" name="BDateL_value" value="<?php echo($parameter->getBookDateLimitation());?>"/>
	</td></tr>
	<tr><td align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 40%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="bookDurationLimitation"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr><td>
			<input type="checkbox" name="BDL_flag" value="1"<?php if($parameter->isBookDurationLimitation()) { ?> checked="checked"<?php }?>/><?php echo($lang['ADMIN_BOOK_DURATION_LIMITATION']); ?>
	</td></tr>
	<tr><td>
			<?php echo($lang['ADMIN_BDL_VALUE']); ?><input type="text" name="BDL_value" value="<?php echo($parameter->getBookDurationLimitation());?>"/>
	</td></tr>
	<tr><td align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 40%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="no_callsign_display"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr><td>
			<input type="checkbox" name="noCallsignDisplay" value="1"<?php if($parameter->isNoCallsignDisplay()) { ?> checked="checked"<?php }?>/><?php echo($lang['ADMIN_NO_CALLSIGN_DISPLAY']); ?>
	</td></tr>
	<tr><td align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 40%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="book_allocating_rule"/>
<input type="hidden" name="type" value=""/>
<table style="border: thin solid black;">
	<tr>
		<td colspan="2" style="color: white; background:#2505ac;">
			<?php echo($lang['ADMIN_BOOK_ALLOCATING_RULE_TITLE']); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo($lang['ADMIN_BOOK_ALLOCATING_RULE_0']); ?>
		</td>
		<td>
			<input type="radio" name="BAllocating_flag" value="0"<?php if($parameter->getBookAllocatorRule()==0) { ?> checked="checked"<?php }?>/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo($lang['ADMIN_BOOK_ALLOCATING_RULE_1']); ?>
		</td>
		<td>
			<input type="radio" name="BAllocating_flag" value="1"<?php if($parameter->getBookAllocatorRule()==1) { ?> checked="checked"<?php }?>/>
		</td>
	</tr>
	<tr><td colspan="2" align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>
<div style="width : 40%; text-align:center;">
<form action="index.php" method="post">
<input type="hidden" name="ope" value="licence" />
<input type="hidden" name="type" value="">
<table style="border: thin solid black;">
	<tr>
		<td colspan="2" style="color: white; background:#2505ac;">
			<?php echo($lang['QUALIF_TITLE']); ?>
		</td>
	</tr>
	<tr><td colspan="2">
			<?php echo($lang['QUALIF_STATUS']); ?>
	</td></tr>
	<tr>
		<td>
			<?php echo($lang['QUALIF_RESTRICTED']); ?>
		</td>
		<td>
			<input type="radio" name="switchto" value="restricted"<?php 
			if (($currentConfig->qualif_enabled) and ($currentConfig->qualif_required)) {
			    ?> checked="checked"<?php
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo($lang['QUALIF_WARNING']); ?>
		</td>
		<td>
			<input type="radio" name="switchto" value="warning"<?php 
			if (($currentConfig->qualif_enabled) and !($currentConfig->qualif_required)) {
			    ?> checked="checked"<?php
			}
			?>/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo($lang['QUALIF_DISABLED']); ?>
		</td>
		<td>
			<input type="radio" name="switchto" value="off"<?php 
			if (!($currentConfig->qualif_enabled)) {
			    ?> checked="checked"<?php
			}
			?>/>
		</td>
	</tr>
	<tr><td colspan="2" align="center"><input type="submit"  value="<?php echo($lang['VALIDATE']); ?>" /></td></tr>
</table>
</form>
</div>