<?
/**
@file file_session.php
@class file_session.
Needs Config file before it.
This class handles php session to be saved in a file on the site instead on /tmp.
*/
require_once(dirname(__FILE__) . "/object.php");
class file_session extends object
{
	var $sessions_folder;	/**< Private Member: The folder Where session files are kept */
//----------------------------------------------------------------------
	/**
	Session constructor.
	Sets this class as the session save handler to make php use it as its save method for saving php normal session.
	It also registers session_write_close() as the shutdown function to make sure that session is written before the page closes.
	And it starts session using session_start. so to implement session in any file just require_once this file.
	@access Public.
	*/
	function file_session() {
		$this->object();
		$this->sessions_folder = dirname(__FILE__) . "/../sessions/";
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
	@param arg_str_save_path (not used).
	@param arg_str_session_name (not used).
	@return Boolean true/false.
	@see close().
	@access Public.
	*/
	function open($arg_str_save_path, $arg_str_session_name) 
	{
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (close).
	Calls the garbage collector method gc().
	@return Boolean true/false.
	@see open().
	@see gc().
	@access Public.
	*/
	function close() 
	{
		$this->gc(SESSION_TIMEOUT);
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (read).
	Gets the session data from the file named after the session id.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return The session data as String or an empty string if there is no session data.
	@see write().
	@access Public.
	*/	
	function read($arg_str_session_id) 
	{
		$str_session = @file_get_contents($this->sessions_folder . $arg_str_session_id);
		if (!$str_session) {
			return "";
		}
		return $str_session;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (write).
	Writes the session data after the page code has finished to the session file with the session id as the file's name.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@param arg_str_session_data the session data to be written to file.
	@return Boolean true/false.
	@see read().
	@access Public.
	*/	
	function write($arg_str_session_id, $arg_str_session_data) 
	{
		$fh = @fopen($this->sessions_folder . $arg_str_session_id, "w");
		@flock($fh, LOCK_EX);
		@fwrite($fh, $arg_str_session_data);
		@flock($fh, LOCK_UN);
		@fclose($fh);
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (destroy).
	This method is called when the code runs session_destroy(). It deletes the session data with the given session id from disk.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return Boolean true/false.
	@see write().
	@access Public.
	*/	
	function destroy($arg_str_session_id) 
	{
		@unlink ($this->sessions_folder . $arg_str_session_id);
		return true;
	}
//----------------------------------------------------------------------	
	/**
	Session storage function (gc).
	This function is called right before close(). It deletes all session entries that are older than needed to clean disk.
	@param arg_int_next_lifetime (not used).
	@return Boolean true/false.
	@see close().
	@access Public.
	*/
	function gc($arg_int_next_lifetime) 
	{
		$dir_folder = @opendir($this->sessions_folder);		// open the folder 
		while ($file = @readdir($dir_folder)){		// while folder
			$last_modified = @filemtime($this->sessions_folder . $file);
			if ($last_modified < (time() - SESSION_TIMEOUT)) {
				@unlink ($this->sessions_folder . $file);
			}
		}	// end while
		@closedir($dir_folder);							// close the folder
		return true;
	}
//----------------------------------------------------------------------
}	// end class file_session

$obj_session = new file_session(); /**< Create a new instance of the class session and this is enough to start the new session.*/

