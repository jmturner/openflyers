<br /><h1>{ADMIN_SUBTITLE}</h1><br />
<div align="center">
<p>
<table style="text-align:center; font-size: 11px; width: 95%; padding: 0;" cellpadding="0">
<tr style="font-weight:bolder;">
	<td class="highlighted">{LAST_NAME}</td>
	<td class="highlight">{FIRST_NAME}</td>
	<td class="highlighted">{LOGIN}</td>
	<td class="highlight">{USER_TYPE}</td>
	<td class="highlighted">{PROFILE}</td>
	<td class="highlight">{EMAIL}</td>
	<td class="fee_expected">{ANNUAL_FEE}</td>
	<td>{MODIFY}</td>
	<td>{DELETE}</td>
	<td>{LICENSE}</td>
</tr>
<form action="index.php" method="post" name="user_form">
<input type="hidden" name="type" value="user" />
<input type="hidden" name="ref" value="-1" />
<input type="hidden" name="ope" value="update" />
<tr>
	<td colspan="6" align="left" class="fee_expected">{RENEW_MESSAGE}: {EXPIRY_DATE})</td>
	<td align="center"  class="fee_expected">
		<input type="submit" value="{USER_UPDATE}" />
	</td>
	<td colspan="3"></td>
</tr>
