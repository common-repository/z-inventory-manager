<?php
class PW1_Response
{
	public $redirect = NULL;
	public $dispatch = NULL;

	public $formErrors = array();

	public $params = array(); // retain these params if the same slug is called
	public $data = array();

	public $content;
	public $contentType = 'text/html'; // also text/plain

	public $menu = array();
	public $title;
	public $js = array();
	public $css = array();

	private $errors = array();
	private $messages = array();
	private $debugs = array();

	private function __construct()
	{}

	public static function construct()
	{
		return new static;
	}

	public function addError( $msgs )
	{
		if( ! is_array($msgs) ) $msgs = array( $msgs );
		foreach( $msgs as $msg ){
			$this->errors[] = $msg;
		}
		return $this;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function addMessage( $msgs )
	{
		if( ! is_array($msgs) ) $msgs = array( $msgs );
		foreach( $msgs as $msg ){
			$this->messages[] = $msg;
		}
		return $this;
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function addDebug( $msgs )
	{
		if( ! is_array($msgs) ) $msgs = array( $msgs );
		foreach( $msgs as $msg ){
			$this->debugs[] = $msg;
		}
		return $this;
	}

	public function getDebugs()
	{
		return $this->debugs;
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