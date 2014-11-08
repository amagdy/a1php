<?
require_once("config.php");
if (!ENABLE_JSON_GATEWAY) exit();
require_once("uploads/configuration/config.php");
require_once("lib/inc.php");
set_error_handler('error_handler');

require_once("lib/" . SESSION_METHOD . ".php");
$__in = array_merge($_COOKIE, $_GET, $_POST);
require_once("lib/dispatcher.php");
dispatcher::request();		// handle request

$__out['user_id'] = $_SESSION['user_id'];
$__out['__errors'] = $__errors;		
$__out['__info'] = $__info;		
//-------------------------------------------------------------
header('Content-type: application/json'); 

// view handling
require_once("lib/services_json.php");
$services_json = new services_json();
print $services_json->encode($__out);
