{make_link controller="group" action="set_permissions" id=$group.id assign="form_action"}
<p align="center">Permissions for User Group [ {$group.name} ]</p>
{form action=$form_action}
{html_checkboxes options=$arr_permissions selected=$selected_permission_ids separator="<br/>" name="group[permissions]"}
<input type="submit" value="Set Permissions"/>
{/form}
