<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp00Html_ extends _PW1
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

		$ret[] = array( '*',	'admin/users',			__CLASS__ . 'Admin0Users_Index@*' );
		$ret[] = array( '*',	'admin/users/{id}',	__CLASS__ . 'Admin0Users0Id_Index@*' );

	// profile
		$ret[] = array( 'HEAD',	'',			__CLASS__ . 'Index@head' );
		$ret[] = array( '*',		'profile',	__CLASS__ . 'Profile_Index@*' );

		return $ret;
	}
}