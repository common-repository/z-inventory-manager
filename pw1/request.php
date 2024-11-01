<?php
class PW1_Request
{
	public $uri = NULL;

	public $method = 'GET';

	public $slug = '';
	public $args = array(); // dynamically parsed arguments from slug, like id etc
	public $params = array(); // parameters passed in query string
	public $data = array(); // post data

	public $misc = array();
	public $currentUserId;
	public $formErrors = array();
	public $formValues = array();

	private function __construct()
	{}

	public static function construct()
	{
		return new static;
	}

	public static function init( PW1_Uri $uri )
	{
		$ret = new static;

		$ret->uri = $uri;

		$ret->method = PW1_HttpRequest::getMethod();
		$ret->slug = $uri->slug;
		$ret->params = $uri->params;

		if( in_array($ret->method, array('POST', 'PUT', 'PATCH')) ){
			$ret->data = PW1_HttpRequest::getPost();
		}

		return $ret;
	}

	public function __set( $name, $value )
	{
		$msg = 'Invalid property: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}

	public function __get( $name )
	{
		$msg = 'Invalid property: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}
}