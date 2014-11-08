<?
/**
@file http.php
@class http
This class wraps the cURL web interaction between the web application and any other application outside the system boundaries.
It uses the 2 main request methods (GET, POST)
*/
class http {
//----------------------------------------------------------------
	var $response_text;
	var $arr_headers;
	var $content;
	var $errno;
	var $errormsg;
	var $ssl_version = 3;
//----------------------------------------------------------------
	/** Contructor */
	function http () {}
//----------------------------------------------------------------	
	/**
	
	@access private
	*/
	function prepare_curl_headers ($arg_url) {
		// reset properties
		$this->response_text = "";
		$this->arr_headers = array();
		$this->content = "";
		$this->errno = 0;
		$this->errormsg = "";
		
		$matches = array();
		if(!preg_match('/^http(s)?:\/\/([a-zA-Z0-9_\.-]+)(:[0-9]{1,5})?(\/.*)?$/i', $arg_url, $matches)){
			// error url not valid
			return array();
		}
		$https = $matches[1];
		$domain_name = $matches[2];
		$cookie_file_name = PHP_ROOT . "uploads/curl_cookies/" . $domain_name;
		if (!file_exists($cookie_file_name)) {
			$fh = fopen($cookie_file_name, "w");
			fclose($fh);
		}
		// prepare curl options
		$arr_options = array(
			CURLOPT_URL		=> $arg_url,
			CURLOPT_RETURNTRANSFER 	=> 1,    // return web page
			CURLOPT_HEADER         	=> 1,	// return headers
			CURLOPT_FOLLOWLOCATION 	=> true,    // follow redirects
			CURLOPT_ENCODING       	=> "",      // handle all encodings
			CURLOPT_USERAGENT      	=> "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",// who am i
			CURLOPT_CONNECTTIMEOUT 	=> 120,     // timeout on connect
			CURLOPT_TIMEOUT        	=> 120,     // timeout on response
			CURLOPT_MAXREDIRS      	=> 10,      // stop after 10 redirects
			CURLOPT_SSLVERSION	=> $this->ssl_version,	// SSL version
			CURLOPT_COOKIEFILE => PHP_ROOT . "uploads/curl_cookies/" . $domain_name,
			CURLOPT_COOKIEJAR => PHP_ROOT . "uploads/curl_cookies/" . $domain_name
		);
		if ($https) {
			$arr_options[CURLOPT_SSL_VERIFYPEER] = false;
			$arr_options[CURLOPT_SSL_VERIFYHOST] = 2;
		}
		return $arr_options;
	}	
//----------------------------------------------------------------
	/**
	get HTTP request
	performs a get request and returns the content text of the response
	@param $arg_url the url that will be accessed using the HTTP (may contain the parameters or may take the paramters from $arg_arr_params)
	@param $arg_arr_params [optional] This array is an associative array that will be url encoded and added to the url.
	@param $arg_arr_request_headers [optional] This is an associative array of extra request headers in case the server will use these headers.
	@access public
	*/
	function get ($arg_url, $arg_arr_params=array(), $arg_arr_request_headers=array()) {
		$arr_req_headers = array();
		if (is_array($arg_arr_request_headers)) {
			// reformat request headers
			while (list($k, $v) = each($arg_arr_request_headers)) {
				$arr_req_headers[] = $k . ": " . $v;
			}
		}
		// run private prepare_curl_headers
		$arr_options = $this->prepare_curl_headers($arg_url);
		if (!$arr_options) return "";
		if ($arr_req_headers) $arr_options[CURLOPT_HTTPHEADER] = $arr_req_headers;
		
		
		// build (get) url
		$str_params = "";
		$i = 0;
		if ($arg_arr_params && is_array($arg_arr_params)) {
			while (list($k, $v) = each($arg_arr_params)) {
				$str_params .= ($i > 0 ? "&" : "") . urlencode($k) . "=" . urlencode($v);
				$i++;
			}
			$arg_url .= (ereg('\?.+$', $arg_url) ? "&" : "?") . $str_params;
		}
		
		// make curl request
		$this->run_curl($arg_url, $arr_options);

		// get headers
		$arr_text = split("\n", $this->response_text);
		$bool_headers_begun = false;
		while (list(, $line) = each($arr_text)) {
			$line = trim($line);
			if ($bool_headers_begun == false) {
				if (strpos($line, 'HTTP/1.1 20') !== false || strpos($line, 'HTTP/1.0 20') !== false) {
					$bool_headers_begun = true;
				}
			} else {	// if the headers began
				if ($line == "") break;
				// these should be headers
				$int_colon_position = strpos($line, ": ");
				$this->arr_headers[substr($line, 0, $int_colon_position)] = substr($line, $int_colon_position + 2);
    		}
		}
		
		// get content
		while (list(, $line) = each($arr_text)) {
			$this->content .= $line . "\n";
		}
		return $this->content;
	}
//----------------------------------------------------------------	
	/**
	Runs a cURL reuqest and fills the object members
	
	@access private
	*/
	function run_curl ($arg_url, $arg_arr_options=array()) {
		// make curl request
		$ch = curl_init();
		curl_setopt_array($ch, $arg_arr_options);
		$this->response_text = curl_exec($ch);
		$this->errno = curl_errno($ch);
		$this->errormsg = curl_error($ch);
		curl_close($ch);
	}
//----------------------------------------------------------------
	/**
	post HTTP request
	performs a post request and returns the content text of the response
	@param $arg_url the url that will be accessed using the HTTP (may contain the parameters or may take the paramters from $arg_arr_params)
	@param $arg_arr_params [optional] This array is an associative array that will be url encoded and added to the url.
	@param $arg_arr_request_headers [optional] This is an associative array of extra request headers in case the server will use these headers.
	@access public
	*/
	function post ($arg_url, $arg_arr_params=array(), $arg_arr_request_headers=array()) {
		$arr_req_headers = array();
		if (is_array($arg_arr_request_headers)) {
			// reformat request headers
			while (list($k, $v) = each($arg_arr_request_headers)) {
				$arr_req_headers[] = $k . ": " . $v;
			}
		}
		// run private prepare_curl_headers
		$arr_options = $this->prepare_curl_headers($arg_url);
		if (!$arr_options) return "";
		if ($arr_req_headers) $arr_options[CURLOPT_HTTPHEADER] = $arr_req_headers;
		
		
		// build (get) url
		$str_params = "";
		$i = 0;
		if ($arg_arr_params && is_array($arg_arr_params)) {
			while (list($k, $v) = each($arg_arr_params)) {
				$str_params .= ($i > 0 ? "&" : "") . urlencode($k) . "=" . urlencode($v);
				$i++;
			}
		}
		$arr_options[CURLOPT_POST] = 1;
		$arr_options[CURLOPT_POSTFIELDS] = $str_params;
		
		// make curl request
		$this->run_curl($arg_url, $arr_options);

		// get headers
		$arr_text = split("\n", $this->response_text);
		$bool_headers_begun = false;
		while (list(, $line) = each($arr_text)) {
			$line = trim($line);
			if ($bool_headers_begun == false) {
				if (strpos($line, 'HTTP/1.1 200 OK') !== false || strpos($line, 'HTTP/1.0 200 OK') !== false) {
					$bool_headers_begun = true;
				}
			} else {	// if the headers began
				if ($line == "") break;
				// these should be headers
				$int_colon_position = strpos($line, ": ");
				$this->arr_headers[substr($line, 0, $int_colon_position)] = substr($line, $int_colon_position + 2);
    			}
		}
		
		// get content
		while (list(, $line) = each($arr_text)) {
			$this->content .= $line . "\n";
		}
		return $this->content;
	}
//----------------------------------------------------------------
	function get_all_headers () {
		return $this->arr_headers;
	}
//----------------------------------------------------------------
	function get_header ($arg_str_header_name) {
		return $this->arr_headers[$arg_str_header_name];
	}
//----------------------------------------------------------------
}

