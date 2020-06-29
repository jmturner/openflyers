<br />
<h1>{CSV_TITLE}</h1>
<br />
<div align="center">
<form action="index.php" method="post" name="user_manager" enctype="multipart/form-data">
<input type="hidden" name="type" value="user">
<input type="hidden" name="ope" value="csvimport">
    <table class="form_gen" style="font-size: 12px;" cellspacing="2">
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_IMPORT_METHOD}</strong></td>
      </tr>
      <tr>
        <td><input type="checkbox" name="import_type" checked="checked" value="true"/>&nbsp;{CSV_IMPORT_TYPE}</td>
        <td colspan="2">{CSV_IMPORT_METHOD_EXPLANATION}</td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_INIT}</strong></td>
      </tr>
      <tr style="text-align:justify"> 
        <td colspan="3">{CSV_INIT_EXPLANATION}</td>
      </tr>
      <tr> 
        <td>{CSV_SEPARATOR_TYPE}</td>
        <td>{CSV_COMMA} 
          <input type="radio" name="separator_type" value="," checked=="checked" /></td>
        <td>{CSV_SEMI_COLON}
          <input type="radio" name="separator_type" value=";" /></td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_FILE_PATH}</strong></td>
      </tr>
      <tr> 
        <td class="form_cell">{CSV_FILE}</td>
        <td class="form_cell"><input type="file" name="filename" ></td>
        <td class="form_cell">{CSV_FILE_EXPLANATION}</td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_MORE_EXPLANATION}</strong></td>
      </tr>
      <tr> 
        <td colspan="3">&nbsp; </td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_PROFILE_CHOICE}</strong></td>
      </tr>
      <tr> 
        <td class="form_cell">{CSV_PROFILE}</td>
        <td class="form_cell">{CSV_SELECT_PROFILE}</td>
        <td class="form_cell">.</td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_VIEW_MODE}</strong></td>
      </tr>
      <tr> 
        <td class="form_cell" style="text-align:center">{CSV_VIEW_AIRCRAFTS}</td>
        <td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_aircraft" value="0" checked="checked" /></td>
        <td class="form_cell" style="text-align:center">{CSV_VIEW_AIRCRAFTS_EXPLANATION}</td>
      </tr>
      <tr> 
        <td class="form_cell" style="text-align:center">{CSV_VIEW_INSTRUCTORS}</td>
        <td class="form_cell" style="text-align:center"><input type="checkbox" name="user_show_inst" value="0" checked="checked" /></td>
        <td class="form_cell" style="text-align:center">{CSV_VIEW_INSTRUCTORS_EXPLANATION}</td>
      </tr>
      <tr> 
        <td class="form_cell" style="text-align:center">{CSV_POPUP}</td>
        <td class="form_cell" style="text-align:center"><input type="checkbox" name="user_colorhelp" value="4" checked="checked" /></td>
        <td class="form_cell" style="text-align:center">{CSV_POPUP_EXPLANATION}</td>
      </tr>
      <tr> 
        <td class="form_cell" style="text-align:center">{CSV_DATE_FORMAT}</td>
        <td class="form_cell" style="text-align:center"><input type="checkbox" name="user_englishdate" value="8" checked="checked" /></td>
        <td class="form_cell" style="text-align:center">{CSV_DATE_FORMAT_EXPLANATION}</td>
      </tr>
	<tr>
		<td class="form_cell">{CSV_USER_HOME_PHONE}:</td>
		<td class="form_cell"><input type="checkbox" name="user_show_homephone" value="64" checked="checked" /></td>
		<td class="form_cell">{CSV_USER_HOME_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{CSV_USER_WORK_PHONE}:</td>
		<td class="form_cell"><input type="checkbox" name="user_show_workphone" value="128" checked="checked" /></td>
		<td class="form_cell">{CSV_USER_WORK_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{CSV_USER_CELL_PHONE}:</td>
		<td class="form_cell"><input type="checkbox" name="user_show_cellphone" value="256" checked="checked" /></td>
		<td class="form_cell">{CSV_USER_CELL_PHONE_EXPLANATION}</td>
	</tr>
	<tr>
		<td class="form_cell">{CSV_USER_EMAIL}:</td>
		<td class="form_cell"><input type="checkbox" name="user_show_cellphone" value="256" checked="checked" /></td>
		<td class="form_cell">{CSV_USER_EMAIL_EXPLANATION}</td>
	</tr>
    <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_MAILING_LIST_HEADER}</strong></td>
      </tr>
      <tr> 
        <td class="form_cell">{CSV_SUBSCRIBE_ML}</td>
        <td class="form_cell"><input type="checkbox" name="subscribe" value="noaction" /></td>
        <td class="form_cell">{CSV_MAILING_LIST}</td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_VALIDATE}</strong></td>
      </tr>
      <tr> 
        <td colspan="3" style="text-align: center;"><input type="submit" value="{VALIDATE}" /></td>
      </tr>
      <tr> 
        <td class="form_cell" colspan="3" style="background: #cccccc;"><strong>{CSV_WARNING}</strong></td>
      </tr>
    </table>
</form>
</div>
<p><a href="index.php?ope=manage" class="dblink"> &nbsp;{BACK}&nbsp; </a></p>