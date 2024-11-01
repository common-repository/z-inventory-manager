<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Db_ extends _PW1
{
	private $_loaded = NULL;
	public $sql;

	public function __construct(
		PW1_Sql_ $sql,
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_ZI3_Install_@conf',		__CLASS__ . 'Install@conf' )
			;

		$pw1
			->wrap( 'PW1_ZI3_Install_@getVersion',	__CLASS__ . '@getVersion' )
			->wrap( 'PW1_ZI3_Install_@setVersion',	__CLASS__ . '@setVersion' )
			;
	}

	public function tableName()
	{
		$ret = 'zi3_install';
		$ret = $this->sql->tableName( $ret );
		return $ret;
	}

	public function getVersion( $migrationName )
	{
		if( NULL === $this->_loaded ){
			$this->_loaded = $this->_load();
		}


		$ret = NULL;

		if( isset($this->_loaded[$migrationName]) ){
			$ret = $this->_loaded[$migrationName];
		}

		return $ret;
	}

	public function setVersion( $name, $version )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		if( NULL === $this->_loaded ){
			$this->_loaded = $this->_load();
		}

		if( 'install' == $name && (! $version) ){
			$this->_loaded[ $name ] = $version;
			return call_user_func_array( $context->parentFunc, $args );
		}

		$tableName = $this->self->tableName();

		if( array_key_exists($name, $this->_loaded) ){
			if( $version !== $this->_loaded[$name] ){
				if( $version > 0 ){
					$sql = 'UPDATE `' . $tableName . '` SET version=' . $version . ' WHERE name="' . $name . '"';
				}
				else {
					$sql = 'DELETE FROM `' . $tableName . '` WHERE name="' . $name . '"';
				}
				$this->sql->query( $sql );
			}
		}
		else {
			$sql = 'INSERT INTO `' . $tableName . '` (name, version) VALUES ("' . $name . '", ' . $version . ')';
			$ret = $this->sql->query( $sql );
		}
		$this->_loaded[ $name ] = $version;

		return call_user_func_array( $context->parentFunc, $args );
	}

	private function _load()
	{
		$ret = array();

		$tableName = $this->self->tableName();

		$sql = 'SELECT name, version FROM `' . $tableName . '`';
		$res = $this->sql->query( $sql );

		if( ($res instanceof PW1_Error) OR (! $res) ){
			return $ret;
		}

		foreach( $res as $va ){
			$k = $va['name'];
			$v = $va['version'];
			$ret[$k] = $v;
		}

		return $ret;
	}
}