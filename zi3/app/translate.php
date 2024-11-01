<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App_Translate extends _PW1
{
	public function translateString( $text )
	{
		return $text;
	}

	public function translateText( $text )
	{
		$text = "" . $text;
		// preg_match_all( '/__(.+)__/U', $text, $ma );
		preg_match_all( '/__[^\=]+__/U', $text, $ma );

		$replace = array();
		$count = count($ma[0]);
		for( $ii = 0; $ii < $count; $ii++ ){
			$what = $ma[0][$ii];
			$replace[$what] = $what;
		}

		foreach( $replace as $what => $from ){
			$from = substr( $what, 2, -2 );
			$to = $this->self->translateString( $from );
			$text = str_replace( $what, $to, $text );
		}

		return $text;
	}
}