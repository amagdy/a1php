{make_link controller="links_category" action="add" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" class="getall_table">
  <tr>
    <td class="normal_text">Category name</td>
    <td>
		<input type="text" name="links_category[name]" id="links_category_name" value="{$links_category.name}"/>
	{error_validator field_name="name"}
	</td>
  </tr>
    <tr>
    <td class="normal_text">Category</td>
    <td>
		{html_options name="links_category[lang]" options=$arr_langs selected=$links_category.lang}
		{error_validator field_name="category_id"}
	</td>
  </tr>
    <td colspan="2" align="center"><input type="submit" value="Add"/></td>
  </tr>
</table>
{/form}
