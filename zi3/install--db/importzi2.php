<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Db_ImportZi2 extends _PW1
{
	public $itemsDb;
	public $transactionsDb;
	public $settingsDb;
	public $qb;
	public $sql;

	public function __construct(
		PW1_ZI3_Items00Db_ $itemsDb,
		PW1_ZI3_Transactions00Db_ $transactionsDb,
		PW1_ZI3_Settings00Db_ $settingsDb,
		PW1_Sql_QueryBuilder $qb,
		PW1_Sql_ $sql
	)
	{}

	public function getDbPrefix()
	{
		global $wpdb;

		if( is_multisite() ){
			$shareDatabase = FALSE;
			$dbPrefix = $shareDatabase ? $wpdb->base_prefix : $wpdb->prefix;
		}
		else {
			$dbPrefix = $wpdb->prefix;
		}

		return $dbPrefix;
	}

	public function has()
	{
		$ret = FALSE;
		if( ! defined('WPINC') ) return $ret;

		$dbPrefix = $this->self->getDbPrefix();

		$tables = array();
		$sql = 'SHOW tables';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$table = current( $e );
			if( $dbPrefix ){
				$table = substr( $table, strlen($dbPrefix) );
			}

			if( 'zi2_conf' == $table ){
				$ret = TRUE;
				break;
			}
		}

		return $ret;
	}

	public function run()
	{
		$errors = array();

		$dbPrefix = $this->self->getDbPrefix();

	// items
		$items = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_items';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$e2 = array();

			$e2['id'] = $e['id'];
			$e2['title'] = $e['title'];
			$e2['sku'] = $e['sku'];
			$e2['description'] = $e['description'];
			$e2['state'] = $e['status'];

			$items[ $e2['id'] ] = $e2;
		}

		$table = $this->itemsDb->tableName();
		$qb = $this->qb->construct();
		$sqls = $qb->getCompiledInsertBatch( $table, $items );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

	// purchases
		$maxId = 0;
		$transactions = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_purchases';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$e2 = array();

			if( $e['id'] >= $maxId ) $maxId = $e['id'];

			$e2['id'] = $e['id'];
			$e2['refno'] = $e['refno'];
			$e2['state'] = $e['status'];
			$e2['created_date'] = $e['created_date'];
			$e2['description'] = $e['description'];
			$e2['type'] = PW1_ZI3_Transactions_Model::TYPE_PURCHASE;

			$transactions[ $e2['id'] ] = $e2;
		}

		$table = $this->transactionsDb->tableName();
		$qb = $this->qb->construct();
		$sqls = $qb->getCompiledInsertBatch( $table, $transactions );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

	// purchase lines
		$transactionLines = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_purchases_lines';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$e2 = array();

			$e2['transaction_id'] = $e['purchase_id'];
			$e2['item_id'] = $e['item_id'];
			$e2['qty'] = $e['qty'];
			$e2['price'] = $e['price'];

			$transactionLines[] = $e2;
		}

		$table = $this->transactionsDb->tableNameLines();
		$qb = $this->qb->construct();
		$sqls = $qb->getCompiledInsertBatch( $table, $transactionLines );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

	// sales - convert ids
		$nextId = $maxId + 1;
		$oldIdToNewId = array();

		$transactions = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_sales';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$e2 = array();

			$oldIdToNewId[ $e['id'] ] = $nextId;

			$e2['id'] = $nextId;
			$e2['refno'] = $e['refno'];
			$e2['state'] = $e['status'];
			$e2['created_date'] = $e['created_date'];
			$e2['description'] = $e['description'];
			$e2['type'] = PW1_ZI3_Transactions_Model::TYPE_SALE;

			$transactions[ $e2['id'] ] = $e2;

			$nextId++;
		}

		$table = $this->transactionsDb->tableName();
		$qb = $this->qb->construct();
		$sqls = $qb->getCompiledInsertBatch( $table, $transactions );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

	// sale lines - convert ids
		$transactionLines = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_sales_lines';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			if( ! isset($oldIdToNewId[$e['sale_id']]) ) continue;

			$e2 = array();
			$parentId = $oldIdToNewId[$e['sale_id']];

			$e2['transaction_id'] = $parentId;
			$e2['item_id'] = $e['item_id'];
			$e2['qty'] = $e['qty'];
			$e2['price'] = $e['price'];

			$transactionLines[] = $e2;
		}

		$table = $this->transactionsDb->tableNameLines();
		$qb = $this->qb->construct();
		$sqls = $qb->getCompiledInsertBatch( $table, $transactionLines );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

	// convert conf
		$conf = array();
		$sql = 'SELECT * FROM ' . $dbPrefix . 'zi2_conf';
		$res = $this->sql->query( $sql );
		foreach( $res as $e ){
			$conf[ $e['name'] ] = $e['value'];
		}

// _print_r( $conf );

		$newConf = array();
		foreach( $conf as $k => $v ){
			if( 'datetime_date_format' == $k ) $newConf[ $k ] = $v;
			if( 'datetime_time_format' == $k ) $newConf[ $k ] = $v;
			if( 'datetime_week_starts' == $k ) $newConf[ $k ] = $v;
			if( 'finance_price_format_before' == $k ) $newConf[ $k ] = $v;
			if( 'finance_price_format_after' == $k ) $newConf[ $k ] = $v;
			if( 'finance_price_format_number' == $k ){
				list( $v1, $v2 ) = json_decode( $v, TRUE );
				if( ' ' == $v1 ) $v1 = 's';
				$newConf[ 'finance_price_format_number_decpoint' ] = $v1;
				if( ' ' == $v2 ) $v2 = 's';
				$newConf[ 'finance_price_format_number_thousep' ] = $v2;
			}

			if( 'purchases_numbers_auto' == $k ) 			$newConf[ 'transactions_purchase_ref_auto' ] = $v;
			if( 'purchases_numbers_auto_prefix' == $k )	$newConf[ 'transactions_purchase_ref_auto_prefix' ] = $v;
			if( 'purchases_numbers_auto_method' == $k )	$newConf[ 'transactions_purchase_ref_auto_method' ] = $v;

			if( 'sales_numbers_auto' == $k ) 			$newConf[ 'transactions_sale_ref_auto' ] = $v;
			if( 'sales_numbers_auto_prefix' == $k )	$newConf[ 'transactions_sale_ref_auto_prefix' ] = $v;
			if( 'sales_numbers_auto_method' == $k )	$newConf[ 'transactions_sale_ref_auto_method' ] = $v;
		}

		$table = $this->settingsDb->tableName();
		$qb = $this->qb->construct();
		$insertConf = array();
		foreach( $newConf as $k => $v ) $insertConf[] = array( 'name' => $k, 'value' => $v );

		$sqls = $qb->getCompiledInsertBatch( $table, $insertConf );
		foreach( $sqls as $sql ){
			$res = $this->sql->query( $sql );
			if( $res instanceof PW1_Error ) $errors[] = $res;
			// echo "$sql<br>";
		}

		return $errors;
	}
}