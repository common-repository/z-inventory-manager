<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0Lines0New_Prepare extends _PW1
{
	public $transactionLinesQuery;

	public function __construct(
		PW1_ZI3_Transactions_Lines_Query	$transactionLinesQuery
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$transactionId = $request->args[0];

		$itemIds = array();
		$lines = $this->transactionLinesQuery->findByTransaction( $transactionId );
		foreach( $lines as $e ) $itemIds[ $e->item_id ] = $e->item_id;

		if( ! isset($request->params['*skip']) ) $request->params['*skip'] = array();
		$request->params['*skip'] += $itemIds;

		return $response;
	}
}