<?
class PermissionDeniedException extends Exception
{
	private $arr_request;	// The not allowed request

	/**
	 * Creates a Validation Exception
	 * @param parent_model $arg_model
	 * @param String $arg_message
	 * @param int $arg_code
	 */
	public function __construct(array $arg_request, $arg_message="Permission Denied Exception", $arg_code=0) {
		parent::__construct($arg_message, $arg_code);
		$this->arr_request = $arg_request;
	}
	
	public function get_request () {
		return $this->arr_request;
	}

}
