<tr>
	<form action="index.php" method="POST">
	<td class="highlighted">
	{LICENSE_NAME} 
	</td>
	<td class="highlight">
	{LICENSE_DATE} 
	</td>
	<td class="highlighted">
		<input type="checkbox" name="alert_license" value="1" {CHECKED} />
	</td>
	<td>
	<input type="hidden" name="qualif_id" value="{QUALIF_ID}" />
		<input type="hidden" name="ref" value="{REF}" />
		<input type="hidden" name="type" value="user" />
		<input type="hidden" name="ope" value="update_license" />
		<input type="image" src="img/license.gif" alt="{LICENSE_MOD}"  />
	</td>
	</form>
	<td>
		<form action="index.php" method="POST">
		<form action="index.php" method="POST">
		<input type="hidden" name="qualif_id" value="{QUALIF_ID}" />
		<input type="hidden" name="ref" value="{REF}?>" />
		<input type="hidden" name="type" value="user" />
		<input type="hidden" name="ope" value="del_license" />
		<input type="image" src="img/destroy.gif" alt="{DELETE}" />
		</form>
	</td>
</tr>