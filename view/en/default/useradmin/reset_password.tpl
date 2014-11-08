{make_link controller="useradmin" action="reset_password" id=$user.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>New Password</td>
    <td>
	<input type="password" name="user[new_password]" id="user_new_password"/>
	{error_validator field_name="new_password"}
    </td>
  </tr>  
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Reset Password  "/></td>
  </tr>
</table>
{/form}
