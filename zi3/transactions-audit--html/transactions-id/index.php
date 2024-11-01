<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Audit00Html_Transactions0Id_Index extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->menu[ '81-audit' ] = array( './audit', '<span>&#9776;</span>__History__' );
		return $response;
	}
}