<?php if (! defined('ABSPATH')) exit;
class PW1_Time00Html_Widget_Input_DateList extends _PW1
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
		$ret = NULL;

		$nameY	= $name . '_y';
		$nameM	= $name . '_m';
		$nameD	= $name . '_d';

		if( isset($post[$nameY]) ){
			$y = $post[ $nameY ];
			$m = $post[ $nameM ];
			$d = $post[ $nameD ];
			$ret = $y . sprintf('%02d', $m) . sprintf('%02d', $d);
		}

		return $ret;
	}

	public function render( $name, $value = NULL )
	{
		$nameY	= $name . '_y';
		$nameM	= $name . '_m';
		$nameD	= $name . '_d';

		if( NULL === $value ){
			$value = $this->t->setNow()->getDateDb();
		}

		$valueY = substr( $value, 0, 4 );
		$valueM = substr( $value, 4, 2 );
		$valueD = substr( $value, 6, 2 );

		ob_start();
?>

<ul class="pw-xs-inline">
	<?php
	$options = range( 1, 31 );
	?>
	<li>
		<select name="<?php echo $nameD; ?>"> 
		<?php foreach( $options as $e ) : ?>
			<option value="<?php echo $e; ?>"<?php if( $e == $valueD ):?> selected<?php endif; ?>><?php echo $e; ?></option>
		<?php endforeach; ?>
		</select>
	</li>

	<?php
	$options = range( 1, 12 );
	?>
	<li>
		<select name="<?php echo $nameM; ?>"> 
		<?php foreach( $options as $e ) : ?>
			<option value="<?php echo $e; ?>"<?php if( $e == $valueM ):?> selected<?php endif; ?>><?php echo $this->tf->formatMonthName( $e ); ?></option>
		<?php endforeach; ?>
		</select>
	</li>

	<?php
	$currentYear = $this->t->setNow()->getYear();
	$options = range( $currentYear - 10, $currentYear + 10 );
	?>
	<li>
		<select name="<?php echo $nameY; ?>"> 
		<?php foreach( $options as $e ) : ?>
			<option value="<?php echo $e; ?>"<?php if( $e == $valueY ):?> selected<?php endif; ?>><?php echo $e; ?></option>
		<?php endforeach; ?>
		</select>
	</li>
</ul>

<?php 
		$ret = ob_get_clean();
		return $ret;
	}
}