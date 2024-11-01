<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp00Html_Index extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $request->currentUserId ){
			// $response->menu[ '91-profile' ] = array( 'profile', '__My Profile__' );
		}
		return $response;
	}
}