<? 
/**
PHP 5
@class db
Database abstract class.
This class manages the raw connection and methods that access the DB. 
*/
require_once(dirname(__FILE__) . "/object.php");
require_once(dirname(__FILE__) . "/db_engine.php");
class mysql extends object implements db_engine
{
	// private
	private $CONN = NULL;				/**< Private Member: The connection identifier of the database */
	private $DB = NULL;				/**< Private Member: Database selected */
	private $str_dbhost;				/**< Private Member: Database server name or IP address */
	private $str_database_name;			/**< Private Member: Database name */
	private $str_dbusername;			/**< Private Member: Database Username */
	private $str_dbpassword;			/**< Private Member: Database Password */
	private $str_dbprofile;				/**< Private Member: Database Profile used only in displaying errors to avoid shown information about the database */
	private $int_matched_rows = 0;			/**< Public Member: the number of matched rows in the last update command got by mysql_info(). */
	private static $arr_db_objects = array();	/**< hash of db objects. */
//---------------------------------------------------------------------------------------------------------
	public static function &get_connection($arg_str_dbprofile=DEFAULT_DBPROFILE) {
		$db_obj = &$arr_db_objects[$arg_str_dbprofile];
		if (!$db_obj) {
			$db_obj = new mysql($arg_str_dbprofile);
			$arr_db_objects[$arg_str_dbprofile] = $db_obj;
		}
		return $db_obj;
	}
//---------------------------------------------------------------------------------------------------------
	/** 
	Public Member: the number of matched rows in the last update command got by mysql_info(). 
	*/
	public function get_matched_rows () {
		return $this->int_matched_rows;
	}
//---------------------------------------------------------------------------------------------------------
	/**
	db private Constructor to create a factory design pattern.
	@param arg_str_dbprofile the database profile from which the database host, databasename, username and password are extracted.
	@see __destruct().
	@access private.
	*/
	private function __construct ($arg_str_dbprofile) {
		global $arr_dbprofiles;
		$this->object();
		if (!$arg_str_dbprofile) throw new Exception("Invalid Database profile");
		$this->str_dbhost = $arr_dbprofiles[$arg_str_dbprofile]['host'];
		$this->str_database_name = $arr_dbprofiles[$arg_str_dbprofile]['dbname'];
		$this->str_dbusername = $arr_dbprofiles[$arg_str_dbprofile]['user'];
		$this->str_dbpassword = $arr_dbprofiles[$arg_str_dbprofile]['pass'];
		$this->str_dbprofile = $arg_str_dbprofile;
	}	// end __construct()
//---------------------------------------------------
	/**
	Close Database connection
	Called when the developer wants to close the connection or when the page closes.
	@see __construct().
	@access Public.
	*/
	public function __destruct() {
		if ($this->CONN) @mysql_close($this->CONN);
	}	// end __destruct()
//---------------------------------------------------
	/**
	connects to the database.
	connects to database and returns true if connected.
	@return A Boolean value.
	@see check_connect().
	@access Private.
	*/	
	private function connect() {
		$this->CONN = @mysql_connect($this->str_dbhost, $this->str_dbusername, $this->str_dbpassword, true, 65536);
		if (!$this->CONN) throw new Exception (mysql_error($this->CONN) . "\nCould Not Connect using DB Profile: ". $this->str_dbprofile);
		$this->DB = @mysql_select_db($this->str_database_name, $this->CONN);
		if (!$this->DB)	throw new Exception (mysql_error($this->CONN) . "\nCould Not Select Database on DB Profile: " . $this->str_dbprofile);
		return true;
	}	// end function connect()
//---------------------------------------------------
	/**
	checks connection.
	Checks to see if there is a connection if not it reconnects to database with the saved settings. 
	This function is called at the beginning of each function that accesses the database in this class.
	@see connect().
	@access Private.
	*/
	private function check_connect() {
		if((!$this->CONN) || (is_null($this->DB))){
			$this->connect();
		}
	}
//---------------------------------------------------
	/**
	Escapes a string to be put in a query.
	Database Type specific cleanup of sql parameters to secure the site against sql injection.
	@access Public.
	*/
	public function escape ($arg_string) {
		if (!get_magic_quotes_gpc()) {
			//$this->check_connect();
			//return mysql_real_escape_string($arg_string, $this->CONN);
			return addslashes($arg_string);
		}
		return $arg_string;
	}
//---------------------------------------------------
	/**
	Escapes an array of strings to be put in a query.
	used instead of addslashes() but takes care of Mysql recommendations and the current characterset of the current db connection.
	@see check_connect().
	@access Public.
	*/
	public function escape_array(array $arg_arr_string)	{
		if (!is_array($arg_arr_string)) return array();
		if (get_magic_quotes_gpc()) return $arg_arr_string;
		//$this->check_connect();
		while (list($key, $value) = each($arg_arr_string)) {
			//$arg_arr_string[$key] = mysql_real_escape_string($value, $this->CONN);
			$arg_arr_string[$key] = addslashes($value);
		}
		reset($arg_arr_string);
		return $arg_arr_string;
	}
//---------------------------------------------------	
	/**
	Inserts a row in the database.
	Inserts a row in the database and returns the last insert id if exists. 
	Can also be used with replace.
	@param arg_str_query string insert statement.
	@return true/false or last insert id.
	@access Public.
	*/
	public function insert($arg_str_query) {
		$this->check_connect();
		if(!$arg_str_query) throw new Exception ("No query to run");
		if (!@mysql_query($arg_str_query, $this->CONN)) throw new Exception (mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ] \nOn DB Profile: " . $this->str_dbprofile);
		$int_insert_id = @mysql_insert_id($this->CONN);
		if ($int_insert_id) {
			return $int_insert_id;	// return the insert id of this query
		} else {
			return true;
		}
	}	// end insert()
//---------------------------------------------------
	/**
	Selects a row or more from database in an array of arrays.
	@param arg_str_query string select statement.
	@return A result array
	@access Public.
	*/
	public function select($arg_str_query) {
		$arr_result = array();	
		$rs_result = $this->raw_query($arg_str_query);
		if ($rs_result) {    // if result
			while($arr_row = mysql_fetch_array($rs_result, MYSQL_ASSOC)){    // while result
				$arr_result[] = $arr_row;
			}    // end while result
			mysql_free_result($rs_result);
			return $arr_result;
        	} else {
			throw new Exception (mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ] on DB Profile: " . $this->str_dbprofile);
		}
	}	// end select()
//---------------------------------------------------
	/**
	Selects one row from database in an array.
	@param $arg_str_query string select statement.
	@return A one row array
	@access Public.
	*/
	public function select_one_row($arg_str_query) {
		$arr_result = array();	
		$rs_result = $this->raw_query($arg_str_query);
		if ($rs_result) {    // if result
			while($arr_row = mysql_fetch_array($rs_result, MYSQL_ASSOC)){    // while result
				$arr_result = $arr_row;
				break;
			}    // end while result
			mysql_free_result($rs_result);
			return $arr_result;
		} else {
			throw new Exception (mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ] on DB Profile: " . $this->str_dbprofile);
		}
	}	// end select()
//---------------------------------------------------
	/**
	Updates a row or more.
	Updates a row or more in the database and returns true or false.
	@param arg_str_query string update statement.
	@param arg_bool_return_affected_rows boolean value with the default value of false if true the function returns the number pf affected rows by this update statement.
	@return true or false or affected rows if arg_bool_return_affected_rows = true.
	@access Public.
	*/
	public function update($arg_str_query, $arg_bool_return_affected_rows=false) {
		$this->check_connect();
		if(!$arg_str_query) {
			throw new Exception("No query to run.");
		}
		if (!@mysql_query($arg_str_query, $this->CONN)) throw new Exception (mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ]");
		$str_info = mysql_info($this->CONN);
		$this->int_matched_rows = trim(substr($str_info, strlen("Rows matched: "), strpos($str_info, "Changed: ")-strlen("Rows matched: ")));

		if($arg_bool_return_affected_rows){
			return mysql_affected_rows($this->CONN);		
		} else {
			return true;
		}
	}	// end function update
//---------------------------------------------------
	/**
	Deletes a row or more.
	Deletes a row or more from the database and returns the affected rows.
	@param arg_str_query string delete statement.
	@return Affected rows.
	@access Public.
	*/
	public function delete($arg_str_query) {
		$this->check_connect();
		if(!$arg_str_query) throw new Exception ("No query to run");
		if(!@mysql_query($arg_str_query, $this->CONN)) throw new Exception (mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ] on DB Profile: " . $this->str_dbprofile);
		return mysql_affected_rows($this->CONN);
	}	// end function delete
//---------------------------------------------------
	/**
	Selects one value.
	Selects one value from db used in things like getting count or maximum or a username.
	@param arg_str_query string select statement.
	@return scalar value.
	@access Public.
	*/
	public function get_one_value($arg_str_query)	{
		$this->check_connect();
		if (!$arg_str_query) throw new Exception ("No query to run");
		$rs_result = @mysql_query($arg_str_query, $this->CONN);
		
		if ($rs_result) {	// if result
			$row = mysql_fetch_array($rs_result, MYSQL_NUM);
			mysql_free_result($rs_result);
			return $row[0];
		} else {	// else if no result
			throw new Exception(mysql_error($this->CONN) . "\nError While Running Query : [ " . $arg_str_query . " ] on DB Profile: " . $this->str_dbprofile);
		}	// end if result
	}	// end get_one_value
//---------------------------------------------------
	/**
	Performs a query on the database and return a result resource
	@param $arg_str_query string query.
	@return the result resource
	@access Public.
	*/
	public function raw_query($arg_str_query) {
		$this->check_connect();
		if (!$arg_str_query) throw new Exception("No query to run");
		return @mysql_query($arg_str_query, $this->CONN);
	}	// end function other
}	// class DB

