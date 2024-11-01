<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App0Wp_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->wrap( 'PW1_Sql_@query',	__CLASS__ . 'Sql@query' )

		// translate
			->wrap( 'PW1_ZI3_App_Translate@translateString',	__CLASS__ . 'Translate@translateString' )
			;
	}

	public function pluginFile()
	{
		return '';
	}
}