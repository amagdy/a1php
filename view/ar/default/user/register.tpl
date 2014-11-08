{make_link controller="user" action="register" assign="form_action"}
{form action=$form_action}
<input type="hidden" name="user[group_id]" value="{$user.group_id}"/>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>الاسم: </td>
    <td>
	<input type="text" name="user[name]" id="user_name" value="{$user.name}"/>
	{error_validator field_name="name"}
    </td>
  </tr>
  <tr>
    <td>البريد الإلكترونى:</td>
    <td>
	<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
	{error_validator field_name="email"}
    </td>
  </tr>
  <tr>
    <td>كلمة المرور: </td>
    <td>
	<input type="text" name="user[password]" id="user_repassword"/>
	{error_validator field_name="password"}
    </td>
  </tr>
  <tr>
    <td>أعد كتابة كلمة المرور: </td>
    <td>
	<input type="text" name="user[repassword]" id="user_repassword"/>
	{error_validator field_name="repassword"}
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  تسجيل  "/></td>
  </tr>
</table>
{/form}
