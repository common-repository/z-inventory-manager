<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp_Crud extends _PW1
{
	public function qToWpArgs( _PW1_Q $q, array $args = array() )
	{
		$where = $q->getWhere();
		foreach( $where as $id => $w ){
			if( $w instanceof $q ){
				$args = array_merge( $args, $this->self->qToWpArgs($w, $args) );
			}
			else {
				list( $name, $compare, $v ) = $w;

				switch( $name ){
					case 'role':
						if( ! is_array($v) ) $v = array( $v );
						switch( $compare ){
							case '=':
								$args['role__in'] = $v;
								break;

							case '<>':
								$args['role__not_in'] = $v;
								break;

							default:
								echo __METHOD__ . ": unknown compare: '$compare'<br>";
								$ret = FALSE;
								break;
						}
					break;

					case 'id':
						switch( $compare ){
							case '=':
								if( isset($args['include']) ){
									if( ! is_array($v) ) $v = array( $v );
									if( ! is_array($args['include']) ) $args['include'] = array( $args['include'] );
									$args['include'] = array_intersect( $args['include'], $v );
								}
								else {
									$args['include'] = $v;
								}
								break;

							case '<>':
								$args['exclude'] = $v;
								break;

							default:
								echo __METHOD__ . ": unknown compare: '$compare'<br>";
								$ret = FALSE;
								break;
						}
					break;
				}
			}
		}

		return $args;
	}

	public function read( _PW1_Q $q )
	{
		$ret = array();

		$args = array();
		$args = $this->self->qToWpArgs( $q, $args );

// _print_r( $q );
// _print_r( $args );
// exit;

		foreach( $q->getOrderBy() as $id => $w ){
			list( $name, $direction ) = $w;
			$args['orderby'] = $name;
			$args['order'] = ( 'ASC' == $direction ) ? 'asc' : 'desc';
		}

		$limit = $q->getLimit();
		if( $limit ){
			$args['number'] = $limit;
		}

		$offset = $q->getOffset();
		if( $offset ){
			$args['offset'] = $offset;
		}

// _print_r( $args );

		$results = get_users( $args );

		reset( $results );
		foreach( $results as $wpUser ){
			$arr = $this->self->wpUserToArray( $wpUser );
			$ret[ $arr['id'] ] = $arr;
		}

		return $ret;
	}

	public function wpUserToArray( $wpUser )
	{
		$ret = array();

		$ret['id'] = $wpUser->ID;
		$ret['title'] = $wpUser->display_name;
		$ret['email'] = $wpUser->user_email;
		$ret['username'] = $wpUser->user_login;
		$ret['wp_user'] = $wpUser;

		return $ret;
	}

	public function count( _PW1_Q $q )
	{
		$ret = 0;

		$args = array();
		$args = $this->self->qToArgs( $q, $args );

		$args['number'] = 1;
		$args['count_total'] = TRUE;

		$wpUserQuery = new WP_User_Query( $args );
		$ret = $wpUserQuery->get_total();

		return $ret;
	}
}