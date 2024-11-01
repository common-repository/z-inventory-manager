<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Auth00Html_Index extends _PW1
{
	public $auth;

	public function __construct(
		PW1_ZI3_Auth_ $auth
	)
	{}

	public function currentUserId( PW1_Request $request )
	{
		$request->currentUserId = $this->auth->currentUserId();
		return $request;
	}
}