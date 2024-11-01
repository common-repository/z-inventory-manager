<?php
class PW1_ZI3_Items00Html_Widget extends _PW1
{
	public function content( _PW1_ZI3_Items_Model $model )
	{
		$ret = array();

		$ret[ '11-sku' ] = '<div class="pw-muted2">' . esc_html( $model->sku ) . '</div>';
		$ret[ '12-title' ] = $this->self->presentTitle( $model, TRUE );

		return $ret;
	}

	public function render( _PW1_ZI3_Items_Model $model )
	{
		$content = $this->self->content( $model );
		ksort( $content );

		ob_start();
?>

<ul>
<?php foreach( $content as $k => $v ) : ?>
<li>
<?php echo $v; ?>
</li>
<?php endforeach; ?>
</ul>

<?php
		return trim( ob_get_clean() );
	}

	public function presentTitle( _PW1_ZI3_Items_Model $model, $linkTo = NULL )
	{
		$ret = $model->title;
		$titleLabel = $model->title;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:items/' . $model->id;
		}

		ob_start();
?>

<?php if( NULL === $linkTo ) : ?>
	<span title="<?php echo esc_attr($titleLabel); ?>"><?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Items_Model::STATE_ACTIVE !== $model->state ) : ?> <?php echo $this->self->presentState( $model->state ); ?><?php endif; ?></span>
<?php else : ?>
	<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($titleLabel); ?>"><?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Items_Model::STATE_ACTIVE !== $model->state ) : ?><?php echo $this->self->presentState( $model->state ); ?><?php endif; ?></a>
<?php endif; ?>

<?php
		return trim( ob_get_clean() );
	}

	public function presentState( $state )
	{
		$labels = array();

		$options = array(
			PW1_ZI3_Items_Model::STATE_ACTIVE	=> array( 'olive', '__Active__' ),
			PW1_ZI3_Items_Model::STATE_ARCHIVE	=> array( 'darkgray', '__Archived__' ),
		);

		if( ! isset($options[$state]) ) return $state;

		list( $color, $label ) = $options[$state];
		ob_start();
?>
<span class="pw-color-<?php echo $color; ?>" title="<?php echo $label; ?>"><?php echo $label; ?></span>
<?php
		return ob_get_clean();
	}
}