<?
global $__in, $__out, $__config;
require_once(PHP_ROOT . "uploads/category/" . $_SESSION['lang'] . ".php");
$__out['__categories'] = $__categories;

require_once(dirname(__FILE__) . "/../model/user.php");
$user = new user();
try {
$__out['user_info'] = $user->get_one_by_id($_SESSION['user_id']) ;
} catch (Exception $ex) {}

