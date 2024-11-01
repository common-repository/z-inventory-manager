<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance00Html_ extends _PW1
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

		// $ret[] = array( '*',		'admin/settings/finance',	__CLASS__ . 'Admin0Settings0Finance_Index@*' );

		$ret[] = array( 'HEAD',	'admin/settings',					__CLASS__ . 'Admin0Settings_Index@head' );


		$ret[] = array( 'GET',	'admin/settings@menu',				__CLASS__ . 'Admin0Settings_Index@menu' );

		$ret[] = array( 'GET',	'admin/settings/finance',			__CLASS__ . 'Admin0Settings0Finance_Index@get' );
		$ret[] = array( 'GET',	'admin/settings/finance@title',	__CLASS__ . 'Admin0Settings0Finance_Index@title' );
		$ret[] = array( 'POST',	'admin/settings/finance',			__CLASS__ . 'Admin0Settings0Finance_Index@post' );

		return $ret;
	}
}