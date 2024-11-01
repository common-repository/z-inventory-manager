<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit00Html_ extends _PW1
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

	// set user id for audit models
		$ret[] = array( 'GET,POST',	'*',		__CLASS__ . 'Index@useCurrentUserId', -4, 'audituser' );
		// $ret[] = array( 'GET,POST',	'*@*',	NULL,	1, 'audituser' );

		return $ret;
	}
}