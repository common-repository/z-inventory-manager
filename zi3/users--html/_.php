<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users00Html_ extends _PW1
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

		$ret[] = array( 'HEAD',	'',	__CLASS__ . 'Index@head' );

	// ACL
		$ret[] = array( '*',	'admin/users*',	__CLASS__ . 'Admin0Users_Acl@check', -1 );

		return $ret;
	}
}