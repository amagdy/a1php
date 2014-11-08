<?
require_once("config.php");		/**< include the site configuration that is edited by the coder. */
require_once("uploads/configuration/config.php");	/**< include the site configuration that is edited by the site admin by the configuration module. */

require_once("lib/inc.php");
set_error_handler('error_handler');		/**< set the custom error handler function the function is in inc.php */

require_once("lib/" . SESSION_METHOD . ".php");
require_once("lib/router.php");

require_once("lib/dispatcher.php");

$__out['lang'] = dispatcher::set_language();
require_once("uploads/translation/" . $__out['lang'] . ".php");
try {
    dispatcher::request();		// handle request
} catch (PermissionDeniedException $ex) {
    if ($_SESSION['group_id'] == 0) {	// visitor
        dispatcher::redirect(array("controller" => "user", "action" => "login"), "please_login_first");
    } else {
        dispatcher::redirect(array("controller" => "errors", "action" => "permission_denied"));
    }
} catch (PageNotFoundException $ex) {
    if (DEBUG) {
        exception_handler($ex);
    } else {
        dispatcher::redirect(array("controller" => "errors", "action" => "page_not_found"));
    }
}catch (Exception $ex) {
    exception_handler($ex);
}

$__out['user_id'] = $_SESSION['user_id'];
$__out['__errors'] = $__errors;				/**< Add the $__errors array to the array of output ($__out) to be shown on the template when displayed. */
$__out['__info'] = $__info;					/**< Add the $__info array to the array of output ($__out) to be shown on the template when displayed. */

//-------------------------------------------------------------
// view handling
require_once("lib/clssmarty.php");
$template = new clssmarty();
$template->display_index();		/**< assign the $__out variables to the template and display the main tpl of the current language and current theme and current group. */
