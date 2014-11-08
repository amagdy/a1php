<?
class ValidationException extends Exception
{
	private $arr_validation_errors;			// the array of all validation errors with their field names
	private $model;
	private $all_models = array();
	/**
	 * Creates a Validation Exception
	 * @param parent_model $arg_model
	 * @param String $arg_message
	 * @param int $arg_code
	 */
	public function __construct(parent_model $arg_model, $arg_message="Validation Exception", $arg_code=0) {
		parent::__construct($arg_message, $arg_code);
		$this->model = $arg_model;
		$this->all_models[] = $arg_model;
		$this->arr_validation_errors = $arg_model->get_and_clear_validation_errors();
	}
	
	public function get_validation_errors () {
		return $this->arr_validation_errors;
	}

	public function add_validation_errors (parent_model $arg_model) {
	    $this->all_models[] = $arg_model;
	    $this->arr_validation_errors = array_merge($this->arr_validation_errors, $arg_model->get_and_clear_validation_errors());
	}

        public function publish_errors () {
            global $__errors;
            $arr_errors = $this->arr_validation_errors;
            $this->arr_validation_errors = array();
            while (list(, $error) = each($arr_errors)) {
                $__errors[] = $error;
            }
        }
        
	public function get_model () {
		return $this->model;
	}

	public function get_all_models () {
		return $this->all_models;
	}

	public function __toString() {
		$str = $this->message . " on Class " . get_class($this->model) . " in file " . $this->file . ":" . $this->line;
		$count = count($this->arr_validation_errors);
		for ($i = 0; $i < $count; $i++) {
			$str .= "\n" . $this->arr_validation_errors[$i]['error_msg'];
			if ($this->arr_validation_errors[$i]['field_name']) $str .= " on field name: " . $this->arr_validation_errors[$i]['field_name'];
		}
		return $str;
	}
}
