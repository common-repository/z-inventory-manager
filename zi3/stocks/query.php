<?php
class PW1_ZI3_Stocks_Query extends _PW1
{
	private $_cache = NULL;
	public $q;
	public $stocksModel;
	public $transactionsQuery;
	public $transactionLinesQuery;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Stocks_Model $stocksModel,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery
	)
	{}

	public function find()
	{
		$ret = array();

		if( NULL !== $this->_cache ) return $this->_cache;

		$limit = 100;
		$offset = 0;
		do {
			$q = $this->q->construct()
				->limit( $limit )
				->offset( $offset )
			;

			$offset += $limit;
			$transactionLines = $this->transactionLinesQuery->find( $q );
			
			$transactionIds = array();
			foreach( $transactionLines as $e ) $transactionIds[ $e->transaction_id ] = $e->transaction_id;

			if( ! $transactionIds ) continue;

			$q2 = $this->q->construct()
				->where( 'id', '=', $transactionIds )
				->where( 'state', '=', PW1_ZI3_Transactions_Model::STATE_ISSUED )
			;
			$transactions = $this->transactionsQuery->find( $q2 );

			if( ! $transactions ) continue;

			foreach( $transactionLines as $line ){
				if( ! isset($transactions[$line->transaction_id]) ) continue;
				$transaction = $transactions[ $line->transaction_id ];

				$key = $line->item_id;
				if( isset($ret[$key]) ){
					$stocksModel = $ret[ $key ];
				}
				else {
					$stocksModel = $this->stocksModel->construct();
					$stocksModel->item_id = $line->item_id;
				}

				if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $transaction->type ){
					$stocksModel->qty += $line->qty;
				}
				if( PW1_ZI3_Transactions_Model::TYPE_SALE == $transaction->type ){
					$stocksModel->qty -= $line->qty;
				}

				$ret[ $key ] = $stocksModel;
			}

		} while( $transactionLines );

		$this->_cache = $ret;

		return $ret;
	}
}