<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_ extends _PW1
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

	// ACL
		$ret[] = array( '*',		'admin/settings*',	__CLASS__ . 'Admin0Settings_Acl@check', -1 );

	// ROOT MENU
		$ret[] = array( 'HEAD',	'',				__CLASS__ . 'Index@head' );

	// INDEX
		$ret[] = array( '*',	'admin/settings',			__CLASS__ . 'Admin0Settings_Index@*' );
		$ret[] = array( '*',	'admin/settings/time',	__CLASS__ . 'Admin0Settings0Time_Index@*' );

	// layout
		$ret[] = array( 'GET',	'admin/settings/*',		__CLASS__ . 'Admin0Settings_Index@afterGet', +1 );

		return $ret;
	}
}