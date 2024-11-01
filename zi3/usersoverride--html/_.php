<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_UsersOverride00Html_ extends _PW1
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

	// override current user id
		$ret[] = array( '*',		'*',						__CLASS__ . 'Index@currentUserId', -5 );
		$ret[] = array( 'GET',	'*',						__CLASS__ . 'Index@announce', 0, 'overrideannounce' );
		$ret[] = array( 'HEAD', 'admin/users/{id}',	__CLASS__ . 'Admin0Users0Id_Index@head' );

		return $ret;
	}

	public function myParamName()
	{
		$ret = 'u';
		return $ret;
	}
}