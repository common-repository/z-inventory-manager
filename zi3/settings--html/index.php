<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Index extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $this->aclQuery->userCan($request->currentUserId, 'manage_settings') ){
			$response->menu[ '81-settings' ] = array( 'admin/settings', '__Settings__' );
		}

		return $response;
	}
}