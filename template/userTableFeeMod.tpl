<tr>
	<td class="highlighted">
		{LAST_NAME}
	</td>
	<td class="highlight">
		{FIRST_NAME}
	</td>
	<td class="highlighted">
		{LOGIN}
	</td>
	<td class="highlight">
		{STATUS}
	</td>
	<td class="highlighted">
		{PROFILE_SELECT}
	</td>
	<td class="highlight">
		<a href="mailto:{EMAIL}">{EMAIL}</a>
	</td>
	<td {CLASS_SPECIAL}>
		{COTISE_OR_NOT}
	</td>
	<td>
		<input type="image" src="img/modify.gif" alt="{MODIFY}" onclick="if(manage_user(document.user_form,{REFERENCE},'modify')){submit();}else{return(false);};">
	</td>
	<td>
		<input type="image" src="img/destroy.gif" alt="{DELETE}" onclick="if(manage_user(document.user_form,{REFERENCE},'destroy')){submit();}else{return(false);};" />
	</td>
	<td>
		<input type="image" src="img/license.gif" alt="{LICENSE_MOD}" onclick="if(manage_user(document.user_form,{REFERENCE},'licensing')){submit();}else{return(false);};" />
	</td>
</tr>