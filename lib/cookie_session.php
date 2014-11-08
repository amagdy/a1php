<?
/**
@file cookie_session.php
@class cookie_session.
Needs Config file before it.
This class handles php session to be saved in a cookie instead on disk.
*/
require_once(dirname(__FILE__) . "/object.php");
class cookie_session extends object
{
//----------------------------------------------------------------------
	/**
	Session constructor.
	Sets this class as the session save handler to make php use it as its save method for saving php normal session.
	It also registers session_write_close() as the shutdown function to make sure that session is written before the page closes.
	And it starts session using session_start. so to implement session in any file just require_once this file.
	@access Public.
	*/
	function cookie_session() {
		$this->object();
		ob_start();
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
	function open($arg_str_save_path, $arg_str_session_name) {
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (close).
	@return Boolean true/false.
	@see open().
	@access Public.
	*/
	function close() 
	{
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (read).
	Selects the session data from cookie and decrypts it given the session id.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return The session data as String or an empty string if there is no session data.
	@see write().
	@access Public.
	*/	
	function read($arg_str_session_id) 
	{
		$cypher = $_COOKIE[$arg_str_session_id];
		$iv = strrev(substr(SESSION_ENCRYPTION_KEY, 0, 8));
		$plain_text = rtrim(mcrypt_cbc(MCRYPT_TRIPLEDES, SESSION_ENCRYPTION_KEY, base64_decode($cypher), MCRYPT_DECRYPT, $iv), "\0");
		return $plain_text;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (write).
	Writes the session data after the page code has finished to the cookie with the session id as the cookie name.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@param arg_str_session_data the session data to be written to cookie.
	@return Boolean true/false.
	@see read().
	@access Public.
	*/	
	function write($arg_str_session_id, $arg_str_session_data) 
	{
		$iv = strrev(substr(SESSION_ENCRYPTION_KEY, 0, 8));
		$cypher = base64_encode(mcrypt_cbc(MCRYPT_TRIPLEDES, SESSION_ENCRYPTION_KEY, $arg_str_session_data, MCRYPT_ENCRYPT, $iv));
		if(COOKIE_DOMAIN) setcookie(session_name(), session_id(), 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL));
		setcookie($arg_str_session_id, $cypher, 0, "/", (COOKIE_DOMAIN ? "." . COOKIE_DOMAIN : NULL));
		ob_end_flush();
		return true;
	}
//----------------------------------------------------------------------
	/**
	Session storage function (destroy).
	This method is called when the code runs session_destroy(). It deletes the session data with the given session id from the cookie.
	@param arg_str_session_id the 32 byte session id supplied by the client.
	@return Boolean true/false.
	@see write().
	@access Public.
	*/	
	function destroy($arg_str_session_id) 
	{
		setcookie($arg_str_session_id, "");
		return true;
	}
//----------------------------------------------------------------------	
	/**
	Session storage function (gc).
	@param arg_int_next_lifetime (not used).
	@return Boolean true/false.
	@access Public.
	*/
	function gc($arg_int_next_lifetime) 
	{
		return true;
	}
//----------------------------------------------------------------------
}	// end class cookie_session

$obj_session = new cookie_session(); /**< Create a new instance of the class Session and this is enough to start the new session.*/

