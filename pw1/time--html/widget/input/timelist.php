<?php if (! defined('ABSPATH')) exit;
class PW1_Time00Html_Widget_Input_TimeList extends _PW1
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

		if( isset($post[$name]) ){
			$ret = $post[$name];
		}

		return $ret;
	}

	public function render( $name, $value = NULL )
	{
		if( NULL === $value ){
			// $value = $this->t->setNow()->getTimeDb();
			$value = $this->t->setNow()->getTimeInDay();
		}

		$step = 5;
		$options = array();

		for( $e = 0; $e <= 24 * 60 * 60; $e += $step * 60 ){
			if( 0 === $e ){
				$options[ $e ] = ' - ' . '__Start of Day__' . ' - ';
			}
			elseif( 24*60*60 === $e ){
				$options[ $e ] = ' - ' . '__End of Day__' . ' - ';
			}
			else {
				$options[ $e ] = $this->tf->formatTimeInDay( $e );
			}
		}

		ob_start();
?>

<select name="<?php echo $name; ?>">
<?php foreach( $options as $k => $v ): ?>
	<option<?php if( $value == $k ) : ?> selected<?php endif; ?>><?php echo $v; ?></option>
<?php endforeach; ?>
</select>

<?php 
		$ret = ob_get_clean();
		return $ret;
	}
}