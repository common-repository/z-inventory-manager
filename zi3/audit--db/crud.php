<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit00Db_Crud extends _PW1
{
	public $_;
	public $qb;
	public $sql;

	public function __construct(
		PW1_ZI3_Audit00Db_ $_,
		PW1_Sql_QueryBuilder $qb,
		PW1_Sql_ $sql
	)
	{}

	public function create( array $values )
	{
		$qb = $this->qb->construct();

		$dataValues = $values[ 'event_data' ];
		unset( $values['event_data'] );

		$qb->set( $values );
		$sql = $qb->getCompiledInsert( $this->_->tableName() );
// echo $sql . '<br>';
		$id = $this->sql->query( $sql );

// _print_r( $dataValues );
// exit;

		$dataBatchValues = array();
		foreach( $dataValues as $k => $v ){
			$thisDataValues = array(
				'audit_id'		=> $id,
				'data_name'		=> $k,
				'data_value'	=> $v
				);

			$dataBatchValues[] = $thisDataValues;
		}

		if( $dataBatchValues ){
			$qb = $this->qb->construct();
			$sqls = $qb->getCompiledInsertBatch( $this->_->tableNameData(), $dataBatchValues );
// _print_r( $sqls );
			foreach( $sqls as $sql ){
				$dataId = $this->sql->query( $sql );
			}
		}

		return $id;
	}

	public function read( _PW1_Q $q = NULL )
	{
		$ret = array();

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

	// orderby
		foreach( $q->getOrderBy() as $id => $w ){
			list( $name, $direction ) = $w;
			$name = $this->_convertName( $name );
			$qb->orderBy( $name, $direction );
		}

	// limit
		$qb->limit( $q->getLimit() );
		$qb->offset( $q->getOffset() );

		$sql = $qb->getCompiledSelect( $this->_->tableName() );
		// echo "SQL = $sql";
		// exit;
		$ret = $this->sql->query( $sql );

		if( ! $ret ) return $ret;

	// data
		$ids = array();
		foreach( $ret as $e ) $ids[ $e['id'] ] = (int) $e['id'];

		$qb = $this->qb->construct();
		$qb->where( 'audit_id', $ids );
		$sql = $qb->getCompiledSelect( $this->_->tableNameData() );
		// echo "SQL = $sql";
		// exit;
		$dataRet = $this->sql->query( $sql );
		$dataById = array();
		foreach( $dataRet as $e ){
			$id = $e['audit_id'];
			if( ! isset($dataById[$id]) ) $dataById[$id] = array();
			$dataById[$id][ $e['data_name'] ] = $e['data_value'];
		}

		foreach( $ret as $ii => $e ){
			$ret[ $ii ][ 'event_data' ] = isset( $dataById[$e['id']] ) ? $dataById[$e['id']] : array();
		}

		return $ret;
	}

	public function delete( _PW1_Q $q )
	{
		$where = $q->getWhere();
		if( ! $where ){
			echo __METHOD__ . ': ' . __LINE__ . ': cannot proceed without conditions!<br>';
			return;
		}

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

		$sql = $qb->getCompiledDelete( $this->_->tableName() );
		$ret = $this->sql->query( $sql );

		return $ret;
	}

	private function _qWhere( $qb, _PW1_Q $q )
	{
		$where = $q->getWhere();
		foreach( $where as $id => $w ){
			if( $w instanceof $q ){
				$qb->groupStart();
				$qb = $this->_qWhere( $qb, $w );
				$qb->groupEnd();
			}
			else {
				list( $name, $compare, $value ) = $w;
				$name = $this->_convertName( $name );
				if( 'LIKE' === $compare ){
					$qb->like( $name, $value );
				}
				else {
					$qb->where( $name . $compare, $value );
				}
			}
		}

		$orWhere = $q->getOrWhere();
		foreach( $orWhere as $id => $w ){
			if( $w instanceof $q ){
				$qb->orGroupStart();
				$qb = $this->_qWhere( $qb, $w );
				$qb->groupEnd();
			}
			else {
				list( $name, $compare, $value ) = $w;
				$name = $this->_convertName( $name );
				if( 'LIKE' === $compare ){
					$qb->orLike( $name, $value );
				}
				else {
					$qb->orWhere( $name . $compare, $value );
				}
			}
		}

		return $qb;
	}

	private function _convertName( $objectName = NULL, $dbName = NULL )
	{
		$convert = array();

		$convert[] = array( 'objectClass', 'object_class' );
		$convert[] = array( 'objectId', 'object_id' );
		$convert[] = array( 'eventDateTime', 'event_datetime' );
		$convert[] = array( 'userId', 'user_id' );

		if( NULL === $objectName ){
			$ret = $dbName;
		}
		elseif( NULL === $dbName ){
			$ret = $objectName;
		}

		foreach( $convert as $c ){
			if( NULL === $objectName ){
				if( $c[1] == $dbName ){
					$ret = $c[1];
					break;
				}
			}
			elseif( NULL === $dbName ){
				if( $c[0] == $objectName ){
					$ret = $c[1];
					break;
				}
			}
		}

		return $ret;
	}
}