<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Promo00Html_Index extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $this->aclQuery->userCan($request->currentUserId, 'manage_install') ){
			// $response->menu[ '91-promo' ] = array( 'promo', '<span>&rarr;</span> __Pro Version__' );
			$response->menu[ '91-promo' ] = array( 'promo', '__Add-Ons__' );

			$label = 'PlainInventory Pro &nearr;';
			$response->menu[ '92-promo' ] = array( 'https://www.z-inventory-manager.com/order/', $label );
		}

		return $response;
	}
}