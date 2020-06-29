<tr>
	<td class="highlight">
		{CALLSIGN}
	</td>
	<td class="highlight">
		{NON_BOOKABLE}
	</td>
	<td class="highlighted">
		{TYPE}
	</td>
	<td class="highlight">
		{HOURLY_COST}
	</td>
	<td class="highlighted">
		{SEATS_AVAILABLE}
	</td>
	<td>
		<form action="index.php" method="post">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="modify" />
			<input type="hidden" name="ref" value="{REFERENCE}" />
			<input type="image" src="img/modify.gif" alt="{MODIFY}" />
		</form>
	</td>
	<td>
		<form action="index.php" method="post" onsubmit="return confirm('{JS_CONFIRM_ACFT_DELETION}')">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="destroy" />
			<input type="hidden" name="ref" value="{REFERENCE}" />
			<input type="image" src="img/destroy.gif" alt="{DELETE}" />
		</form>
	</td>
	<td>
		<form action="index.php" method="post">
			<input type="hidden" name="type" value="aircraft" />
			<input type="hidden" name="ope" value="license" />
			<input type="hidden" name="ref" value="{REFERENCE}" />
			<input type="image" src="img/license.gif" alt="{MANAGE_LICENSE}" />
		</form>
	</td>
</tr>
</form>