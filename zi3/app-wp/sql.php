<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App0Wp_Sql extends _PW1
{
	public function query( $sql )
	{
		global $wpdb;

		$isSelect = FALSE;
		$isInsert = FALSE;

		if( preg_match( '/^\s*insert\s/i', $sql ) ){
			$isInsert = TRUE;
		}
		elseif( preg_match( '/^\s*(select|show)\s/i', $sql ) ){
			$isSelect = TRUE;
		}

		if( $isSelect ){
			return $wpdb->get_results( $sql, ARRAY_A );
		}
		else {
			$ret = $wpdb->query( $sql );

			if( FALSE === $ret ){
				$error = array();
				$error[] = 'Database Error';
				$error[] = $wpdb->last_error;
				if( is_admin() ){
					$error[] = $sql;
				}
				$error = join( '<br/>', $error );

				$ret = new PW1_Error( $error );
				return $ret;
				// throw new Exception( $error );
			}

			if( $isInsert ){
				$ret = $wpdb->insert_id;
			}
			return $ret;
		}
	}
}