<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Auth00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes',	__CLASS__ . '@routes' )
			;
	}

	public function routes()
	{
		$ret = array();

	// set current user id
		$ret[] = array( '*',	'*',	__CLASS__ . 'Index@currentUserId', -5, 'currentuser' );
		// $ret[] = array( '*',	'*@*',	NULL, 1, 'currentuser' );

		return $ret;
	}
}