<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items_Acl extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function checkEdit( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_items') ){
			$ret = new PW1_Error( '__Not Allowed__' );
			return $ret;
		}
	}

	public function checkView( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'view_items') ){
			$ret = new PW1_Error( '__Not Allowed__' );
			return $ret;
		}
	}
}