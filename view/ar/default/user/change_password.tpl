{make_link controller="user" action="change_password" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td width="200">كلمة السر القديمة</td>
    <td>
			<input type="password" name="user[old_password]" id="user_old_password"/>
			{error_validator field_name="old_password"}
	</td>
  </tr>
  <tr>
    <td>كلمة السر الجديدة</td>
    <td>
			<input type="password" name="user[new_password]" id="user_new_password"/>
			{error_validator field_name="new_password"}
	</td>
  </tr>  
  <tr>
    <td>أعد كتابة كلمة السر الجديدة</td>
    <td>
			<input type="password" name="user[renew_password]" id="user_renew_password"/>
			{error_validator field_name="renew_password"}
	</td>
  </tr>   
  <tr>
    <td colspan="2" align="center"><input type="submit" value="تغيير كلمة السر"/></td>
  </tr>
</table>
{/form}
