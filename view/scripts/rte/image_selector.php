<?
require_once(dirname(__FILE__) . "/../../../config.php");
require_once(PHP_ROOT . "lib/inc.php");
require_once(PHP_ROOT . "model/page.php");

$page = new page($_GET['id']);
if ($_GET['lang'] == "en") {
	require_once(PHP_ROOT . "uploads/translation/en.php");
}else{
	require_once(PHP_ROOT . "uploads/translation/ar.php");
}

$prefix = $_GET['prefix'];
$arr_controls = array();


if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if ($page->add_picture ($_FILES['pic'])) {
		add_info("file_uploaded");
	}
}
if ($_GET['action']=="del") {
	$page->delete_picture($_GET['image']);
} elseif ($_GET['action']=="setdefault") {
	$page->set_default_picture($_GET['image']);
} elseif ($_GET['action']=="unsetdefault") {
	$page->unset_default_picture();
}
$arr_pix = $page->get_all_pictures();
?>
<html>
	<head>
		<link href="/view/en/default/style.css" rel="stylesheet"/>
		<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=Windows-1256"/>
		<script language="JavaScript">
		<!--
		function check_upload(){	// checks to see if the user has put a file
			if(document.getElementById('pic').value==""){
				return false;
			}else{
				return true;
			}
		}	// end function check_upload
		
		function add_image(str_image, control_name){
				window.parent.addImage(control_name, str_image);
				return false;
		}
		
		
		
		
		function del_pic_check(){
			var reply = window.confirm("<?=$arr_lang['sure_delete']?>");
			if (reply){
				return true;
			}else{
				return false;
			}
		}	// asks the user if he is sure that he wants to delete the picture
		//-->
		</script>
	</head>
	<body>
		<table width="75%"class="container_table">
		<? if ($__info) { 
			while (list($k, $v) = each($__info)) {
		?>
		  <tr>
			<td colspan="2" align="center" class="{$__info[inf].type}msg">
				<?=$arr_lang[$v['info_msg']]?>
			</td>
		  </tr>
		<? }
		}?>
		<? if ($__errors) { 
			while (list($k, $v) = each($__errors)) { 
			?>
		  <tr>
			<td colspan="2" align="center" class="errormsg">
				<?=$arr_lang[$v['error_msg']]?>
			</td>
		  </tr>
		<? }
		}?>
		</table>
	</body>
</html>
<form method="post" action="image_selector.php?id=<?=$_GET['id']?>&lang=<?=$_GET['lang']?>" onSubmit="return check_upload();" enctype="multipart/form-data">
<input type="file" name="pic" id="pic"/>
<input type="submit" value="<?=$arr_lang['upload']?>"/>
</form>
<table width="100%">
<?
if ($arr_pix) {
	while (list($img, $default) = each($arr_pix)) {
?>
	<tr>
		<td rowspan="3"><img src="<?=HTML_ROOT . "uploads/page/" . $img?>"/></td>
		<td><a onClick="return del_pic_check();" class="mylink" href="image_selector.php?id=<?=$_GET['id']?>&action=del&image=<?=$img?>&lang=<?=$_GET['lang']?>"><?=$arr_lang['delete']?></a></td>
	</tr>
	<tr>
		<td><a href="#" class="mylink" onClick="javascript:return add_image('<?=HTML_ROOT . "uploads/page/" . $img?>', 'body');"><?=$arr_lang['insert_image']?></a></td>
	</tr>
	<tr>
		<td>
			<? if (!$default) { ?>
			<a class="mylink" href="image_selector.php?id=<?=$_GET['id']?>&action=setdefault&image=<?=$img?>&lang=<?=$_GET['lang']?>"><?=$arr_lang['set_default_picture']?></a>
			<? } else { ?>
			<a class="mylink" href="image_selector.php?id=<?=$_GET['id']?>&action=unsetdefault&image=<?=$img?>&lang=<?=$_GET['lang']?>"><?=$arr_lang['unset_default_picture']?></a>
			<? } ?>
		</td>
	</tr>	
<?
	}
}
?>
</table>