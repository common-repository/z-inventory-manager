<?php if (! defined('ABSPATH')) exit;
class PW1_Time00Html_ extends _PW1
{
	public $pw1;

	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->wrap( __CLASS__ . 'Widget_Input_Date@grab', __CLASS__ . 'Widget_Input_DateList@grab' )
			->wrap( __CLASS__ . 'Widget_Input_Date@render', __CLASS__ . 'Widget_Input_DateList@render' )
			;
	}
}