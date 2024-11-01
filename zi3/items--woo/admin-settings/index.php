<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Woo_Admin0Settings_Index extends _PW1
{
	public function head( PW1_Request $req, PW1_Response $resp )
	{
		$resp->menu[ '61-woo' ] = array( './woo', 'WooCommerce' );
		return $resp;
	}
}