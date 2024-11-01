<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Woo_Crud extends _PW1
{
	public $_;
	public $qb;
	public $sql;

	public function __construct(
		PW1_ZI3_Items00Db_ $_,
		PW1_Sql_QueryBuilder $qb,
		PW1_Sql_ $sql
	)
	{}

	protected function _fromWoo( WC_Product $p )
	{
		$ret = [];

		$ret['id'] = $p->get_id();
		$ret['title'] = $p->get_name();
		$ret['description'] = $p->get_description();
		$ret['status'] = $p->get_status();
		$ret['sku'] = $p->get_sku();
		$ret['default_cost'] = null;
		$ret['default_price'] = $p->get_price();

		return $ret;
	}

	public function read( _PW1_Q $q = null )
	{
		$ret = [];

		$wpArg = [];
		$wpArg['limit'] = -1;
		$wpArg['orderby'] = 'name';
		$wpArg['order'] = 'ASC';

		if( $q ){
			$wpArg = $this->_convertQ( $wpArg, $q );
		}

		$wcProductList = wc_get_products( $wpArg );
		foreach( $wcProductList as $p ){
			$a = $this->_fromWoo( $p );
			$ret[ $a['id'] ] = $a;
		}

		return $ret;
	}

	public function count( _PW1_Q $q = null )
	{
		$ret = 0;

		$wpArg = [];
		if( $q ){
			$wpArg = $this->_convertQ( $wpArg, $q );
		}

		$wpArg['fields'] = 'ID';
		$wpArg['limit'] = -1;
		$wpArg['offset'] = 0;

		$wcProductList = wc_get_products( $wpArg );
		$ret = count( $wcProductList );

		return $ret;
	}

	public function create( array $values )
	{
	}

	public function update( _PW1_Q $q, array $values )
	{
		return;
	}

	public function delete( _PW1_Q $q )
	{
		return;
	}

	public function whereSearch( $s, _PW1_Q $q )
	{
		$q->where( 'title', 'LIKE', $s );
		return $q;
	}

	private function _convertQ( array $wpArg, _PW1_Q $q )
	{
		$where = $q->getWhere();

		foreach( $where as $id => $w ){
			if( $w instanceof $q ){
				$wpArg = array_merge( $wpArg, $this->_convertQ( $wpArg, $w ) );
			}
			else {
				list( $name, $compare, $value ) = $w;
				if( 'LIKE' === $compare ){
					$wpArg['s'] = $value;
				}
				else {
					$k = $name . ' ' . $compare;
					$v = $value;

					if( 'id <>' == $k ){
						$wpExclude = is_array( $v ) ? $v : [ $v ];
						$wpArg[ 'exclude' ] = isset( $wpArg['exclude'] ) ? array_merge( $wpArg['exclude'], $wpExclude ) : $wpExclude;
						// unset( $q['where'][$ii] );
					}

					if( 'id =' == $k ){
						$wpInclude = is_array( $v ) ? $v : [ $v ];
						$wpArg[ 'include' ] = isset( $wpArg['include'] ) ? array_intersect( $wpArg['include'], $wpInclude ) : $wpInclude;
						// unset( $q['where'][$ii] );
					}
				}
			}
		}

		$limit = $q->getLimit();
		$wpArg[ 'limit' ] = $limit;

		$offset = $q->getOffset();
		$wpArg[ 'offset' ] = $offset;

		return $wpArg;
	}
}