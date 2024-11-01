<?php
class PW1_ObjectDecorator
{
	private $_src;
	private $_srcName;
	private $_pw1;

	public function __construct( $src, $srcClassName, $pw1 )
	{
		$this->_src = $src;
		$this->_srcName = $srcClassName;
		$this->_pw1 = $pw1;
	}

	public function __call( $name, $args )
	{
		$call = $this->_srcName . '@' . $name;
		array_unshift( $args, $call );
		$ret = call_user_func_array( array($this->_pw1, 'call'), $args );
		if( $ret === $this->_src ){
			$ret = $this;
		}
		return $ret;
	}

	public function __invoke()
	{
		return $this->__call( '', func_get_args() );
	}

	public function __set( $name, $value )
	{
		$this->_src->{$name} = $value;
	}

	public function __get( $name )
	{
		return $this->_src->{$name};
	}
}
