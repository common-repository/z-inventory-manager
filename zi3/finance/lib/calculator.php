<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance_Lib_Calculator extends _PW1
{
	public function construct()
	{
		$ret = new _PW1_ZI3_Finance_Lib_Calculator;
		return $ret;
	}
}

class _PW1_ZI3_Finance_Lib_Calculator
{
	private $result = 0;

	public function reset()
	{
		$this->result = 0;
		return $this;
	}

	public function add( $amount )
	{
		$this->result += $amount;
	}

	public function get()
	{
		$ret = $this->result;
		$ret = $ret * 100;

		$test1 = (int) $ret;
		$diff = abs($ret - $test1);
		if( $diff < 0.01 ){
		}
		else {
			$ret = ( $ret > 0 ) ? ceil( $ret ) : floor( $ret );
		}

		$ret = (int) $ret;
		$ret = $ret / 100;

		return $ret;
	}
}