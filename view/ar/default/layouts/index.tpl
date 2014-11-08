<html>
<head>
<title>{$__page_title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="{$HTML_ROOT}view/ar/default/images/style.css" rel="stylesheet"/>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="10" marginheight="10">
<table cellpadding="0" cellspacing="0" border="0" width="100%" dir="rtl">
	<tr>
		<td width="150" valign="top">
			<a href="{make_link controller=$controller action=$action id=$id lang="en"}" class="mylink">English</a>
			<br/>
			<br/>
			<a href="{make_link}" class="mylink">الصفحة الرئيسية</a><br/>
			<a href="{make_link controller="user" action="login"}" class="mylink">دخول</a><br/>
		</td>
		<td>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
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
			</table>
			{include file=$document}
		</td>
	</tr>
</table>		
</body>
</html>
