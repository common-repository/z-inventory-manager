<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Admin0Settings_Acl extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function check( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_settings') ){
			$ret = new PW1_Error( '__Not Allowed__' );
			return $ret;
		}
	}
}