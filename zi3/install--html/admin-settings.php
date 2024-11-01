<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_Admin0Settings extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $this->aclQuery->userCan($request->currentUserId, 'manage_install') ){
			$response->menu[ '81-about' ] = array( './about', '__About__' );
			$response->menu[ '82-uninstall' ] = array( './uninstall', '__Uninstall__' );
		}
		return $response;
	}
}