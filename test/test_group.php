<?
require_once(dirname(__FILE__) . "/inc/test.php");
class test_group extends UnitTestCase
{
	public function test_add () {
		$group = &new group();
		$this->expectException("ValidationException");
		$this->assertTrue(is_valid_id($group->add(array("name" => "Group3", "layout" => "index"))));
	}
}
