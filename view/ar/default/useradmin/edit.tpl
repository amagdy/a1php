{make_link controller="useradmin" action="edit" id=$user.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" dir="rtl">
  <tr>
    <td>البريد الإلكترونى</td>
    <td>
	<input type="text" name="user[email]" id="user_email" value="{$user.email}"/>
	{error_validator field_name="email"}
    </td>
  </tr>
  <tr>
    <td>كلمة المرور</td>
    <td>
	<input type="text" name="user[password]" id="user_password" value="{$user.password}"/>
	{error_validator field_name="password"}
    </td>
  </tr>
  <tr>
    <td>الاسم</td>
    <td>
	<input type="text" name="user[name]" id="user_name" value="{$user.name}"/>
	{error_validator field_name="name"}
    </td>
  </tr>
  <tr>
    <td>فعال</td>
    <td>
	<input type="checkbox" name="user[active]" id="user_active" value="1"{if $user.active} checked="checked"{/if}/>
	{error_validator field_name="active"}
    </td>
  </tr>
  <tr>
    <td>المجموعة</td>
    <td>
	<select name="user[group_id]" id="user_group_id">
		{html_options options=$arr_group_id selected=$user.group_id}
	</select>
	{error_validator field_name="group_id"}
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  حفظ  "/></td>
  </tr>
</table>
{/form}
