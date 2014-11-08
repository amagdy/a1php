<?
require_once(dirname(__FILE__) . "/inc/test.php");
class test_group_controller extends UnitTestCase {
	public function test_getall () {
		global $__in, $__out;
		$__in['controller'] = "group";
		$__in['action'] = "getall";
		$this->expectException("PermissionDeniedException");
		//$_SESSION['group_id'] = 1;
		//$_SESSION['user_id'] = 6;
		dispatcher::request();

		$this->assertTrue($__out['arr_groups']);
	}
}
