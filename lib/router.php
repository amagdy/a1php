<?
/**
Used to define routs within the site.
You can add as many routs as you wish to this function but note their order.
@param the requiest_uri server variable or the link to be parsed.
@return an associative array of request parameters.
*/

require_once(PHP_ROOT . "uploads/link/links.php");
require_once(PHP_ROOT . "model/link.php");
/**
reads the url and decides the request of that url.
@param String $arg_str_path the url of the page.
@return Array of current request.
*/
function route ($arg_str_path, $arg_use_friendly_urls=ENABLE_FRIENDLY_URLS) {
	global $__in, $__out, $__links;
	$str_url = strtolower(substr($arg_str_path, strlen(HTML_ROOT))); 
	
	// use friendly Links
	if ($arg_use_friendly_urls) {
		$index = strpos($str_url, "?");
		$url = $str_url;
		if ($index > 0)
			$url = substr($str_url, 0, $index);
		
		if (isset($__links[$_SERVER['HTTP_HOST'] . "/" . $url])) {
			// get data from links table
			$link = new link();
            $__out['__link'] = $link->get_one_by_domain_url($_SERVER['HTTP_HOST'], "/" . $url);
            return $__links[$_SERVER['HTTP_HOST'] . "/" . $url];
		}
	}
	
	$m = array();
	if ($str_url == "admin" || $str_url == "admin/") 
		return array("controller" => "user", "action" => "login");		
	if (preg_match("/^([a-z0-9_]+)\/([a-z0-9_]+)\/([0-9]+)\.html.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => $m[2], "id" => (int)$m[3]);
	if (preg_match("/^([a-z0-9_]+)\/([a-z0-9_]+)\/?.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => $m[2]);
	if (preg_match("/^([a-z0-9_]+)\/?.*$/", $str_url, $m)) 
		return array("controller" => $m[1], "action" => "index");

	return array("controller" => "user", "action" => "login");
}	// end function route().

if(empty($_GET))
{
	$_SERVER['QUERY_STRING'] = preg_replace('#^.*\?#','',$_SERVER['REQUEST_URI']);
	parse_str($_SERVER['QUERY_STRING'], $_GET);
}
$__in = array_merge($_COOKIE, $_GET, $_POST);
$__in = array_merge(route($_SERVER['REQUEST_URI']), $__in);
if ($_FILES) {
	$_FILES = reorder_files_array($_FILES);
	if (!is_array($_FILES)) $_FILES = array();
	$__in = array_merge_recursive($_FILES, $__in);
}

/**< Stop post repetition */
if (ENABLE_POST_REPITITION_STOPPER) {
	if ($_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] && $_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] == $_POST['__POST_REPITITION_STOPPER_TIMESTAMP']){
		$__in['action'] = 'index';
	}
	if($_POST['__POST_REPITITION_STOPPER_TIMESTAMP']) $_SESSION['__POST_REPITITION_STOPPER_TIMESTAMP'] = $_POST['__POST_REPITITION_STOPPER_TIMESTAMP'];
	if ($_GET['t']) {
		if ($_GET['t'] == $_SESSION['t']) {
			$__in['action'] = 'index';
		} else {
			$_SESSION['t'] = $_GET['t'];
		}
	}
}
