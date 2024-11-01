<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance00Html_Widget extends _PW1
{
	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
		)
	{}

	public function renderPriceNumber( $amount )
	{
		$decPoint = $this->settings->get( 'finance_price_format_number_decpoint' );
		$thousandSep = $this->settings->get( 'finance_price_format_number_thousep' );

		if( 's' == $decPoint ) $decPoint = ' ';
		if( 's' == $thousandSep ) $thousandSep = ' ';

		$amount = floatval( $amount );
		$ret = number_format( $amount, 2, $decPoint, $thousandSep );

		return $ret;
	}

	public function renderPrice( $amount )
	{
		if( ! strlen($amount) ) return;

		$beforeSign = $this->settings->get('finance_price_format_before');
		$afterSign = $this->settings->get('finance_price_format_after');

		$amount = $this->self->renderPriceNumber( $amount );
		$ret = $beforeSign . $amount . ' ' . $afterSign;

		return $ret;
	}
}