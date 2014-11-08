{make_link controller="user" action="login" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" dir="rtl">
  <tr>
    <td class="bold_text">البريد الإلكترونى:</td>
    <td>
			<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
			{error_validator field_name="email"}
	</td>
  </tr>
  <tr>
    <td class="bold_text">كلمة السر: </td>
    <td>
			<input type="password" name="user[password]" id="user_password"/>
			{error_validator field_name="password"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  دخول  "/></td>
  </tr>
</table>
{/form}
