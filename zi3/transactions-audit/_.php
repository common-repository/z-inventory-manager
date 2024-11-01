<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Audit_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
	// LISTEN COMMAND
		$pw1
			->wrap( 		'PW1_ZI3_Transactions_Command@create',			__CLASS__ . 'Command@wrapCreate' )

			->listen( 	'PW1_ZI3_Transactions_Command@update',			__CLASS__ . 'Command@listenStateChange' )
			->listen( 	'PW1_ZI3_Transactions_Command@update',			__CLASS__ . 'Command@listenDateChange' )
			->listen( 	'PW1_ZI3_Transactions_Command@update',			__CLASS__ . 'Command@listenRefnoChange' )
			->listen( 	'PW1_ZI3_Transactions_Command@delete',			__CLASS__ . 'Command@listenDelete' )

			->listen( 	'PW1_ZI3_Transactions_Lines_Command@delete',	__CLASS__ . 'Command@listenLinesDelete' )
			->listen( 	'PW1_ZI3_Transactions_Lines_Command@create',	__CLASS__ . 'Command@listenLinesCreate' )
			->listen( 	'PW1_ZI3_Transactions_Lines_Command@update',	__CLASS__ . 'Command@listenLinesUpdate' )
			;
	}
}