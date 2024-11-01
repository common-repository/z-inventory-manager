<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings0Acl_ extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_ $acl
	)
	{}

	public function boot()
	{
		$this->acl
			->register( 'manage_settings', FALSE, '__Manage Settings__' )
			;
	}
}