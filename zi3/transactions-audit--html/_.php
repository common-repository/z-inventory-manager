<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Audit00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
	// routes
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;

	// widget
		$pw1
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentChangeState' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentChangeDate' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentChangeRefno' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentCreate' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentDeleteLine' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentCreateLine' )
			->chain( 'PW1_ZI3_Audit00Html_Widget@present', __CLASS__ . 'Widget@presentUpdateLine' )
			;
	}

	public function routes()
	{
		$ret = array();

		// $ret[] = array( '*',		'transactions/{id}:audit',	__CLASS__ . 'Transactions0Id0Audit_Index0Preview@*' );
		// $ret[] = array( 'GET',	'transactions/{id}',	':audit' );

		$ret[] = array( 'HEAD',	'transactions/{id}',			__CLASS__ . 'Transactions0Id_Index@head' );
		$ret[] = array( '*',		'transactions/{id}/audit',	__CLASS__ . 'Transactions0Id0Audit_Index@*' );

		return $ret;
	}
}