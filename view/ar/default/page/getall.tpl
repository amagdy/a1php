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
				تأكيد
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				هل أنت متأكد أنك تريد حذف هذه الصفحة؟
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="" id="delyes">نعم</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete();">لا</a>
			</td>		
		</tr>	
	</table>
</div>
<div id="div_confirm_del_many" style="display:none">
	<table width="100%" cellpadding="4" cellspacing="0" border="0" class="confirm_table">
		<tr>
			<td colspan="2" align="center" class="confirmmsg">
				تأكيد
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				هل أنت متأكد أنك تريد حذف الصفحات المختارة؟
			</td>
		</tr>	
		<tr>
			<td align="center">
				<a class="mylink" href="#" onclick="javascript:document.getElementById('form1').submit(); return false;">نعم</a>
			</td>
			<td align="center">
				<a class="mylink" href="#" onClick="javascript:return hide_confirm_delete_many();">لا</a>
			</td>		
		</tr>	
	</table>
</div>
{make_link controller="page" action="delete_many" assign="form_action"}
{form id="form1" action=$form_action}
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  <tr class="table_title"> 
    <td width="80%"><div align="center">العنوان</div></td>
	<td width="80%"><div align="center">اللغة</div></td>
    <td width="10%"><div align="center">تعديل</div></td>
    <td width="10%"><div align="center">حذف</div></td>
	<td width="10%"></td>
  </tr>
  {section name=pageid loop=$arr_pages}
  <tr bgcolor="{cycle values="#EEEEEE,#DDDDDD"}"> 
    <td><div align="center">{$arr_pages[pageid].title}</div></td>
    <td><div align="center">{if $arr_pages[pageid].language eq "ar"}عربى{else}English{/if}</div></td>
    <td><div align="center"><a class="mylink" href="{make_link controller="page" action="edit" id=$arr_pages[pageid].id}">تعديل</a></div></td>
    <td><div align="center">{if $arr_pages[pageid].fixed}-{else}<a class="mylink" href="{make_link controller="page" action="delete" id=$arr_pages[pageid].id}" onClick="javascript:return confirm_delete(this);">حذف</a>{/if}</div></td>
	<td width="10%">{if $arr_pages[pageid].fixed}-{else}<input type="checkbox" name="arr_ids[]" value="{$arr_pages[pageid].id}"/>{/if}</td>
  </tr>
  {/section}
  <tr>
  	<td colspan="5">
		<table width="100%" border="0">
			<tr>
				<td align="center"><a href="#" class="mylink" onclick="javascript:return select_all_none(true);">إختر الكل</a></td>
				<td align="center"><a href="#" class="mylink" onclick="javascript:return select_all_none(false);">إختر لا شىء</a></td>
				<td align="right"><a href="#" onclick="javascript:return confirm_delete_many();" class="mylink"><img border="0" src="{$HTML_ROOT}view/images/icon_delete.gif"/> حذف</a></td>
			</tr>
		</table>
	</td>
  </tr>
  <tr>
  	<td colspan="5">Page: 
		{foreach key=k item=v from=$page.__paging_pages}
			{if $v}
			<a href="{make_link controller="page" action="getall" array=$v}" class="mylink">{$k}</a>
			{else}
			<b><u><font size="2">{$k}</font></u></b>
			{/if}
		{/foreach}
	</td>
  </tr>
</table>
{/form}
