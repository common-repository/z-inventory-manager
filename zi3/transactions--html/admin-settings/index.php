<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Admin0Settings_Index extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->menu[ '41-transactions' ] = array( './transactions', '__Transactions__' );
		return $response;
	}
}