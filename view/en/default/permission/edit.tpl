{make_link controller="permission" action="edit" id=$permission.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td >Controller</td>
    <td>
			<input type="text" name="permission[controller]" id="permission_controller" value="{$permission.controller}"/>
			{error_validator field_name="controller"}
	</td>
  </tr>
  <tr>
    <td >Action</td>
    <td>
			<input type="text" name="permission[action]" id="permission_action" value="{$permission.action}"/>
			{error_validator field_name="action"}
	</td>
  </tr>
  <tr>
    <td >Extra Params</td>
    <td>
			<input type="text" name="permission[extra_params]" id="permission_extra_params" value="{$permission.extra_params}"/>
			{error_validator field_name="extra_params"}
	</td>
  </tr>
  <tr>
    <td >Allow</td>
    <td>
		<input type="checkbox" name="permission[allow]" id="permission_allow" value="1"{if $permission.allow} checked{/if}/>
	</td>
  </tr>
  <tr>
    <td >Description</td>
    <td>
			<textarea name="permission[description]" id="permission_description">{$permission.description}</textarea>
			{error_validator field_name="description"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Save"/></td>
  </tr>
</table>
{/form}
