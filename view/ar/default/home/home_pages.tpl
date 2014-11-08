{section loop=$arr_home_pages name=pageid}
<table width="415" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_01.gif" width="253" height="22"></td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_02.gif" width="8" height="22"></td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_03.gif" width="124" height="22" dir="rtl" style="padding-right:6px;" class="container_title_text">{$arr_home_pages[pageid].title}</td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_04.jpg" width="30" height="22"></td>
  </tr>
  <tr>
	<td width="415" colspan="4" valign="top" background="{$HTML_ROOT}view/ar/default/images/middel_05.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td dir="rtl" style="padding:6px;" class="normal_text">
			{if $arr_home_pages[pageid].pic}<img align="left" src="{$HTML_ROOT}uploads/page/{$arr_home_pages[pageid].pic}" border="0"/>{/if}
			{$arr_home_pages[pageid].summary|nl2br}
		</td>
	  </tr>
	  <tr>
		<td height="25"><a href="{make_link controller="home" action="showonepage" id=$arr_home_pages[pageid].id}"><img src="{$HTML_ROOT}view/ar/default/images/middel_07.jpg" width="58" height="20" border="0"/></a></td>
	  </tr>
	</table></td>
  </tr>
  <tr>
	<td colspan="4"><table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
		  <td background="{$HTML_ROOT}view/ar/default/images/middel_10.gif" width="126" height="6"></td>
		  <td background="{$HTML_ROOT}view/ar/default/images/middel_11.gif" width="140" height="6"></td>
		  <td background="{$HTML_ROOT}view/ar/default/images/middel_12.gif" width="149" height="6"></td>
		</tr>
	</table></td>
  </tr>
</table>
{/section}
