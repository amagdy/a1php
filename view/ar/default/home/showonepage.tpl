<table width="415" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_01.gif" width="253" height="22"></td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_02.gif" width="8" height="22"></td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_03.gif" width="124" height="22" dir="rtl" style="padding-right:6px;" class="container_title_text">
		{$page.title}
	</td>
	<td background="{$HTML_ROOT}view/ar/default/images/middel_04.jpg" width="30" height="22"></td>
  </tr>
  <tr>
	<td width="415" colspan="4" valign="top" background="{$HTML_ROOT}view/ar/default/images/middel_05.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td dir="rtl" style="padding:6px;" class="normal_text">
			{$page.body}	
		</td>
	  </tr>
	</table></td>
  </tr>
{if $page.contact_email}
  <tr>
	<td colspan="4" dir="rtl">
		<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
			  <tr>
				<td dir="rtl" COLSPAN="3">
					{make_link controller="home" action="contact" id=$page.id assign="form_action"}
					{form action=$form_action}
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td class="bold_text">الاسم: </td>
						<td>
							<input type="text" name="contact[name]" value="{$contact.name}"/>
							{error_validator field_name="name"}
						</td>
					  </tr>
					  <tr>
						<td class="bold_text">البريد الالكترونى: </td>
						<td>
							<input type="text" name="contact[email]" value="{$contact.email}"/>
							{error_validator field_name="email"}
						</td>
					  </tr>
					  <tr>
						<td class="bold_text">عنوان الرسالة: </td>
						<td>
							<input type="text" name="contact[subject]" value="{$contact.subject}"/>
							{error_validator field_name="subject"}
						</td>
					  </tr>
					  <tr>
						<td class="bold_text">نص الرسالة: </td>
						<td>
							<textarea name="contact[message]" rows="8" cols="45">{$contact.message}</textarea>
							{error_validator field_name="message"}
						</td>
					  </tr>
					  <tr>
						<td colspan="2" align="center"><input type="submit" value="إرسال"/></td>
					  </tr>
					</table>
					{/form}
				</td>
			</tr>
		</TABLE>
	</td>
  </tr>
{/if}
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
