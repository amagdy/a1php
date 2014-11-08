{make_link controller="page" action="edit" id=$page.id assign="form_action"}
{form action=$form_action}
<input type="hidden" name="adding" value="{$adding}"/>
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr> 
    <td width="20%">العنوان</td>
    <td width="80%"><input name="page[title]" id="page_title" type="text" value="{$page.title}">{error_validator field_name="title"}</td>
  </tr>
  <tr> 
    <td>نص الصفحة</td>
    <td>{rte name="page[body]"}{$page.body}{/rte}</td>
  </tr>
  <tr> 
    <td>تلخيص</td>
    <td><textarea name="page[summary]" cols="70" rows="8">{$page.summary}</textarea></td>
  </tr>
  <tr> 
    <td>البريد الالكترونى</td>
    <td>
        <input name="page[contact_email]" id="page_contact_email" type="text" value="{$page.contact_email}">{error_validator field_name="contact_email"}
    </td>
  </tr>
  <tr> 
    <td>اللغة</td>
    <td>
		<select name="page[language]" id="page_language">
			<option value="ar"{if $page.language eq "ar"} selected="selected"{/if}>عربى</option>
			<option value="en"{if $page.language eq "en"} selected="selected"{/if}>English</option>
		</select>{error_validator field_name="language"}
    </td>
  </tr>  
  <tr> 
    <td>ترتيب الصفحة</td>
    <td>
        <select name="page[rank]" id="page_rank">
			<option value="0">لا تظهر فى القائمة الرئيسية</option>
			{for assign=ranks start=1 max=$count_pages}
			{foreach from=$ranks item=curr_rank}
			{if $page.rank eq $curr_rank}
				<option value="{$curr_rank}" selected>{$curr_rank}</option>
			{else}
				<option value="{$curr_rank}">{$curr_rank}</option>
			{/if}
			{/foreach}
        </select>
    </td>
  </tr>
  <tr> 
    <td colspan="2" align="center">
		<input type="submit" id="submit" value="    حفظ    ">
	</td>
  </tr>
</table>
{/form}
<iframe src="{$HTML_ROOT}view/scripts/rte/image_selector.php?id={$page.id}&lang={$lang}" width="100%" height="300">

</iframe>
