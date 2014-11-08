{make_link controller="useradmin" action="add" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>Email</td>
    <td>
	<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
	{error_validator field_name="email"}
    </td>
  </tr>
  <tr>
    <td>Password</td>
    <td>
	<input type="text" name="user[password]" id="user_password" value="{$user.password}"/>
	{error_validator field_name="password"}
    </td>
  </tr>
  <tr>
    <td>Name</td>
    <td>
	<input type="text" name="user[name]" id="user_name" value="{$user.name}"/>
	{error_validator field_name="name"}
    </td>
  </tr>
  <tr>
    <td>Active</td>
    <td>
	<input type="checkbox" name="user[active]" id="user_active" value="1"{if $user.active} checked="checked"{/if}/>
	{error_validator field_name="active"}
    </td>
  </tr>
  <tr>
    <td>Group</td>
    <td>
	<select name="user[group_id]" id="user_group_id">
		{html_options options=$arr_group_id selected=$user.group_id}
	</select>
	{error_validator field_name="group_id"}
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Add  "/></td>
  </tr>
</table>
{/form}
