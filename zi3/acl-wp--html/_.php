<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp00Html_ extends _PW1
{
	public $pw1;

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

		$ret[] = array( 'HEAD',	'admin/users',	__CLASS__ . 'Admin0Users_Index@head' );

		$ret[] = array( '*',		'admin/users/acl-wp',				__CLASS__ . 'Admin0Users0Acl0Wp_Index@*' );
		$ret[] = array( '*',		'admin/users/acl-wp/newrole',		__CLASS__ . 'Admin0Users0Acl0Wp0NewRole_Index@*' );
		$ret[] = array( '*',		'admin/users/acl-wp/newuser',		__CLASS__ . 'Admin0Users0Acl0Wp0NewUser_Index@*' );

		$ret[] = array( 'GET',	'admin/users/acl-roles/{id}',		__CLASS__ . 'Admin0Users0Acl0Roles0Id_Index0Wp@get' );

		$ret[] = array( '*',		'acl-wp-reset',		__CLASS__ . 'Acl0Wp0Reset_Index@get' );


		return $ret;
	}
}