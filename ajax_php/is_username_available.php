<?
// initialization and configuration
require_once(dirname(__FILE__) . "/../config.php");
require_once(PHP_ROOT . "lib/inc.php");

set_error_handler('error_handler');

require_once(PHP_ROOT . "lib/" . SESSION_METHOD . ".php");
if (!session_is_registered('lang')) {
	exit();
}
require_once(PHP_ROOT . "uploads/translation/" . $_SESSION['lang'] . ".php");
require_once(PHP_ROOT . "model/user.php");
$user = new user();
$str_error = $user->is_username_available($_GET['username']);
if ($str_error == "") {
?>
	<b><font size="2" color="#009900"><?=$arr_lang['username_available']?></font></b>
<?
} else {
?>
	<b><font size="2" color="#990000"><?=$arr_lang[$str_error]?></font></b>
<?
}
?>
