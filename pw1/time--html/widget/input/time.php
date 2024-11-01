<?php if (! defined('ABSPATH')) exit;
class PW1_Time00Html_Widget_Input_Time extends _PW1
{
	public $t;
	public $tf;

	public function __construct(
		PW1_Time_ $t,
		PW1_Time_Format $tf
	)
	{}

	public function grab( $name, array $post )
	{
		$hour = isset( $post[$name . '_hour'] ) ? $post[$name . '_hour'] : 0;
		$minute = isset( $post[$name . '_minute'] ) ? $post[$name . '_minute'] : 0;
		$ret = $hour * 60 * 60 + $minute * 60;
		return $ret;
	}

	public function render( $name, $value = NULL )
	{
		if( NULL === $value ){
			// $value = $this->t->setNow()->getTimeDb();
			$value = $this->t->setNow()->getTimeInDay();
		}

		$hourOptions = array();
		for( $h = 0; $h <= 23; $h++ ){
			// $v = $h * 60 * 60;
			// $hourOptions[ $h ] = $this->tf->formatTimeInDay( $v );
			$v = sprintf( '%02d', $h );
			$v = $this->tf->formatTimeInDay( $h * 60 * 60 );
		// remove minutes :15
			$v = preg_replace( '/\:\d\d/', '', $v );

			$hourOptions[ $h ] = $v;
		}

		$minuteOptions = array();
		for( $m = 0; $m <= 55; $m += 5 ){
			// $v = $h * 60 * 60;
			// $hourOptions[ $h ] = $this->tf->formatTimeInDay( $v );
			$minuteOptions[ $m ] = ': ' . sprintf( '%02d', $m );
		}

		$hourValue = floor( $value / (60 * 60) );
		$minuteValue = floor( ( $value - $hourValue * 60 * 60 ) / 60 );
		$minuteValue = floor( $minuteValue / 5 ) * 5;

		ob_start();
?>

<fieldset>
	<ul class="pw-xs-inline">
		<li>
			<select name="<?php echo $name; ?>_hour">
			<?php foreach( $hourOptions as $k => $v ): ?>
				<option value="<?php echo $k; ?>"<?php if( $hourValue == $k ) : ?> selected<?php endif; ?>><?php echo $v; ?></option>
			<?php endforeach; ?>
			</select>
		</li>
		<li>
			<select name="<?php echo $name; ?>_minute">
			<?php foreach( $minuteOptions as $k => $v ): ?>
				<option value="<?php echo $k; ?>"<?php if( $minuteValue == $k ) : ?> selected<?php endif; ?>><?php echo $v; ?></option>
			<?php endforeach; ?>
			</select>
		</li>
	</ul>
</fieldset>

<?php 
		$ret = ob_get_clean();
		return $ret;
	}
}