{make_link controller="group" action="add" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>Group Name</td>
    <td>
	<input type="text" name="group[name]" id="group_name" value="{$group.name}"/>
	{error_validator field_name="name"}
    </td>
  </tr>
  <tr>
    <td>Layout</td>
    <td>
	<select name="group[layout]" id="group_layout">
		{html_options options=$arr_layout selected=$group.layout}
	</select>
	{error_validator field_name="layout"}
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="  Add  "/></td>
  </tr>
</table>
{/form}
