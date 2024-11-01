<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit00Html_Index extends _PW1
{
	public $auditModel;

	public function __construct(
		PW1_ZI3_Audit_Model $auditModel
	)
	{}

	public function useCurrentUserId( PW1_Request $request )
	{
		$this->auditModel->withCurrentUserId( $request->currentUserId );
		return;
	}
}