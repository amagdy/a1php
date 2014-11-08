{make_link controller="configuration" action="add" id=$configuration.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td>Variable Type</td>
    <td>
			<select name="configuration[variable_type]" id="configuration_variable_type">
				<option value="string"{if $configuration.variable_type eq "string"} selected{/if}>String</option>
				<option value="number"{if $configuration.variable_type eq "number"} selected{/if}>Number</option>
				<option value="boolean"{if $configuration.variable_type eq "boolean"} selected{/if}>Boolean</option>
			</select>
			{error_validator field_name="variable_type"}
	</td>
  </tr>
  <tr>
    <td>Key</td>
    <td>
			<input type="text" name="configuration[key]" id="configuration_key" value="{$configuration.key}"/>
			{error_validator field_name="key"}
	</td>
  </tr>
  <tr>
    <td>Value</td>
    <td>
			<textarea name="configuration[value]" id="configuration_value">{$configuration.value}</textarea>
			{error_validator field_name="value"}
	</td>
  </tr>
  <tr>
    <td >Description</td>
    <td>
			<textarea name="configuration[description]" id="configuration_description">{$configuration.description}</textarea>
			{error_validator field_name="description"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Add"/></td>
  </tr>
</table>
{/form}
