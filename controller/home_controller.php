<?
/**
@file home_controller.php
*/
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/page.php");

class home_controller extends object implements controller
{
//-----------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "showonepage", "id" => 1));
	}	// end function index().
//-----------------------------------------------------	
	function showonepage() {
		global $__in, $__out;
		$page = new page();
		$__out['page'] = $page->get_one_by_id($__in['id']);
		return true;
	}
//-----------------------------------------------------
	function contact_thankyou () {
		return true;
	}
//-----------------------------------------------------
	function contact () {
		global $__in, $__out;
		$page = new page($__in['id']);
		if($page->send_contact_email ($__in['contact']['name'], $__in['contact']['email'], $__in['contact']['subject'], $__in['contact']['message'])) {
			$__out['contact'] = $__in['contact'];
			return dispatcher::redirect(array("action" => "contact_thankyou"));
		} else {
			$__out['contact'] = $__in['contact'];
			return dispatcher::redirect(array("action" => "showonepage", "id" => $__in['id']));
		}
	}
//-----------------------------------------------------
}
