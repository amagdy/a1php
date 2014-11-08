{make_link controller="configuration" action="admin_edit" id=$configuration.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td >{$configuration.description}</td>
    <td>
		{if $configuration.variable_type eq "string"}
		<textarea name="configuration[value]" id="configuration_value">{$configuration.value}</textarea>
		{elseif $configuration.variable_type eq "number"}
		<input type="text" name="configuration[value]" id="configuration_value" value="{$configuration.value}"/>
		{elseif $configuration.variable_type eq "boolean"}
		<input type="checkbox" name="configuration[value]" id="configuration_value" value="1"{if $configuration.value eq "1"} checked{/if}/>
		{/if}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Save"/></td>
  </tr>
</table>
{/form}
