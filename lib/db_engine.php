<?
interface db_engine {

	/** 
	Public Member: the number of matched rows in the last update command got by mysql_info(). 
	*/
	public function get_matched_rows ();
//---------------------------------------------------	
	public static function &get_connection($arg_str_dbprofile=DEFAULT_DBPROFILE);
//---------------------------------------------------
	/**
	Escapes a string to be put in a query.
	Database Type specific cleanup of sql parameters to secure the site against sql injection.
	@access Public.
	*/
	public function escape ($arg_string);
//---------------------------------------------------
	/**
	Escapes an array of strings to be put in a query.
	used instead of addslashes() but takes care of Mysql recommendations and the current characterset of the current db connection.
	@see check_connect().
	@access Public.
	*/
	public function escape_array(array $arg_arr_string);
//---------------------------------------------------	
	/**
	Inserts a row in the database.
	Inserts a row in the database and returns the last insert id if exists. 
	Can also be used with replace.
	@param arg_str_query string insert statement.
	@return true/false or last insert id.
	@access Public.
	*/
	public function insert($arg_str_query);
//---------------------------------------------------
	/**
	Selects a row or more from database in an array of arrays.
	@param arg_str_query string select statement.
	@return A result array
	@access Public.
	*/
	public function select($arg_str_query);
//---------------------------------------------------
	/**
	Selects one row from database in an array.
	@param $arg_str_query string select statement.
	@return A one row array
	@access Public.
	*/
	public function select_one_row($arg_str_query);
//---------------------------------------------------
	/**
	Updates a row or more.
	Updates a row or more in the database and returns true or false.
	@param arg_str_query string update statement.
	@param arg_bool_return_affected_rows boolean value with the default value of false if true the function returns the number pf affected rows by this update statement.
	@return true or false or affected rows if arg_bool_return_affected_rows = true.
	@access Public.
	*/
	public function update($arg_str_query, $arg_bool_return_affected_rows=false);
//---------------------------------------------------
	/**
	Deletes a row or more.
	Deletes a row or more from the database and returns the affected rows.
	@param arg_str_query string delete statement.
	@return Affected rows.
	@access Public.
	*/
	public function delete($arg_str_query);
//---------------------------------------------------
	/**
	Selects one value.
	Selects one value from db used in things like getting count or maximum or a username.
	@param arg_str_query string select statement.
	@return scalar value.
	@access Public.
	*/
	public function get_one_value($arg_str_query);
//---------------------------------------------------
	/**
	Performs a query on the database and return a result resource
	@param $arg_str_query string query.
	@return the result resource
	@access Public.
	*/
	public function raw_query($arg_str_query);
//---------------------------------------------------
}

