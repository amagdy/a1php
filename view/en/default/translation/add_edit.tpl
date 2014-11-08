{make_link controller="translation" action="add_edit" assign="form_action"}
{form action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" class="getall_table">
  <tr>
    <td class="normal_text">Variable Name</td>
    <td>
			<input type="text" name="translation[key]" id="translation_key" value="{$translation.key}" size="50"/>
			{error_validator field_name="key"}
	</td>
  </tr>
  {foreach from=$available_languages item="lang_info" key="lang"}
  <tr>
    <td class="normal_text">{$lang_info.name} Text</td>
    <td>
			<textarea name="translation[text][{$lang}]" id="translation_{$lang}_text" rows="10" cols="60">{$translation.text[$lang]}</textarea>
	</td>
  </tr>
  {/foreach}
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Save"/></td>
  </tr>
</table>
{/form}
