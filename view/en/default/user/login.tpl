{make_link controller="user" action="login" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td >Email Address</td>
    <td>
			<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
			{error_validator field_name="email"}
	</td>
  </tr>
  <tr>
    <td >Password</td>
    <td>
			<input type="password" name="user[password]" id="user_password"/>
			{error_validator field_name="password"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Login  "/></td>
  </tr>
</table>
{/form}
