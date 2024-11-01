<?php
class _PW1_Q
{
	const TYPE_WHERE		= 'WHERE';
	const TYPE_ORWHERE	= 'ORWHERE';
	const TYPE_SELECT		= 'SELECT';
	const TYPE_LIMIT		= 'LIMIT';
	const TYPE_OFFSET		= 'OFFSET';
	const TYPE_ORDERBY	= 'ORDERBY';

	private $qs = array();

	private function _getByType( $type )
	{
		$ret = array();
		foreach( $this->qs as $id => $q ){
			if( $type !== $q[0] ) continue;
			// array_shift( $q );
			$ret[ $id ] = $q[1];
		}
		return $ret;
	}

	public function isWhereSet( $name )
	{
		$ret = FALSE;

		foreach( $this->qs as $q ){
			if( ! in_array($q[0], array(static::TYPE_WHERE, static::TYPE_ORWHERE) ) ) continue;
			if( is_array($q[1]) ){
				$thisRet = ( $name === $q[1][0] );
			}
			else {
				$thisRet = $q[1]->isWhereSet( $name );
			}
			if( $thisRet ){
				$ret = TRUE;
				break;
			}
		}

		return $ret;
	}

	public function where( $name, $compare = NULL, $value = NULL )
	{
		// if( (NULL === $value) && (NULL !== $compare) ){
			// $value = $compare;
			// $compare = '=';
		// }

		if( NULL === $compare ){
			$this->qs[] = array( static::TYPE_WHERE, $name );
		}
		else {
			$this->qs[] = array( static::TYPE_WHERE, array($name, $compare, $value) );
		}
		return $this;
	}

	public function getWhere()
	{
		return $this->_getByType( static::TYPE_WHERE );
	}

	public function orWhere( $name, $compare = NULL, $value = NULL )
	{
		if( NULL === $compare ){
			$this->qs[] = array( static::TYPE_ORWHERE, $name );
		}
		else {
			$this->qs[] = array( static::TYPE_ORWHERE, array($name, $compare, $value) );
		}
		return $this;
	}

	public function getOrWhere()
	{
		return $this->_getByType( static::TYPE_ORWHERE );
	}

	public function orderBy( $name, $direction = 'ASC' )
	{
		$this->qs[] = array( static::TYPE_ORDERBY, array($name, $direction) );
		return $this;
	}

	public function getOrderBy()
	{
		return $this->_getByType( static::TYPE_ORDERBY );
	}

	public function limit( $qty )
	{
		$this->qs[] = array( static::TYPE_LIMIT, $qty );
		return $this;
	}

	public function getLimit()
	{
		$ret = $this->_getByType( static::TYPE_LIMIT );
		$ret = $ret ? array_shift( $ret ) : NULL;
		return $ret;
	}

	public function offset( $qty )
	{
		$this->qs[] = array( static::TYPE_OFFSET, $qty );
		return $this;
	}

	public function getOffset()
	{
		$ret = $this->_getByType( static::TYPE_OFFSET );
		$ret = $ret ? array_shift( $ret ) : NULL;
		return $ret;
	}

	public function select( $name )
	{
		$this->qs[] = array( static::TYPE_SELECT, $name );
		return $this;
	}

	public function getSelect()
	{
		return $this->_getByType( static::TYPE_SELECT );
	}

	public function delete( $id )
	{
		unset( $this->qs[$id] );
		return $this;
	}
}

class PW1_Q extends _PW1
{
	public function construct()
	{
		$class = '_' . __CLASS__;
		$ret = new $class;
		return $ret;
	}

	public function filter( array $objects, _PW1_Q $q )
	{
		return $this->filterObjects( $objects, $q );
	}

	public function filterObjects( array $objects, _PW1_Q $q )
	{
		$ret = array();
		foreach( $objects as $id => $obj ){
			if( ! $this->self->checkObject($obj, $q) ) continue;
			$ret[ $id ] = $obj;
		}
		return $ret;
	}

	public function checkObject( $obj, _PW1_Q $q )
	{
	// AND GROUP
		$ret = TRUE;
		$where = $q->getWhere();

		foreach( $where as $id => $w ){
			if( $w instanceof $q ){
				$thisRet = $this->self->checkObject( $obj, $w );
			}
			else {
				$thisRet = $this->self->checkObjectOne( $obj, $w );
			}

			if( ! $thisRet ){
				$ret = FALSE;
				break;
			}
		}

		if( ! $ret ) return $ret;

	// OR GROUP
		$orWhere = $q->getOrWhere();

		if( $orWhere ){
			$ret = FALSE;
		}

		foreach( $orWhere as $id => $w ){
			if( $w instanceof $q ){
				$thisRet = $this->self->checkObject( $obj, $w );
			}
			else {
				$thisRet = $this->self->checkObjectOne( $obj, $w );
			}

			if( $thisRet ){
				$ret = TRUE;
				break;
			}
		}

		return $ret;
	}

	public function checkObjectOne( $obj, $cond )
	{
		$ret = FALSE;
		list( $k, $compare, $to ) = $cond;

		// if( ! property_exists($obj, $k) ){
			// echo __METHOD__ . ': ' . __LINE__ . ": unknown object property: '$k'<br>";
			// $ret = FALSE;
			// return $ret;
		// }
		// $v = $obj->{$k};

		// $v = property_exists( $obj, $k ) ? $v = $obj->{$k} : $k;

		if( ! property_exists($obj, $k) ){
			$ret = TRUE;
			return $ret;
		}

		$v = $obj->{$k};

		switch( $compare ){
			case '=':
				if( is_array($to) ){
					$ret = in_array( $v, $to );
				}
				else {
					$ret = ( $v == $to );
				}
				break;

			case '<>':
				if( is_array($to) ){
					$ret = ! in_array( $v, $to );
				}
				else {
					$ret = ( $v != $to );
				}
				break;

			case '>':
				$ret = ( $v > $to );
				break;

			case '<':
				$ret = ( $v < $to );
				break;

			case 'LIKE':
				$ret = ( FALSE === strpos($v, $to) ) ? FALSE : TRUE;
				break;

			default:
				echo __METHOD__ . ": unknown compare: '$compare'<br>";
				$ret = FALSE;
				break;
		}

		return $ret;
	}

	public function __call( $name, $args )
	{
		$ret = $this->self->construct();
		return call_user_func_array( array($ret, $name), $args );
	}
}