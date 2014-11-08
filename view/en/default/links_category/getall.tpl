<script>
{literal}
function confirm_delete(mylink){
	document.getElementById('delyes').href = mylink.href;
	document.getElementById('div_confirm').style.display = '';
	return false;
}
function hide_confirm_delete() {
	document.getElementById('div_confirm').style.display = 'none';
	return false;
}
function confirm_delete_many(){
	document.getElementById('div_confirm_del_many').style.display = '';
	return false;
}
function hide_confirm_delete_many() {
	document.getElementById('div_confirm_del_many').style.display = 'none';
	return false;
}

function select_all_none(bool_all) {
	var arr_checkboxes = document.getElementsByName('arr_ids[]');
	var i;
	for (i=0; i<arr_checkboxes.length; i++) {
		arr_checkboxes[i].checked = bool_all;
	}
	return false;
}
{/literal}
</script>
<div id="div_confirm" style="display:none">
	<table width="100%" cellpadding="4" cellspacing="0" border="0" class="confirm_table">
		<tr>
			<td colspan="2" align="center" class="confirmmsg">
				Confirmation
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				Are you sure you want to delete this links_category?
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="" id="delyes">Yes</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onclick="javascript:return hide_confirm_delete();">No</a>
			</td>		
		</tr>	
	</table>
</div>
<div id="div_confirm_del_many" style="display:none">
	<table width="100%" cellpadding="4" cellspacing="0" border="0" class="confirm_table">
		<tr>
			<td colspan="2" align="center" class="confirmmsg">
				Confirmation
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				Are you sure you want to delete the selected links_categories?
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="#" onclick="javascript:document.getElementById('form1').submit(); return false;">Yes</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete_many();">No</a>
			</td>		
		</tr>	
	</table>
</div>
{make_link controller="links_category" action="delete_many" assign="form_action"}
{form id="form1" action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4" class="getall_table">
  <tr class="table_title">
	<td align="center">Category name</td>
	<td align="center">Language</td>
    <td width="70" align="center">Edit</td>
    <td width="70" align="center">Delete</td>
	<td width="20"></td>
  </tr>
  {section name=links_categoryid loop=$arr_links_categories}
  <tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}"> 
	<td align="center" class="normal_text">{$arr_links_categories[links_categoryid].name}</td>
	<td align="center" class="normal_text">{$arr_links_categories[links_categoryid].lang}</td>
    <td align="center"><a class="mylink" href="{make_link controller="links_category" action="edit" id=$arr_links_categories[links_categoryid].id}"><img border="0" src="{$HTML_ROOT}view/images/icon_edit.png"/> Edit</a></td>
    <td align="center"><a class="mylink" href="{make_link controller="links_category" action="delete" id=$arr_links_categories[links_categoryid].id}" onclick="javascript:return confirm_delete(this);"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> Delete</a></td>
	<td><input type="checkbox" name="arr_ids[]" value="{$arr_links_categories[links_categoryid].id}"/></td>
  </tr>
  {/section}
  <tr>
  	<td colspan="5">
		<table width="100%" border="0">
			<tr>
				<td align="center"><a href="#" class="mylink" onclick="javascript:return select_all_none(true);">Select All</a></td>
				<td align="center"><a href="#" class="mylink" onclick="javascript:return select_all_none(false);">Select None</a></td>
				<td align="right"><a href="#" onclick="javascript:return confirm_delete_many();" class="mylink"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> Delete</a></td>
			</tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td colspan="5">Page: 
		{foreach key=k item=v from=$links_category.__paging_pages}
			{if $v}
			<a href="{make_link controller="links_category" action="getall" array=$v}" class="mylink">{$k}</a>
			{else}
			<b><u><font size="2">{$k}</font></u></b>
			{/if}
		{/foreach}
	</td>
  </tr>
</table>
{/form}
