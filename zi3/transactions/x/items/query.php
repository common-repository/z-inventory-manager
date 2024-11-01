<?php
class PW1_ZI3_Transactions_X_Items_Query extends _PW1
{
	public $q;
	public $transactionsQuery;
	public $transactionLinesQuery;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery
	)
	{}

	public function whereNotInTransaction( $transactionOrId, _PW1_Q $q )
	{
		$lines = $this->transactionLinesQuery->findByTransaction( $transactionOrId );

		$currentItemsIds = array();
		foreach( $lines as $line ) $currentItemsIds[ $line->item_id ] = $line->item_id;

		if( $currentItemsIds ){
			$q->where( 'id', '<>', $currentItemsIds );
		}

		return $q;
	}
}