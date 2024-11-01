<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Index extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $this->aclQuery->userCan($request->currentUserId, 'manage_transactions') ){
			$response->menu[ '31-transactions' ] = array( 'transactions', '__Transactions__' );
		}
		return $response;
	}
}