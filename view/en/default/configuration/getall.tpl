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
				Are you sure you want to delete this configuration?
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
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr class="table_title">
	<td align="center">Description</td>
	<td width="10%" align="center">Edit</td>
	<td width="10%" align="center">Admin Edit</td>
    <td width="10%" align="center">Delete</td>
  </tr>
  {section name=configurationid loop=$arr_configurations}
  <tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}"> 
	<td align="center">{$arr_configurations[configurationid].description}</td>
	<td align="center"><a class="mylink" href="{make_link controller="configuration" action="admin_edit" id=$arr_configurations[configurationid].id}">Admin Edit</a></td>
	<td align="center"><a class="mylink" href="{make_link controller="configuration" action="edit" id=$arr_configurations[configurationid].id}">Edit</a></td>
    <td align="center"><a class="mylink" href="{make_link controller="configuration" action="delete" id=$arr_configurations[configurationid].id}" onclick="javascript:return confirm_delete(this);">Delete</a></td>
  </tr>
  {/section}
</table>
