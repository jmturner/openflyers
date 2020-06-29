<tr>
		<td class="form_cell">{USER_LOGIN}</td>
		<td class="form_cell"><input type="text" name="user_name" value="{USER_LOGIN_VALUE}" /></td>
		<td class="form_cell">{USER_LOGIN_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_PASSWORD}</td>
		<td class="form_cell"><input type="password" name="user_password" value="" /></td>
		<td class="form_cell">{USER_PASSWORD_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_PASSWORD}</td>
		<td class="form_cell"><input type="password" name="user_password_confirmation" value="" /></td>
		<td class="form_cell">{USER_PASSWORD_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_MEMBER_NUM}</td>
		<td class="form_cell"><input type="text" name="user_member_num" value="{USER_MEMBER_NUM_VALUE}" /></td>
		<td class="form_cell">{USER_MEMBER_NUM_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_TYPE}</td>
		<td class="form_cell">{USER_TYPE_MEMBER} : <input type="checkbox" name="user_type_member" value="1" {MEMBER_CHECKED} /><br />{USER_TYPE_INSTRUCTOR} : <input type="checkbox" name="user_type_instructor" value="1" {INSTRUCTOR_CHECKED} onclick=" if (document.user_manager.user_type_instructor.checked) { document.user_manager.user_inst_trigramme.disabled=false;  } else {document.user_manager.user_inst_trigramme.disabled=true;}" />
		</td>
		<td class="form_cell">{USER_TYPE_EXPLANATION}</td>
	</tr>
	<tr> 
		<td class="form_cell">{USER_TRIGRAM}</td>
		<td class="form_cell"><input type="text" name="user_inst_trigramme" value="{USER_TRIGRAM_VALUE}" {DISABLED} /></td>		
      <td class="form_cell">{USER_TRIGRAM_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_FIRST_NAME}:</td>
		<td class="form_cell"><input type="text" name="user_first_name" value="{USER_FIRST_NAME_VALUE}" /></td>
	    <td class="form_cell">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_LAST_NAME}:</td>
		<td class="form_cell"><input type="text" name="user_last_name" value="{USER_LAST_NAME_VALUE}" /></td>
		<td class="form_cell">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_PROFILE}:</td>
		<td class="form_cell">{USER_PROFILE_CHOICE}</td>
		<td class="form_cell">{USER_PROFILE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_LANGUAGE}</td>
		<td class="form_cell"> <select name="user_language" size="1">
		{USER_LANGUAGE_SELECT}
		</select> 
		</td>
		<td>{USER_LANGUAGE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_TIME_ZONE}</td>
		<td><select name="user_timezone" size="1">
			{TIMEZONE_VALUE}
		</select>
		</td>
		<td class="form_cell">{USER_TIMEZONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">{USER_PERSONAL_DATA}</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3">{USER_PERSONAL_DATA_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_MAIL_DATA}<br />{USER_ADDRESS_EXPLANATION}</td>
		<td class="form_cell" colspan="2">
			<textarea cols="40" rows="4" name="user_address">{USER_ADDRESS_VALUE}</textarea><br />
			{USER_ZIPCODE} : <input type="text" name="user_zipcode" size="5" value="{USER_ZIPCODE_VALUE}" /> {USER_CITY} : <input type="text" name="user_city" value="{USER_CITY_VALUE}" /><br />
			{USER_STATE} : <input type="text" name="user_state" size="5" value="{USER_STATE_VALUE}" /><br />
			{USER_COUNTRY} : <input type="text" name="user_country" value="{USER_COUNTRY_VALUE}" />
		</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_HOME_PHONE}:</td>
		<td class="form_cell"><input type="text" name="user_homephone" value="{USER_HOME_PHONE_VALUE}" /> <input type="checkbox" name="user_show_homephone" value="64" {USER_CHECKED_1}/></td>
		<td class="form_cell">{USER_HOME_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_WORK_PHONE}:</td>
		<td class="form_cell"><input type="text" name="user_workphone" value="{USER_WORK_PHONE_VALUE}" /> <input type="checkbox" name="user_show_workphone" value="128" {USER_CHECKED_2}/></td>
		<td class="form_cell">{USER_WORK_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_CELL_PHONE}:</td>
		<td class="form_cell"><input type="text" name="user_cellphone" value="{USER_CELL_PHONE_VALUE}" /> <input type="checkbox" name="user_show_cellphone" value="256" {USER_CHECKED_3}/></td>
		<td class="form_cell">{USER_CELL_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_NOTIFY_MAIL}:</td>
		<td class="form_cell"><input type="checkbox" name="user_notify[]" value="1" {USER_NOTIFY_1}/></td>
		<td class="form_cell">{USER_NOTIFY_MAIL_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_NOTIFY_SMS}:</td>
		<td class="form_cell"><input type="checkbox" name="user_notify[]" value="2" {USER_NOTIFY_2}/></td>
		<td class="form_cell">{USER_NOTIFY_SMS_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">{USER_VIEW_MODE}</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">{USER_VIEW_AIRCRAFT}</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_aircraft" value="0" {USER_CHECKED_4}/></td>
		<td class="form_cell" style="text-align:center">{USER_VIEW_AIRCRAFT_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">{USER_VIEW_INSTRUCTOR}</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_inst" value="0" {USER_CHECKED_5}/></td>
		<td class="form_cell" style="text-align:center">{USER_VIEW_INSTRUCTOR_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">{USER_POPUP_LEGEND}</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_colorhelp" value="4" {USER_CHECKED_6}/></td>
		<td class="form_cell" style="text-align:center">{USER_POPUP_LEGEND_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" style="text-align:center">{USER_DATE_FORMAT}</td>
		<td class="form_cell" style="text-align:center"><input type="checkbox" name="user_englishdate" value="8" {USER_CHECKED_7}/></td>
		<td class="form_cell" style="text-align:center">{USER_DATE_FORMAT_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell" colspan="3" style="background: #cccccc;">&nbsp;</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_EMAIL}</td>
		<td class="form_cell"><input type="text" name="user_email" value="{USER_EMAIL_VALUE}" /> <input type="checkbox" name="user_show_email" value="512" {USER_CHECKED_8}/></td>
		<td class="form_cell">{USER_EMAIL_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{USER_MAILING_LIST}</td>
		<td class="form_cell">
			{USER_MAILING_LIST_SIGN}
			<input type="checkbox" name="subscribe" /> 
			{USER_MAILING_LIST_SIGNOFF}
			<input type="checkbox" name="unsubscribe" /></td>
		<td class="form_cell">{USER_MAILING_LIST_EXPLANATION} ({MAILING_LIST})</td>
	</tr>
	<tr>
		<td colspan="3" style="text-align: center;"><input type="submit" value="{VALIDATE}" /></td>
	</tr>
</table>
<input type="hidden" name="user_num" value="{USER_REFERENCE_NUM}" />
</form>
</div>
<p><a href="index.php?type=user&ope=manage" class="dblink"> &nbsp;{BACK}&nbsp; </a></p>