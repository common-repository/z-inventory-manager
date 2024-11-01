<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Db_ extends _PW1
{
	private $_loaded = NULL;
	public $parent;
	public $sql;

	public function __construct(
		PW1_ZI3_Settings_	$parent,
		PW1_Sql_	$sql,
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_ZI3_Install_@conf', __CLASS__ . 'Install@conf' )
			;

		$pw1
			->wrap( 'PW1_ZI3_Settings_@reset',	__CLASS__ . '@wrapReset' )
			->wrap( 'PW1_ZI3_Settings_@get',		__CLASS__ . '@wrapGet' )
			->wrap( 'PW1_ZI3_Settings_@set',		__CLASS__ . '@wrapSet' )
			;
	}

	public function tableName()
	{
		$ret = 'zi3_settings';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}

	public function wrapGet( $name )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		if( NULL === $this->_loaded ){
			$this->_loaded = $this->_load();
		}

		if( array_key_exists($name, $this->_loaded) ){
			$ret = $this->_loaded[ $name ];
			return $ret;
		}

		$ret = call_user_func( $context->parentFunc, $name );
		return $ret;
	}

	public function wrapSet( $name, $value )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		if( NULL === $this->_loaded ){
			$this->_loaded = $this->_load();
		}

		$tableName = $this->self->tableName();

		if( array_key_exists($name, $this->_loaded) ){
			if( $value !== $this->_loaded[$name] ){
				$sql = 'UPDATE `' . $tableName . '` SET value="' . $value . '" WHERE name="' . $name . '"';
				$this->sql->query( $sql );
			}
		}
		else {
			
			$current = $this->parent->get( $name );
			if( $value !== $current ){
				$sql = 'INSERT INTO `' . $tableName . '` (name, value) VALUES ("' . $name . '", "' . $value . '")';
				$ret = $this->sql->query( $sql );
			}
		}
		$this->_loaded[ $name ] = $value;

		return call_user_func( $context->parentFunc, $name, $value );
	}

	public function wrapReset( $name )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		$tableName = $this->self->tableName();

		$sql = 'DELETE FROM `' . $tableName . '` WHERE name="' . $name . '"';
		$res = $this->sql->query( $sql );

		return call_user_func( $context->parentFunc, $name );
	}

	private function _load()
	{
		$ret = array();

		$tableName = $this->self->tableName();

		$sql = 'SELECT name, value FROM `' . $tableName . '`';
		$res = $this->sql->query( $sql );

		if( ($res instanceof PW1_Error) OR (! $res) ){
			return $ret;
		}

		foreach( $res as $va ){
			$k = $va['name'];
			$v = $va['value'];

			// $v2 = json_decode( $v, TRUE );
			// if( JSON_ERROR_NONE == json_last_error() ){
				// $v = $v2;
			// }

			$ret[$k] = $v;
		}

		return $ret;
	}
}