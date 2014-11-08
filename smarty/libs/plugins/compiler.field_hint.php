<?php
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates a client side onfocus hint for the field with the specified id.
@author Ahmed Magdy <admin@a1works.com>.
@param field_id the html tag id.
@param error_text the error text message that is written if there is any error.
*/
function smarty_compiler_field_hint($tag_attrs, &$compiler)
{
	$_params = $compiler->_parse_attrs($tag_attrs);
	
	if (!isset($_params['field_id'])) {
		$compiler->_syntax_error("field_hint: missing 'field_id' parameter", E_USER_WARNING);
	        return;
	}	
	if (!isset($_params['hint'])) {
		$compiler->_syntax_error("field_hint: missing 'hint' parameter", E_USER_WARNING);
	        return;
	}
	$output = "<span  id=\"<?=$_params[field_id]?>_span\" class='hint'></span>";
	$output .= "
<script language='javascript'>
		
	document.getElementById(\"<?=$_params[field_id]?>\").onfocus = function () {
		";
	$output .= "
	document.getElementById(\"<?=$_params[field_id]?>_span\").innerHTML =\"<b><?=$_params[hint]?></b>\";	
	";
	$output .= "
	};
	
	
	document.getElementById(\"<?=$_params[field_id]?>\").onblur = function () {
		";
	$output .= "
	document.getElementById(\"<?=$_params[field_id]?>_span\").innerHTML =\"\";	
	";
	
	$output .= "
	};
	

</script>
	";


    return "?>" . $output . "<?";
}

/* vim: set expandtab: */

?>
<font
