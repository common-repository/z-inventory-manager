<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Auth0Wp_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->wrap( 'PW1_ZI3_Auth_@currentUserId', __CLASS__ . '@currentUserId' )
			;
	}

	public function currentUserId()
	{
		$ret = get_current_user_id();
		return $ret;
	}
}