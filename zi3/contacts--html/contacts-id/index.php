<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Contacts0Id_Index extends _PW1
{
	public $aclQuery;
	public $query;
	public $widget;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery,
		PW1_ZI3_Contacts_Query $query,
		PW1_ZI3_Contacts00Html_Widget	$widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		if( is_array($id) && (count($id) > 1) ) return;

		$model = $this->query->findById( $id );
		$response->title = esc_html( $model->title );

		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_contacts' );

		if( $canEdit ){
			$response->menu[ '99-delete' ]	= array( './delete', '<span>&times;</span>__Delete__' );
		}

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
	// assume extension by subrequests
		return $response;
	}
}