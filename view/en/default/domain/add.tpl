{make_link controller="domain" action="add" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" class="getall_table">
  <tr>
    <td class="normal_text">Domain Name</td>
    <td>
		<input type="text" name="domain[domain_name]" id="domain_domain_name" value="{$domain.domain_name}"/>
		{error_validator field_name="domain_name"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Add"/></td>
  </tr>
</table>
{/form}
