<script>
{literal}
function confirm_delete(mylink){
	document.getElementById('delyes').href = mylink.href;
	document.getElementById('div_confirm').style.display = '';
	return false;
}
function select_unselect_checkbox(checkbox) {
	if (checkbox.checked == true) {
		select_all_none(true);
	} else {
		select_all_none(false);
	}
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
				Are you sure you want to delete this group?
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="" id="delyes">Yes</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete();">No</a>
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
				Are you sure you want to delete the selected groups?
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
{make_link controller="group" action="delete_many" assign="form_action"}
{form id="form1" action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr class="table_title"> 
		
		<td>
			<div align="center">
				Group Name
				<a href="{make_link controller="group" action="getall" __orderby=name __desc="no"}"><img src="{$HTML_ROOT}view/images/up_arrow.png" border="0"/></a>
				<a href="{make_link controller="group" action="getall" __orderby=name __desc="yes"}"><img src="{$HTML_ROOT}view/images/down_arrow.png" border="0"/></a>
			</div>
		</td>
		<td>
			<div align="center">
				Layout
				<a href="{make_link controller="group" action="getall" __orderby=layout __desc="no"}"><img src="{$HTML_ROOT}view/images/up_arrow.png" border="0"/></a>
				<a href="{make_link controller="group" action="getall" __orderby=layout __desc="yes"}"><img src="{$HTML_ROOT}view/images/down_arrow.png" border="0"/></a>
			</div>
		</td>		
                <td>View</td>
                <td>Set Permissions</td>
		<td>Edit</td>
		<td>Delete</td>
		<td width="10%" align="center"><input type="checkbox" onchange="javascript:select_unselect_checkbox(this);"/></td>
	</tr>
	<tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}">
		<td><div align="center">Visitors</div></td>
		<td><div align="center">index</div></td>
                <td><div align="center">-</div></td>
		<td><div align="center"><a class="mylink" href="{make_link controller="group" action="set_permissions" id=0}">Set Permissions</a></div></td>
		<td><div align="center">-</div></td>
		<td><div align="center">-</div></td>
		<td></td>
	</tr>
	{section name=groupid loop=$arr_groups}
	<tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}"> 
		<td><div align="center">{$arr_groups[groupid].name}</div></td>
		<td><div align="center">{$arr_groups[groupid].layout}</div></td>
                <td><div align="center"><a class="mylink" href="{make_link controller="group" action="getone" id=$arr_groups[groupid].id}">View</a></div></td>
		<td><div align="center"><a class="mylink" href="{make_link controller="group" action="set_permissions" id=$arr_groups[groupid].id}">Set Permissions</a></div></td>
                <td><div align="center"><a class="mylink" href="{make_link controller="group" action="edit" id=$arr_groups[groupid].id}"><img border="0" src="{$HTML_ROOT}view/images/icon_edit.png"/> Edit</a></div></td>
		<td><div align="center"><a class="mylink" href="{make_link controller="group" action="delete" id=$arr_groups[groupid].id}" onClick="javascript:return confirm_delete(this);"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> Delete</a></div></td>
		<td width="10%" align="center"><input type="checkbox" name="arr_ids[]" value="{$arr_groups[groupid].id}"/></td>
	</tr>
	{/section}
</table>
<div class="div_text" align="right"><a href="#" onclick="javascript:return confirm_delete_many();" class="mylink"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> Delete</a></div>
<div class="div_text">Page: 
		{foreach key=k item=v from=$group.__paging_pages}
			{if $v}
			<a href="{make_link controller="group" action="getall" __orderby=$__orderby __desc=$__desc array=$v}" class="mylink">{$k}</a>
			{else}
			<b><u><font size="2">{$k}</font></u></b>
			{/if}
		{/foreach}
</div>
{/form}
