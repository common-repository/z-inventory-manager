<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id_Index extends _PW1
{
	public $transactionsQuery;
	public $transactionsWidget;

	public function __construct(
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		if( is_array($id) && (count($id) > 1) ) return;

		$model = $this->transactionsQuery->findById( $id );
		$response->title = $this->transactionsWidget->presentTitle( $model );

		// $response->menu[ '81-print' ] = array( '.:print', '__Print View__' );
		$response->menu[ '82-print' ] = '<a href="URI:.:print" target="_blank">__Print View__</a>';
		$response->menu[ '99-delete' ] = array( './delete', '<span>&times;</span>__Delete__' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
	// assume content is defined in sub requests
		return $response;
	}
}