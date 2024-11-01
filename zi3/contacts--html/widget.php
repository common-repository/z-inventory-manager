<?php
class PW1_ZI3_Contacts00Html_Widget extends _PW1
{
	public function content( _PW1_ZI3_Contacts_Model $model )
	{
		$ret = array();

		$ret[ '11-type' ] = '<div class="pw-muted2">' . $this->self->presentType( $model ) . '</div>';
		// $ret[ '12-title' ] = '<strong>' . $this->self->presentTitle( $model, TRUE ) . '</strong>';
		$ret[ '12-title' ] = $this->self->presentTitle( $model, TRUE );
		if( strlen($model->phone) ){
			$ret[ '13-phone' ] = '<div title="__Phone__">' . esc_html( $model->phone ) . '</div>';
		}
		if( strlen($model->email) ){
			$ret[ '14-email' ] = '<div title="__Email__">' . esc_html( $model->email ) . '</div>';
		}

		return $ret;
	}

	public function render( _PW1_ZI3_Contacts_Model $model )
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

	public function presentType( $modelOrType )
	{
		$types = array();
		if( $modelOrType instanceof _PW1_ZI3_Contacts_Model ){
			if( $modelOrType->is_customer ) $types[ PW1_ZI3_Contacts_Model::TYPE_CUSTOMER ] = PW1_ZI3_Contacts_Model::TYPE_CUSTOMER;
			if( $modelOrType->is_supplier ) $types[ PW1_ZI3_Contacts_Model::TYPE_SUPPLIER ] = PW1_ZI3_Contacts_Model::TYPE_SUPPLIER;
		}
		else {
			$types[ $modelOrType ] = $modelOrType;
		}

		$ret = array();
		if( isset($types[PW1_ZI3_Contacts_Model::TYPE_CUSTOMER]) ) $ret[] = '__Customer__';
		if( isset($types[PW1_ZI3_Contacts_Model::TYPE_SUPPLIER]) ) $ret[] = '__Supplier__';
		$ret = join( ', ', $ret );
		return $ret;
	}

	public function presentTitle( _PW1_ZI3_Contacts_Model $model, $linkTo = NULL )
	{
		$ret = $model->title;
		$titleLabel = $model->title;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:contacts/' . $model->id;
		}

		ob_start();
?>

<?php if( NULL === $linkTo ) : ?>
	<span title="<?php echo esc_attr($titleLabel); ?>"><?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Contacts_Model::STATE_ACTIVE !== $model->state ) : ?> &dash; <?php echo $this->self->presentState( $model->state ); ?><?php endif; ?></span>
<?php else : ?>
	<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($titleLabel); ?>"><?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Contacts_Model::STATE_ACTIVE !== $model->state ) : ?><span> &dash; <?php echo $this->self->presentState( $model->state ); ?></span><?php endif; ?></a>
<?php endif; ?>

<?php
		return trim( ob_get_clean() );
	}

	public function presentTitleDetails( _PW1_ZI3_Contacts_Model $model, $linkTo = NULL )
	{
		$ret = $model->title;
		$titleLabel = $model->title;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:contacts/' . $model->id;
		}

		ob_start();
?>

<?php if( NULL === $linkTo ) : ?>
	<span title="<?php echo esc_attr($titleLabel); ?>"><?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Contacts_Model::STATE_ACTIVE !== $model->state ) : ?> <?php echo $this->self->presentState( $model->state ); ?><?php endif; ?></span>
<?php else : ?>
	<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($titleLabel); ?>">
		<?php echo esc_html($titleLabel); ?><?php if( PW1_ZI3_Contacts_Model::STATE_ACTIVE !== $model->state ) : ?><span><?php echo $this->self->presentState( $model->state ); ?></span><?php endif; ?>
		<?php if( strlen($model->phone) ) : ?><span title="__Phone Number__"><?php echo esc_html( $model->phone ); ?></span><?php endif; ?><?php if( strlen($model->email) ) : ?><span title="__Email__"><?php echo esc_html( $model->email ); ?></span><?php endif; ?>
	</a>
<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		$ret = str_replace( array("\n", "\r", "\t"), '', $ret );
		$ret = str_replace( ">\s", '>', $ret );
		$ret = str_replace( "<\s", '<', $ret );

		return $ret;
	}

	public function presentState( $state )
	{
		$labels = array();

		$options = array(
			PW1_ZI3_Contacts_Model::STATE_ACTIVE	=> array( 'olive', '__Active__' ),
			PW1_ZI3_Contacts_Model::STATE_ARCHIVE	=> array( 'darkgray', '__Archived__' ),
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