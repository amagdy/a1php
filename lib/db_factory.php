<?
require_once(dirname(__FILE__) . "/object.php");
require_once(dirname(__FILE__) . "/db_engine.php");
require_once(dirname(__FILE__) . "/" . DB_ENGINE . ".php");
class db_factory extends object
{
	public static function &get_connection($arg_str_dbprofile=DEFAULT_DBPROFILE, $arg_database_engine=DB_ENGINE) {
		if ($arg_database_engine == "mysql") {
			$db = &mysql::get_connection($arg_str_dbprofile);
		} else if ($arg_database_engine == "mysqli") {
			$db = &mysqli::get_connection($arg_str_dbprofile);
		} else if ($arg_database_engine == "sqlite") {
			$db = &sqlite::get_connection($arg_str_dbprofile);
		}
		return $db;
	}
}

