<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl00Html_Admin0Users_Index extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->menu[ '41-acl-roles' ] = array( './acl-roles', '__Permission Roles__' );
		return $response;
	}
}