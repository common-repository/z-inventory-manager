<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Index extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query $aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( $this->aclQuery->userCan($request->currentUserId, 'view_contacts') ){
			$response->menu[ '41-contacts' ] = array( 'contacts', '__Contacts__' );
		}

		return $response;
	}
}