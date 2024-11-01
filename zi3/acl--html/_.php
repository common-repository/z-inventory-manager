<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl00Html_ extends _PW1
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

		$ret[] = array( 'HEAD',	'admin/users',		__CLASS__ . 'Admin0Users_Index@head' );

		$ret[] = array( '*',	'admin/users/acl-roles',					__CLASS__ . 'Admin0Acl0Roles_Index@*' );
		$ret[] = array( '*',	'admin/users/acl-roles/new',				__CLASS__ . 'Admin0Acl0Roles0New_Index@*' );
		$ret[] = array( '*',	'admin/users/acl-roles/{id}',				__CLASS__ . 'Admin0Acl0Roles0Id_Index@*' );
		$ret[] = array( '*',	'admin/users/acl-roles/{id}/delete',	__CLASS__ . 'Admin0Acl0Roles0Id0Delete_Index@*' );

		$ret[] = array( 'GET',	'admin/users/{id}',		__CLASS__ . 'Admin0Users0Id_Index0Permissions@get', +1 );

		$ret[] = array( '*',		'profile:permissions',	__CLASS__ . 'Admin0Users0Id_Index0Permissions@*' );
		$ret[] = array( 'GET',	'profile',					__CLASS__ . 'Admin0Users0Id_Index0Permissions@mainGet', +1 );

		return $ret;
	}
}