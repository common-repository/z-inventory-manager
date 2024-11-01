<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App0Wp00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes',		__CLASS__ . '@Routes' )
			;

		$pw1
			->wrap( 'PW1_App00Html_Csrf@render',	__CLASS__ . 'Csrf@render' )
			->wrap( 'PW1_App00Html_Csrf@check',		__CLASS__ . 'Csrf@check' )
			;
	}

	public function routes()
	{
		$ret = array();

		$ret[] = array( 'GET',	'*',			__CLASS__ . 'Body@', 9, 'body' );
		$ret[] = array( 'GET',	'*:*',		NULL, 9, 'body' );
		$ret[] = array( 'GET',	'*@*',		NULL, 9, 'body' );
		$ret[] = array( 'GET',	'*:print',	__CLASS__ . 'Body@printLayout', 9, 'body' );

		return $ret;
	}
}