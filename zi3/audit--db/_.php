<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit00Db_ extends _PW1
{
	public $sql;

	public function __construct(
		PW1_Sql_ $sql,
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_ZI3_Install_@conf',		__CLASS__ . 'Install@conf' )
			;

	// persistency
		$pw1
			->wrap( 'PW1_ZI3_Audit_Crud@create',	__CLASS__ . 'Crud@create' )
			->wrap( 'PW1_ZI3_Audit_Crud@read',		__CLASS__ . 'Crud@read' )
			->wrap( 'PW1_ZI3_Audit_Crud@delete',	__CLASS__ . 'Crud@delete' )
			;
	}

	public function tableName()
	{
		$ret = 'zi3_audit';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}

	public function tableNameData()
	{
		$ret = 'zi3_audit_data';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}
}