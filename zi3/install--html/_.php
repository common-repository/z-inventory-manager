<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
	// UI
		$pw1
			->merge( 'PW1_Handle@routes',	__CLASS__ . '@routes' )
			;
	}

	public function routes()
	{
		$ret = array();

		$ret[] = array( '*',	'install*',						__CLASS__ . 'Index@can' );
		$ret[] = array( '*',	'admin/settings/about*',	__CLASS__ . 'Index@can' );

	// check install
		$ret[] = array( 'GET',	'*',		__CLASS__ . 'Index@checkInstall', -4, 'checkinstall' );
		$ret[] = array( 'POST',	'*',		__CLASS__ . 'Index@checkInstall', -4, 'checkinstall' );
		$ret[] = array( 'GET',	'*:*',	NULL, 1, 'checkinstall' );

	// install
		$ret[] = array( '*',	'install',		__CLASS__ . 'Install@*' );
		$ret[] = array( '*',	'installok',	__CLASS__ . 'InstallOk@*' );

	// about && uninstall
		$ret[] = array( 'HEAD',	'admin/settings',			__CLASS__ . 'Admin0Settings@head' );
		// $ret[] = array( '*',		'admin/settings/about',	__CLASS__ . 'Admin0Settings0About@*' );

		$ret[] = array( '*',		'admin/settings/uninstall',	__CLASS__ . 'Admin0Settings0Uninstall@*' );

		return $ret;
	}
}