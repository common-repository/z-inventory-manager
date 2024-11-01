<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Widget_ColorPicker
{
	private $_colors = array(
		'#339933',
		'#cbe86b', '#ffb3a7', '#89c4f4', '#f5d76e', '#be90d4', '#fcf13a', '#ffffbb', '#ffbbff',
		'#87d37c', '#ff8000', '#73faa9', '#c8e9fc', '#cb9987', '#cfd8dc', '#99bb99', '#99bbbb',
		'#bbbbff', '#dcedc8', '#800000', '#8b0000', '#a52a2a', '#b22222', '#dc143c', '#ff0000',
		'#ff6347', '#ff7f50', '#cd5c5c', '#f08080', '#e9967a', '#fa8072', '#ffa07a', '#ff4500',
		'#ff8c00', '#ffa500', '#ffd700', '#b8860b', '#daa520', '#eee8aa', '#bdb76b', '#f0e68c',
		'#808000', '#ffff00', '#9acd32', '#556b2f', '#6b8e23', '#7cfc00', '#7fff00', '#adff2f',
		'#006400', '#008000', '#228b22', '#00ff00', '#32cd32', '#90ee90', '#98fb98', '#8fbc8f',
		'#00fa9a', '#00ff7f', '#2e8b57', '#66cdaa', '#3cb371', '#20b2aa', '#2f4f4f', '#008080',
		'#008b8b', '#00ffff', '#00ffff', '#e0ffff', '#00ced1', '#40e0d0', '#48d1cc', '#afeeee',
		'#7fffd4', '#b0e0e6', '#5f9ea0', '#4682b4', '#6495ed', '#00bfff', '#1e90ff', '#add8e6',
		'#87ceeb', '#87cefa', '#191970', '#000080', '#00008b', '#0000cd', '#0000ff', '#4169e1',
		'#8a2be2', '#4b0082', '#483d8b', '#6a5acd', '#7b68ee', '#9370db', '#8b008b', '#9400d3',
		'#9932cc', '#ba55d3', '#800080', '#d8bfd8', '#dda0dd', '#ee82ee', '#ff00ff', '#da70d6',
		'#c71585', '#db7093', '#ff1493', '#ff69b4', '#ffb6c1', '#ffc0cb', '#faebd7', '#f5f5dc',
		'#ffe4c4', '#ffebcd', '#f5deb3', '#fff8dc', '#fffacd', '#fafad2', '#ffffe0', '#8b4513',
		'#a0522d', '#d2691e', '#cd853f', '#f4a460', '#deb887', '#d2b48c', '#bc8f8f', '#ffe4b5',
		'#ffdead', '#ffdab9', '#ffe4e1', '#fff0f5', '#faf0e6', '#fdf5e6', '#ffefd5', '#fff5ee',
		'#f5fffa', '#708090', '#778899', '#b0c4de', '#e6e6fa', '#fffaf0', '#f0f8ff', '#f8f8ff',
		'#f0fff0', '#fffff0', '#f0ffff', '#fffafa', '#696969', '#808080', '#a9a9a9', '#c0c0c0',
		'#d3d3d3', '#dcdcdc', '#f5f5f5',
	);

	public function render( $name, $label = NULL, $value = NULL )
	{
		$options = array();
		foreach( $this->_colors as $color ){
			$bgClass = 'pw-bg-' . $color;
			$options[ $color ] = '<div class="pw-rounded" style="background-color: ' . $color . '">&nbsp;&nbsp;</div>';
		}

		if( ! strlen($value) ){
			reset( $this->_colors );
			$value = current( $this->_colors );
		}

		$htmlId = 'pw_colorpicker_' . rand( 1000, 9999 );
		$jsRenderFuncName = $htmlId . '_render';

		ob_start();
?>

<script>
function <?php echo $jsRenderFuncName; ?>( form, color ){
form['<?php echo $htmlId; ?>'].checked = false;
var htmlContent = '<span style="background-color: ' + color + ' ; padding: 0 .25em;" class="pw-inline-block pw-rounded">&nbsp;</span> ' + color;
document.getElementById( '<?php echo $htmlId; ?>_label' ).innerHTML = htmlContent;
}
</script>

<?php if( strlen($label) ) : ?>
<fieldset>
	<legend><?php echo $label; ?></legend>
<?php endif; ?>

<input type="checkbox" id="<?php echo $htmlId; ?>" name="<?php echo $htmlId; ?>" class="pw-collapse">
<label for="<?php echo $htmlId; ?>" id="<?php echo $htmlId; ?>_label">
<span style="background-color: <?php echo $value; ?>; padding: 0 .25em;" class="pw-inline-block pw-rounded">&nbsp;</span> <?php echo $value; ?>
</label>

<div class="pw-collapse-on">
<?php foreach( $this->_colors as $color ) : ?>
	<?php
	$isChecked = ( $color == $value );
	?>
	<div class="pw-inline-block pw-nowrap" style="width: 8em;">
		<label>
			<input name="<?php echo $name; ?>" onchange="<?php echo $jsRenderFuncName; ?>(this.form, '<?php echo $color; ?>');" style="visibility: hidden2;" type="radio" value="<?php echo $color; ?>" <?php if( $isChecked ) :?> checked<?php endif; ?>>
			<span style="background-color: <?php echo $color; ?>; padding: 0 .25em;" class="pw-inline-block pw-rounded">&nbsp;</span> <?php echo $color; ?>
		</label>
	</div>
<?php endforeach; ?>
</div>

<?php if( strlen($label) ) : ?>
</fieldset>
<?php endif; ?>

<?php 
		$ret = ob_get_clean();
		return $ret;
	}

	public function grab( $name, array $post )
	{
		$ret = isset( $post[$name] ) ? $post[$name] : NULL;
		return $ret;
	}
}