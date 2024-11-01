<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl00Db_ extends _PW1
{
	public $sql;
	public $pw1;

	public function __construct(
		PW1_Sql_	$sql,
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_ZI3_Install_@conf',		__CLASS__ . 'Install@conf' )
			;

	// persistency
		$pw1
			->wrap( 'PW1_ZI3_Acl_Roles_Crud@create',	__CLASS__ . 'Roles_Crud@create' )
			->wrap( 'PW1_ZI3_Acl_Roles_Crud@read',		__CLASS__ . 'Roles_Crud@read' )
			->wrap( 'PW1_ZI3_Acl_Roles_Crud@update',	__CLASS__ . 'Roles_Crud@update' )
			->wrap( 'PW1_ZI3_Acl_Roles_Crud@delete',	__CLASS__ . 'Roles_Crud@delete' )
			;
	}

	public function tableNameRoles()
	{
		$ret = 'zi3_acl_roles';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}
}