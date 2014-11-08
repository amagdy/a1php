{make_link controller="link" action="edit" id=$link.id assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" dir="rtl" class="getall_table">
  <tr>
    <td class="normal_text">النطاق</td>
    <td>
		{html_options name="link[domain_id]" options=$arr_domains selected=$link.domain_id}
		{error_validator field_name="domain_id"}
	</td>
  </tr>
  <tr>
    <td class="normal_text">الرابط المستخدم</td>
    <td>
		<input type="text" name="link[friendly_url]" id="link_friendly_url" value="{$link.friendly_url}"/>
	{error_validator field_name="friendly_url"}
	</td>
  </tr>
  <tr>
    <td class="normal_text">الرابط الحقيقى</td>
    <td>
		<input type="text" name="link[real_url]" id="link_real_url" value="{$link.real_url}"/>
	{error_validator field_name="real_url"}
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="حفظ"/></td>
  </tr>
</table>
{/form}
