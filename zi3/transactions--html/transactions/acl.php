<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions_Acl extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function check( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_transactions') ){
			$ret = new PW1_Error( '__Not Allowed__' );
			return $ret;
		}
	}
}