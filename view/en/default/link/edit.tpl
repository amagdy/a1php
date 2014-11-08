<script language="javascript">
{literal}
var inputs = 0;

function addContact(){
 inputs++;
    var table = document.getElementById('contacts');
    //table.rows[0].style.display='block';       


    var tr    = document.createElement('TR');
    var td1   = document.createElement('TD');
    var td2   = document.createElement('TD');
    var td3   = document.createElement('TD');
    var inp1  = document.createElement('INPUT');
    var inp2  = document.createElement('INPUT');

    if(inputs>0){
        var img     = document.createElement('IMG');
        img.setAttribute('src', '{/literal}{$HTML_ROOT}{literal}view/images/icon_delete.gif');
        img.onclick = function(){
            removeContact(tr);
            inputs--;
            if(inputs == 0)
            	removeContact(tr1);
        }
        td1.appendChild(img);
    }

    inp1.setAttribute("name", "link[names][]");
    inp2.setAttribute("name", "link[urls][]");
    
    table.appendChild(tr);
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    td2.appendChild(inp1);
    td3.appendChild(inp2);
   
}
function removeContact(tr){
    tr.parentNode.removeChild(tr);
}

function delete_see_also(imgField) {
	var row = imgField.parentNode.parentNode;
	var table = row.parentNode;
	table.removeChild(row);
}
{/literal}
</script>



{make_link controller="link" action="edit" id=$link.id assign="form_action"}
{form action=$form_action}
			<table width="100%" border="0" cellspacing="0" cellpadding="4" class="getall_table">
			  <tr>
				<td class="normal_text">Domain</td>
				<td>
					{html_options name="link[domain_id]" options=$arr_domains selected=$link.domain_id}
					{error_validator field_name="domain_id"}
				</td>
			  </tr>
			  <tr>
				<td class="normal_text">Friendly URL</td>
				<td>
					<input type="text" name="link[friendly_url]" id="link_friendly_url" value="{$link.friendly_url}"/>
					{error_validator field_name="friendly_url"}
				</td>
			  </tr>
			  <tr>
				<td class="normal_text">Real URL</td>
				<td>
					<input type="text" name="link[real_url]" id="link_real_url" value="{$link.real_url}"/>
					{error_validator field_name="real_url"}
				</td>
			  </tr>
			   <tr>
				<td class="normal_text">Title</td>
				<td>
					<input type="text" name="link[title]" id="link_title" value="{$link.title}"/>
					{error_validator field_name="title"}
				</td>
			  </tr>
			  
			   <tr>
				<td class="normal_text">Description</td>
				<td>
					<textarea name="link[description]" id="link_description"/>{$link.description}</textarea>
					{error_validator field_name="description"}
				</td>
			  </tr>
				<tr>
				<td class="normal_text">Key Words</td>
				<td>
					<textarea name="link[keywords]" id="link_keywords"/>{$link.keywords}</textarea>
					{error_validator field_name="keywords"}
				</td>
			  </tr>
			   <tr>
				<td class="normal_text">social bookmarking</td>
				<td>
					<input type="checkbox" name="link[enable_social_bookmarking]" id="enable_social_bookmarking" value="1"{if $link.enable_social_bookmarking eq 1} checked{/if}/>
					{error_validator field_name="enable_social_bookmarking"}
				</td>
			  </tr>
				<tr>
				<td class="normal_text">Parent</td>
				<td>
					<select name="link[parent_id]">
						<option>--- None ---</option>
						{html_options options=$arr_parents selected=$link.parent_id}
					</select>
					{error_validator field_name="parent_id"}
				</td>
			  </tr>
			  
			  <tr>
				<tr>
				<td class="normal_text">Category</td>
				<td>
					<select name="link[category_id]">
						<option>--- None ---</option>
						{html_options options=$arr_categories selected=$link.category_id}
					</select>
					{error_validator field_name="category_id"}
				</td>
			  </tr>
			  
			  <tr>
				<td class="normal_text">Language</td>
				<td>
					{html_options name="link[lang]" options=$arr_langs selected=$link.lang}
					{error_validator field_name="lang"}
				</td>
			  </tr>
			   <tr>
					 <td colspan="2"><a href="javascript:addContact();">Add 'see-also links'</a></td>
				  </tr>
			  <tr>
				<td class="normal_text">See-also links</td>
				<td>
					<table border="0" id="contacts">
					<tr>
						<td align="center"> </td>
						<td align="center">Name</td>
						<td align="center">URL</td>
					</tr>
				{if $arr_see_also_links}
						{section name=id loop=$arr_see_also_links}
						<tr>
							<td align="center" class="normal_text">
									<img border="0" onclick="javascript:return delete_see_also(this);" src="{$HTML_ROOT}view/images/icon_delete.gif"/>
							</td>
							<td align="center" class="normal_text"><input type="text" name="link[names][]" value="{$arr_see_also_links[id].name}"/></td>
							<td align="center" class="normal_text"><input type="text" name="link[urls][]" value="{$arr_see_also_links[id].url}"/></td>
						</tr>
						{/section}
			  {/if}
					</table>
				</td>
			  </tr>
			  <tr><td colspan="2" align="center">
			  <table border="0" id="contacts2">
			</table>
			  </td>
			  </tr>
   <tr> 
    <td colspan="2" align="center">
		<input type="submit" id="submit" value="    Save    ">
	</td>
  </tr>
			</table>

{/form}
