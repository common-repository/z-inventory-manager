<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Helper extends _PW1
{
	public $settingsQuery;

	public function __construct(
		PW1_ZI3_Settings_Query $settingsQuery
		)
	{}

	public function renderPriceNumber( $amount )
	{
		$settings = $this->settingsQuery->get();

		list( $decPoint, $thousandSep ) = $settings->finance_price_format_number;

		$amount = floatval( $amount );
		$ret = number_format( $amount, 2, $decPoint, $thousandSep );

		return $ret;
	}

	public function renderPrice( $amount )
	{
		$settings = $this->settingsQuery->get();

		$beforeSign = $settings->finance_price_format_before;
		$afterSign = $settings->finance_price_format_after;

		$amount = $this->self->renderPriceNumber( $amount );
		$ret = $beforeSign . $amount . ' ' . $afterSign;

		return $ret;
	}
}