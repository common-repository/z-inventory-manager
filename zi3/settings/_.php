<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings_ extends _PW1
{
	private $_values = array();
	public $t;
	public $tf;

	public function __construct(
		PW1_Time_ $t,
		PW1_Time_Format $tf
	)
	{}

	public function boot()
	{
		$dateFormat = $this->self->get( 'datetime_date_format' );
		$this->tf->setDateFormat( $dateFormat );

		$timeFormat = $this->self->get( 'datetime_time_format' );
		$this->tf->setTimeFormat( $timeFormat );

		$weekStartsOn = $this->self->get( 'datetime_week_starts' );
		$this->t->setWeekStartsOn( $weekStartsOn );
	}

	public function get( $name )
	{
		$ret = NULL;

		if( array_key_exists($name, $this->_values) ){
			$ret = $this->_values[ $name ];
		}
		else {
			$defaults = $this->self->getDefaults();

			if( ! array_key_exists($name, $defaults) ){
				$msg = __METHOD__ . ': not registered: ' . $name . '<br>';
				echo $msg;
				return $ret;
			}

			$ret = $defaults[ $name ];
		}

		return $ret;
	}

	public function set( $name, $value )
	{
		$this->_values[ $name ] = $value;
		return $this;
	}

	public function reset( $name )
	{
		unset( $this->_values[$name] );
		return $this;
	}

	public function getDefaults()
	{
		$ret = array();

		$ret['datetime_date_format'] = 'j M Y';
		$ret['datetime_time_format'] = 'g:ia';
		$ret['datetime_week_starts'] = 0;

		$ret['email_from'] = 'info@yoursite.com';
		$ret['email_from_name'] = 'Plainware';
		$ret['email_html'] = TRUE;

		return $ret;
	}
}