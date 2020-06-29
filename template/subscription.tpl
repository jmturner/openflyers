<div style="text-align:center;">
<form action="index.php" method="POST">
<input type="hidden" name="ope" value="fee" />
<table style="border: thin solid black;" width="40%">
	<tr>
		<td style="color: white; background:#2505ac;" colspan="2">{TITLE_SUBSCRIPTION}</td>
	</tr>
	<tr>
		<td colspan="2">{FEE_SPEECH}</td>
	</tr>
	<tr>
		<td>{FEE_OPTION_1}</td>
		<td><input type="radio" name="fee_mode" value="restricted" {CHECKED_1} /></td>
	</tr>
	<tr>
		<td>{FEE_OPTION_2}</td>
		<td><input type="radio" name="fee_mode" value="warning" {CHECKED_2} /></td>
	</tr>
	<tr>
		<td>{FEE_OPTION_3}</td>
		<td><input type="radio" name="fee_mode" value="off" {CHECKED_3} /></td>
	</tr>
	<tr>
		<td style="color: white; background:#2505ac;" colspan="2">{PROFILES_SUBSCRIPTION}</td>
	</tr>
	<tr>
		<td colspan="2">{FEE_OPTION_EXPLANATION}</td>
	</tr>
	<tr>
		<td colspan="2">{LIST_OF_PROFILES}</td>
	</tr>
	<tr>
		<td style="color: white; background:#2505ac;" colspan="2">{EXPIRY_SUBSCRIPTION}</td>
	</tr>
	<tr>
		<td colspan="2">{SUBSCRIPTION_DATE}</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="sub_update" value="{VALIDATE_SUB_UPDATE}" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="sub_validate" value="{VALIDATE}" /></td>
	</tr>
</table>
</form>
</div>