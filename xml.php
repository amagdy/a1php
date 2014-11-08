<?
require_once("config.php");		/**< include the site configuration that is edited by the coder. */
if (!ENABLE_XML_GATEWAY) exit();
require_once("uploads/configuration/config.php");	/**< include the site configuration that is edited by the site admin by the configuration module. */
require_once("lib/inc.php");
set_error_handler('error_handler');		/**< set the custom error handler function . */

require_once("lib/" . SESSION_METHOD . ".php");

require_once("lib/dispatcher.php");
dispatcher::request();		// handle request

$__out['user_id'] = $_SESSION['user_id'];
$__out['__errors'] = $__errors;		/**< Add the $__errors array to the array of output ($__out) to be shown on the template when displayed. */
$__out['__info'] = $__info;			/**< Add the $__info array to the array of output ($__out) to be shown on the template when displayed. */
//-------------------------------------------------------------
// view handling
require_once("lib/xml.php");
$xml = new xml();
print $xml->array_to_xml($__out);
