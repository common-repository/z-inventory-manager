<?php
class PW1_ZI3_Apps0Z0Inventory0Manager_ extends _PW1
{
	public static function modules()
	{
		$ret = array(
		// wp stuff
			'PW1_ZI3_Auth0Wp_',

			'PW1_ZI3_Acl0Wp_',
			'PW1_ZI3_Acl0Wp00Db_',
			'PW1_ZI3_Acl0Wp00Html_',

			'PW1_ZI3_App0Wp_',
			'PW1_ZI3_App0Wp00Html_',

			'PW1_ZI3_Users0Wp_',
			'PW1_ZI3_Users0Wp00Html_',

			'PW1_ZI3_Items00Woo_',

			'PW1_ZI3_Promo00Html_',
		);

		$ret = array_merge( PW1_::$modules, PW1_ZI3_::$modules, $ret );
		$ret[] = __CLASS__;

		return $ret;
	}

	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes',	__CLASS__ . '@htmlRoutes' )
			;
	}

	public function htmlRoutes()
	{
		$ret = array();
		$ret[] = array( 'GET',	'admin/settings/about',	__CLASS__ . 'Html_Admin0Settings0About@get', 0, 'about' );
		return $ret;
	}
}