<?
require_once(dirname(__FILE__) . "/../config.php");
header("X-FILENAME: " . $_POST['file']);
if (file_exists(PHP_ROOT . "view/scripts/js/controllers/" . $_POST['file'] . "_controller.js")) {
	require_once(PHP_ROOT . "view/scripts/js/controllers/" . $_POST['file'] . "_controller.js");
}

