<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Db_ extends _PW1
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
			->wrap( 'PW1_ZI3_Transactions_Crud@create',	__CLASS__ . 'Crud@create' )
			->wrap( 'PW1_ZI3_Transactions_Crud@read',		__CLASS__ . 'Crud@read' )
			->wrap( 'PW1_ZI3_Transactions_Crud@count',	__CLASS__ . 'Crud@count' )
			->wrap( 'PW1_ZI3_Transactions_Crud@update',	__CLASS__ . 'Crud@update' )
			->wrap( 'PW1_ZI3_Transactions_Crud@delete',	__CLASS__ . 'Crud@delete' )

			->wrap( 'PW1_ZI3_Transactions_Lines_Crud@create',	__CLASS__ . 'Lines_Crud@create' )
			->wrap( 'PW1_ZI3_Transactions_Lines_Crud@read',		__CLASS__ . 'Lines_Crud@read' )
			->wrap( 'PW1_ZI3_Transactions_Lines_Crud@count',	__CLASS__ . 'Lines_Crud@count' )
			->wrap( 'PW1_ZI3_Transactions_Lines_Crud@update',	__CLASS__ . 'Lines_Crud@update' )
			->wrap( 'PW1_ZI3_Transactions_Lines_Crud@delete',	__CLASS__ . 'Lines_Crud@delete' )

			->wrap( 'PW1_ZI3_Transactions_States_Crud@create',	__CLASS__ . 'States_Crud@create' )
			->wrap( 'PW1_ZI3_Transactions_States_Crud@read',	__CLASS__ . 'States_Crud@read' )
			->wrap( 'PW1_ZI3_Transactions_States_Crud@count',	__CLASS__ . 'States_Crud@count' )
			->wrap( 'PW1_ZI3_Transactions_States_Crud@update',	__CLASS__ . 'States_Crud@update' )
			->wrap( 'PW1_ZI3_Transactions_States_Crud@delete',	__CLASS__ . 'States_Crud@delete' )
			;
	}

	public function tableName()
	{
		$ret = 'zi3_transactions';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}

	public function tableNameLines()
	{
		$ret = 'zi3_transactions_lines';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}

	public function tableNameStates()
	{
		$ret = 'zi3_transactions_states';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}
}