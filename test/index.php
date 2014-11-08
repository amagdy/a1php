<?
require_once(dirname(__FILE__) . "/inc/test.php");
class test_all extends TestSuite
{

	public function test_all () 
	{
		$this->TestSuite('All tests');
	        $this->addFile(PHP_ROOT . 'test/test_group.php');
	        $this->addFile(PHP_ROOT . 'test/test_group_controller.php');
	}
	
}

