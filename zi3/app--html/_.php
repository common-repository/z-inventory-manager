<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;
	}

	public function routes()
	{
		$ret = array();

		$ret[] = array( 'GET',	'*',		__CLASS__ . 'Index@flashdata',	-3 );

	// layout
		// $ret[] = array( 'GET',	'*@css',	__CLASS__ . 'Layout@css' );

		$ret[] = array( 'GET',	'*',		__CLASS__ . 'Layout@',					+3, 'layout' );
		$ret[] = array( 'GET',	'*:*',	__CLASS__ . 'Layout@partialLayout',	+3, 'layout' );

	// print view
		$ret[] = array( '*',		'{*}:print',	'>{1}' );
		$ret[] = array( 'GET',	'*:print',		__CLASS__ . 'Layout@printLayout',	+3, 'layout' );

	// finalize layout
		$ret[] = array( 'GET',	'*',		__CLASS__ . 'Layout@finalize',	+10, 'finalize' );

	// admin
		$ret[] = array( '*',	'',			__CLASS__ . 'Index@*' );

		return $ret;
	}
}