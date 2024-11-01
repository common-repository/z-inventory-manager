<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Apps0Common_Html_Admin0Settings0About extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__About__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		return $response;
	}
}