<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl_ extends _PW1
{
	private $_defaults = array();
	private $_labels = array();
	private $_automatics = array();

// get permission on/off, $ret is preliminary, can be changed by automatics
	public function finalizePermissions( array $ret )
	{
		$catalog = $this->self->catalog();

	// defaults
		reset( $catalog );
		foreach( $catalog as $k ){
			if( ! array_key_exists($k, $ret) ){
				$ret[ $k ] = $this->self->getDefault( $k );
			}
		}

	// process automatics
		$automatics = $this->_automatics;

		reset( $automatics );
		foreach( $automatics as $a ){
			list( $perm1, $if, $perm2, $then ) = $a;
			$v1 = $ret[ $perm1 ];
			if( ($if && $v1) OR (!$if && !$v1) ){
				$ret[ $perm2 ] = $then;
			}
		}

		return $ret;
	}

	public function register( $permissionName, $default, $label = NULL )
	{
		if( NULL === $label ) $label = $permissionName;
		$this->_defaults[ $permissionName ] = $default;
		$this->_labels[ $permissionName ] = $label;

		return $this;
	}

	public function catalog()
	{
		$ret = array_keys( $this->_defaults );
		return $ret;
	}

	public function getLabel( $permissionName )
	{
		$ret = NULL;

		if( ! $this->isRegistered($permissionName) ){
			echo __CLASS__ . ": '$permissionName' not registered<br/>";
			return $ret;
		}

		$ret = $this->_labels[ $permissionName ];
		return $ret;
	}

	public function getDefault( $permissionName )
	{
		$ret = NULL;

		if( ! $this->isRegistered($permissionName) ){
			echo __CLASS__ . ": '$permissionName' not registered<br/>";
			return $ret;
		}

		$ret = $this->_defaults[ $permissionName ];
		return $ret;
	}

	public function isRegistered( $permissionName )
	{
		return array_key_exists( $permissionName, $this->_defaults );
	}

	public function ifOn( $permissionName )
	{
		if( ! $this->isRegistered($permissionName) ) echo __CLASS__ . ": '$permissionName' not registered<br/>";
		$this->_automatics[] = array( $permissionName, TRUE );
		return $this;
	}

	public function ifOff( $permissionName )
	{
		if( ! $this->isRegistered($permissionName) ) echo __CLASS__ . ": '$permissionName' not registered<br/>";
		$this->_automatics[] = array( $permissionName, FALSE );
		return $this;
	}

	public function thenOn( $permissionName )
	{
		if( ! $this->isRegistered($permissionName) ) echo __CLASS__ . ": '$permissionName' not registered<br/>";
		$lastIndex = count( $this->_automatics ) - 1;
		$this->_automatics[ $lastIndex ][] = $permissionName;
		$this->_automatics[ $lastIndex ][] = TRUE;
		return $this;
	}

	public function thenOff( $permissionName )
	{
		if( ! $this->isRegistered($permissionName) ) echo __CLASS__ . ": '$permissionName' not registered<br/>";
		$lastIndex = count( $this->_automatics ) - 1;
		$this->_automatics[ $lastIndex ][] = $permissionName;
		$this->_automatics[ $lastIndex ][] = FALSE;
		return $this;
	}
}