{make_link controller="user" action="change_info" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td width="200">Full Name</td>
    <td>
			<input type="text" name="user[name]" id="user_name" value="{$user.name}"/>
			{error_validator field_name="name"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Save  "/></td>
  </tr>
</table>
{/form}
