<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance00Html_Admin0Settings_Index
{
	public $self;

	public function head( PW1_Request $req, PW1_Response $resp )
	{
		$resp->menu[ '51-finance' ] = array( './finance', '__Finance__' );
		return $resp;
	}
}