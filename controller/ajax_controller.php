<?
require_once(PHP_ROOT . "lib/controller.php");
class ajax_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	function app () {
		global $__in, $__out;
		$__out['__layout'] = "ajax";
	}
//------------------------------------------------------------------------------------------------------	
	function test_slow () {
		global $__in, $__out;
		for ($i = 0; $i < 10000000; $i++) {
			$j = $i * ($i/2);
		}
		$__out['some_output'] = "Slow";
	}
//------------------------------------------------------------------------------------------------------	
	function test_fast () {
		global $__in, $__out;
		$__out['some_output'] = "Fast";
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "app"));
	}
//------------------------------------------------------------------------------------------------------	
	function get_cities () {
		global $__in, $__out;
                $countries_cities = array();
                $countries_cities['EG'] = array(1 => "Cairo", 2 => "Alexandria", 3 => "Aswan", 4 => "Luxor");
                $countries_cities['US'] = array(5 => "New York", 6 => "San Francisco", 7 => "Los Angelos");
                $countries_cities['UK'] = array(8 => "London", 9 => "Oxford");
                $countries_cities['FR'] = array(10 => "Paris");
		$__out['data'] = $countries_cities[$__in['id']];
		return true;
	}
//------------------------------------------------------------------------------------------------------
	function get_areas () {
		global $__in, $__out;
                $cities_areas = array();
                $cities_areas[1] = array(1 => "Zamalek", 2 => "Giza", 3 => "Haram", 4 => "6 October");
                $cities_areas[2] = array(5 => "Louran", 6 => "Smouha", 7 => "Sedi Beshr", 8 => "Ibrahimeya", 9 => "Mandara");
		$__out['data'] = $cities_areas[$__in['id']];
		return true;
	}
//------------------------------------------------------------------------------------------------------	
}	// end class ajax_controller
