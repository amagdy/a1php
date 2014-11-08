<?
/**
@file errors_controller.php
*/
require_once(PHP_ROOT . "lib/controller.php");
class errors_controller extends object implements controller
{
//-----------------------------------------------------
	function permission_denied () {
		return true;
	}
//-----------------------------------------------------
	function page_not_found () {
		return true;
	}
//-----------------------------------------------------
	function site_unavailable () {
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "page_not_found"));
	}
//-----------------------------------------------------	
}

