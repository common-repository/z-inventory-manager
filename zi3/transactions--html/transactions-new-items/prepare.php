<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0New0Items_Prepare
{
	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$itemIds = isset( $request->params['_items'] ) ? $request->params['_items'] : array();
		if( ! $itemIds ) return $response;

		if( ! isset($request->params['*skip']) ) $request->params['*skip'] = array();
		$request->params['*skip'] += $itemIds;

		return $response;
	}
}