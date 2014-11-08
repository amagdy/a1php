<html>
<head>
<title>{$__page_title}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="{$HTML_ROOT}view/ar/default/images/style.css" rel="stylesheet"/>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="10" marginheight="10">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="150" valign="top">
			<a href="{make_link controller=$controller action=$action id=$id lang="ar"}" class="mylink">عربى</a>
			<br/>
			<br/>
			<a href="{make_link controller="ajax" action="app"}" class="mylink">A J A X</a><br/>
			<br/>
			<a href="{make_link}" class="mylink">Home Page</a><br/>
			<a href="{make_link controller="user" action="login"}" class="mylink">Login</a><br/>
			<br/><br/>
			{foreach from=$__categories key=cat_id item=cat_data}
				<b>- {$cat_data.name}</b><br/>
				{foreach from=$cat_data.links key=friendly item=real}
					 &nbsp;  &nbsp;  &nbsp;- <a href="{$friendly}" title="{$real.description}" class="mylink">{$real}</a><br/>
				{/foreach}
			{/foreach}
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
