<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
	// extend
		$pw1
			->wrap( 'PW1_ZI3_Users_Model@construct',	__CLASS__ . 'Model@construct' )
			->wrap( 'PW1_ZI3_Users_Query@find',			__CLASS__ . 'Query@wrapFind' )
			->wrap( 'PW1_ZI3_Users_Query@findById',	__CLASS__ . 'Query@wrapFindById' )
			->wrap( 'PW1_ZI3_Users_Crud@read',			__CLASS__ . 'Crud@read' )
			;
	}
}