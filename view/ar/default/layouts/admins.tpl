<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Admin Panel</title>

<script language="javascript" src="{$HTML_ROOT}view/scripts/js/menu.js"></script>

<link href="{$HTML_ROOT}view/en/default/images/style.css" rel="stylesheet"/>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8"/>
</head>
<body>
<table width="100%"class="container_table" dir="rtl">
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
					<a href="{make_link controller=$controller action=$action id=$id lang="en"}" class="mylink">English</a>
					<br/>
					<br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('account_div');"><font class="bold_text"><span id="account_div_span">+</span> إدارة حساب مدير الموقع</font></a>
					<div id="account_div" style="margin-left:15px; margin-top:7px; display:none;">
						<a class="mylink" href="{make_link controller="user" action="home"}">صفحتى الرئيسية</a><br/>
						<a class="mylink" href="{make_link controller="user" action="change_password"}">تغيير كلمة السر</a><br/>
						<a class="mylink" href="{make_link controller="user" action="change_info"}">تعديل بيانات مدير الموقع</a><br/>
						<a class="mylink" href="{make_link controller="user" action="logout"}">خروج</a><br/>
					</div>
					
					<br/><br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('site_div');"><font class="bold_text"><span id="site_div_span">+</span> إدارة الموقع</font></a>
					<div id="site_div" style="margin-left:15px; margin-top:7px; display:none;">
						{if $DEBUG}
						<a class="mylink" href="{make_link controller="configuration" action="add"}">Add Configuration</a><br/>
						{/if}
						<a class="mylink" href="{make_link controller="configuration" action="getall"}">التحكم بالمتغيرات</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="page" action="add"}">أضف صفحة</a><br/>
						<a class="mylink" href="{make_link controller="page" action="getall"}">التحكم بالصفحات</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="link" action="add"}">أضف رابط</a><br/>
						<a class="mylink" href="{make_link controller="link" action="getall"}">التحكم بالروابط</a><br/>
						<br/>
						<a class="mylink" href="{make_link controller="domain" action="add"}">أضف نطاق </a><br/>
						<a class="mylink" href="{make_link controller="domain" action="getall"}">التحكم بالنطاقات</a><br/>
					</div>

					<br/><br/>
					<a href="#" style="text-decoration:none" onClick="javascript:return hide_show('user_div');"><font class="bold_text"><span id="user_div_span">+</span> التحكم بالمستخدمين</font></a>
					<div id="user_div" style="margin-left:15px; margin-top:7px; display:none;">	
						<a class="mylink" href="{make_link controller="useradmin" action="add"}">أضف مستخدم</a><br/>
						<a class="mylink" href="{make_link controller="useradmin" action="getall"}">إدارة المستخدمين</a><br/>
						{if $DEBUG}
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
						{/if}
					</div>
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
