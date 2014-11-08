{section loop=$arr_home_pages name=pageid}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="container_table">
  <tr>
    <td class="container_title">{$arr_home_pages[pageid].title}</td>
  </tr>
  <tr>
    <td dir="ltr" class="container_body">{$arr_home_pages[pageid].body}</td>
  </tr>
</table>
<br/>
{/section}
