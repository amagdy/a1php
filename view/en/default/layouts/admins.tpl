<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Admin Panel</title>

<script language="javascript" src="{$HTML_ROOT}view/scripts/js/menu.js"></script>

<link href="{$HTML_ROOT}view/en/default/images/style.css" rel="stylesheet"/>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8"/>
</head>
<body>
<table width="100%"class="container_table">
{if $__info}
{section loop=$__info name=inf}
  <tr>
    <td colspan="2" align="center" class="{$__info[inf].type}msg">
		{$__info[inf].info_msg|TT:$__info[inf].info_params}
	</td>
  </tr>
{/section}  
{/if}
{if $__errors}
{section loop=$__errors name=err}
  <tr>
    <td colspan="2" align="center" class="errormsg">
		{$__errors[err].error_msg|TT:$__errors[err].error_params}
	</td>
  </tr>
{/section}  
{/if}
  <tr>
    <td colspan="2" align="center" class="container_title">.:: {$__page_title} ::.</td>
  </tr>
  <tr>
  	<td valign="top" align="center" width="200">
		<table width="100%" cellpadding="4" cellspacing="0">
			<tr>
				<td>
					<a href="{make_link controller=$controller action=$action id=$id lang="ar"}" class="mylink">عربى</a>
					<br/>
					<br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('account_div');"><font class="bold_text"><span id="account_div_span">+</span> Account management</font></a>
					<div id="account_div" style="margin-left:15px; margin-top:7px; display:none;">
						<a class="mylink" href="{make_link controller="user" action="home"}">Home</a><br/>
						<a class="mylink" href="{make_link controller="user" action="change_password"}">Change Password</a><br/>
						<a class="mylink" href="{make_link controller="user" action="change_info"}">Change User Information</a><br/>
						<a class="mylink" href="{make_link controller="user" action="logout"}">Logout</a><br/>
					</div>

					<br/><br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('site_div');"><font class="bold_text"><span id="site_div_span">+</span> Site Management</font></a>
					<div id="site_div" style="margin-left:15px; margin-top:7px; display:none;">
						<a class="mylink" href="{make_link controller="configuration" action="add"}">Add Configuration</a><br/>
						<a class="mylink" href="{make_link controller="configuration" action="getall"}">Manage Configurations</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="page" action="add"}">Add a Page</a><br/>
						<a class="mylink" href="{make_link controller="page" action="getall"}">Manage Pages</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="link" action="add"}">Add a Link</a><br/>
						<a class="mylink" href="{make_link controller="link" action="getall"}">Manage Links</a><br/>
						<a class="mylink" href="{make_link controller="link" action="getall_category_links"}">Generate Links Files</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="links_category" action="add"}">Add a Links Category</a><br/>
						<a class="mylink" href="{make_link controller="links_category" action="getall"}">Manage Links Categories</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="domain" action="add"}">Add a Domain</a><br/>
						<a class="mylink" href="{make_link controller="domain" action="getall"}">Manage Domains</a><br/>
					</div>

					<br/><br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('user_div');"><font class="bold_text"><span id="user_div_span">+</span> User Management</font></a>
					<div id="user_div" style="margin-left:15px; margin-top:7px; display:none;">	
						<a class="mylink" href="{make_link controller="useradmin" action="add"}">Add User</a><br/>
						<a class="mylink" href="{make_link controller="useradmin" action="getall"}">Manage Users</a><br/>
						<a class="mylink" href="{make_link controller="useradmin" action="search"}">Search for Users</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="group" action="add"}">Add User Group</a><br/>
						<a class="mylink" href="{make_link controller="group" action="getall"}">Manage User Groups</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="permission" action="add"}">Add Permission</a><br/>
						<a class="mylink" href="{make_link controller="permission" action="getall"}">Manage Permissions</a><br/>
					</div>
					<br/><br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('translation_div');"><font class="bold_text"><span id="translation_div_span">+</span> Translation Management</font></a>
					<div id="translation_div" style="margin-left:15px; margin-top:7px; display:none;">
						<a class="mylink" href="{make_link controller="translation" action="getall"}">Manage Translation</a><br/>
						<a class="mylink" href="{make_link controller="translation" action="add_edit"}">Add a Translation Entry</a><br/>
					</div>
					<br/><br/><br/><br/>
					<a class="mylink" href="{$HTML_ROOT}test/"><b>T E S T</b></a><br/>
				</td>
			</tr>
		</table>
	</td>
    <td valign="top">
		{include file=$document}
	</td>
  </tr>
</table>
{literal}
<script language="javascript">
loop_on_divs();
</script>
{/literal}
</body>
</html>
