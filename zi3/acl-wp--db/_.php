<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp00Db_ extends _PW1
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
			->wrap( 'PW1_ZI3_Acl0Wp_Connections_Crud@create',	__CLASS__ . 'Connections_Crud@create' )
			->wrap( 'PW1_ZI3_Acl0Wp_Connections_Crud@read',		__CLASS__ . 'Connections_Crud@read' )
			->wrap( 'PW1_ZI3_Acl0Wp_Connections_Crud@delete',	__CLASS__ . 'Connections_Crud@delete' )
			;
	}

	public function tableNameConnections()
	{
		$ret = 'zi3_acl_wp_connections';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}
}