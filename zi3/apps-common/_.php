<?php
class PW1_ZI3_Apps0Common_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes',	__CLASS__ . '@htmlRoutes' )
			;
	}

	public function htmlRoutes()
	{
		$ret[] = array( 'HEAD',	'admin/settings/about',	__CLASS__ . 'Html_Admin0Settings0About@head' );
		$ret[] = array( 'GET',	'admin/settings/about',	__CLASS__ . 'Html_Admin0Settings0About@get', 0, 'about' );
		return $ret;
	}
}