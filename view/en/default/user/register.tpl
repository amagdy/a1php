{make_link controller="user" action="register" assign="form_action"}
{form action=$form_action}
<input type="hidden" name="user[group_id]" value="{$user.group_id}"/>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>Name: </td>
    <td>
	<input type="text" name="user[name]" id="user_name" value="{$user.name}"/>
	{error_validator field_name="name"}
    </td>
  </tr>
  <tr>
    <td>Email: </td>
    <td>
	<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
	{error_validator field_name="email"}
    </td>
  </tr>
  <tr>
    <td>Password: </td>
    <td>
	<input type="text" name="user[password]" id="user_repassword"/>
	{error_validator field_name="password"}
    </td>
  </tr>
  <tr>
    <td>Rewrite Password: </td>
    <td>
	<input type="text" name="user[repassword]" id="user_repassword"/>
	{error_validator field_name="repassword"}
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Sign Up  "/></td>
  </tr>
</table>
{/form}
