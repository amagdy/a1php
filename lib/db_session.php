<?
/**
@file db_session.php
@class db_session.
Needs Config file before it.
This class handles php session to be saved in a db table instead on disk.
*/
require_once(dirname(__FILE__) . "/object.php");
class db_session extends object 
{
	var $db;	/**< Private Member: Database resource */
//----------------------------------------------------------------------
	/**
	Session constructor.
	Sets this class as the session save handler to make php use it as its save method for saving php normal session.
	It also registers session_write_close() as the shutdown function to make sure that session is written before the page closes.
	And it starts session using session_start. so to implement session in any file just require_once this file.
	@access Public.
	*/
	function db_session() {
		$this->object();
		session_set_save_handler	(	array(&$this, 'open'),
										array(&$this, 'close'),
										array(&$this, 'read'),
										array(&$this, 'write'),
										array(&$this, 'destroy'),
										array(&$this, 'gc')
									);
		register_shutdown_function('session_write_close');
		session_start();
	}
//----------------------------------------------------------------------
	/**
	Session storage function (open).
	Opens the database connection and selects a database. It also has 2 parameters that are not used they are just there for the interface.
	@param arg_str_save_path (not used).
	@param arg_str_session_name (not used).
	@return Boolean true/false.
	@see close().
	@access Public.
	*/
	function open($arg_str_save_path, $arg_str_session_name) 
	{
		global $arr_dbprofiles;
		$host = $arr_dbprofiles[DEFAULT_DBPROFILE]['host'];
		$dbname = $arr_dbprofiles[DEFAULT_DBPROFILE]['dbname'];
		$user = $arr_dbprofiles[DEFAULT_DBPROFILE]['user'];
		$pass = $arr_dbprofiles[DEFAULT_DBPROFILE]['pass'];
		
		$link_id = @mysql_connect($host, $user, $pass);
		$this->db = $link_id;		
		$sel_db = @mysql_select_db($dbname, $link_id);
		if (!$link_id || !$sel_db) {
			return false;
		}

		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (close).
	Calls the garbage collector method gc() then closes the database connection.
	@return Boolean true/false.
	@see open().
	@see gc().
	@access Public.
	*/
	function close() 
	{
		$this->gc(SESSION_TIMEOUT);
		@mysql_close($this->db);
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (read).
	Selects the session data from db given the session id.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return The session data as String or an empty string if there is no session data.
	@see write().
	@access Public.
	*/	
	function read($arg_str_session_id) 
	{
		$arg_str_session_id = addslashes($arg_str_session_id);
		$rs = @mysql_query("SELECT `session_data` FROM `sessions` WHERE `sid`='" . $arg_str_session_id . "' AND `last_accessed`>" . (time() - SESSION_TIMEOUT));
		if ($rs) {
			while ($row = mysql_fetch_assoc($rs)) {
				$session_data = stripslashes($row['session_data']);
			}
			@mysql_free_result($rs);
			return $session_data;
		}
		return "";	// if there is no data then return empty string		
	}
//----------------------------------------------------------------------
	/**
	Session storage function (write).
	Writes the session data after the page code has finished to database with the session id as the primary key.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@param arg_str_session_data the session data to be written to database.
	@return Boolean true/false.
	@see read().
	@access Public.
	*/	
	function write($arg_str_session_id, $arg_str_session_data) 
	{
		$arg_str_session_id = addslashes($arg_str_session_id);
		$arg_str_session_data = addslashes($arg_str_session_data);
		@mysql_query("REPLACE INTO `sessions` (`sid`, `last_accessed`, `session_data`) VALUES ('" . $arg_str_session_id . "', " . time() . ", '" . $arg_str_session_data . "')", $this->db);
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (destroy).
	This method is called when the code runs session_destroy(). It deletes the session data with the given session id from database.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return Boolean true/false.
	@see write().
	@access Public.
	*/	
	function destroy($arg_str_session_id) 
	{
		$arg_str_session_id = addslashes($arg_str_session_id);
		@mysql_query("DELETE FROM `sessions` WHERE `sid`='" . $arg_str_session_id . "'", $this->db);
		// return mysql_affected_rows($this->db);
		return true;
	}
//----------------------------------------------------------------------	
	/**
	Session storage function (gc).
	This function is called right before close(). It deletes all session entries that are older than needed to clean db.
	@param arg_int_next_lifetime (not used).
	@return Boolean true/false.
	@see close().
	@access Public.
	*/
	function gc($arg_int_next_lifetime) 
	{
		@mysql_query("DELETE FROM `sessions` WHERE `last_accessed`<" . (time() - SESSION_TIMEOUT), $this->db);
		// return mysql_affected_rows($this->db);
		return true;
	}
//----------------------------------------------------------------------
}	// end class db_session

$obj_session = new db_session(); /**< Create a new instance of the class Session and this is enough to start the new session.*/

