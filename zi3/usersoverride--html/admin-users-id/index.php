<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_UsersOverride00Html_Admin0Users0Id_Index extends _PW1
{
	public $_;

	public function __construct(
		PW1_ZI3_UsersOverride00Html_ $_
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$p = $this->_->myParamName();
		$id = $request->args[0];

		$response->menu[ '71-override' ] = array( '?*' . $p . '=' . $id, '__Access As If This User__' );
		return $response;
	}
}