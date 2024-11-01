<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp00Html_Admin0Users_Index extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->menu[ '51-acl-wp' ] = array( './acl-wp', '__WordPress Permission Connections__' );
		return $response;
	}
}