<?php
class PW1_Error
{
	private $msg;

	public function __construct( $msg )
	{
		$this->msg = $msg;
	}

	public function getMessage()
	{
		$ret = $this->msg;

		if( is_array($ret) ){
			$ret = join( ', ', $ret );
		}

		return $ret;
	}
}
