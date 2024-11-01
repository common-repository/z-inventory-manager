<?php
class PW1_ZI3_Transactions00Html_Widget extends _PW1
{
	public $transactionsModel;

	public function __construct(
		PW1_ZI3_Transactions_Model	$transactionsModel
	)
	{}

	public function presentTitle( _PW1_ZI3_Transactions_Model $model, $linkTo = NULL )
	{
		$ret = $model->refno;
		$titleLabel = $model->refno;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:transactions/' . $model->id;
		}

		list( $typeIcon, $typeLabel ) = $this->_presentType( $model->type );
		list( $stateColor, $stateLabel ) = $this->_presentState( $model->state );

		$title = $typeLabel . ' ' . $model->refno . ' ' . $stateLabel;


		ob_start();
?>
<?php if( NULL === $linkTo ) : ?>
<span class="pw-muted2"><?php echo $this->self->presentTypeTitle($model); ?> </span><?php echo esc_html($titleLabel); ?><span> <?php echo $this->self->presentState( $model->state ); ?></span>
<?php else : ?>
<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($title); ?>" class="pw-color-<?php echo $stateColor; ?>"><?php echo esc_html( $titleLabel ); ?></a>
<?php endif; ?>
<?php
		return ob_get_clean();
	}

	private function _presentState( $modelOrValue )
	{
		$state = ( $modelOrValue instanceof _PW1_ZI3_Transactions_Model ) ? $modelOrValue->state : $modelOrValue;

		$options = array();
		$options[ PW1_ZI3_Transactions_Model::STATE_DRAFT ] = array( 'darkgray', '__Draft__' );
		$options[ PW1_ZI3_Transactions_Model::STATE_ISSUED ] = array( 'olive', '__Issued__' );

		$ret = $options[ $state ];
		return $ret;
	}

	public function presentStateOld( $state )
	{
		list( $color, $label ) = $this->_presentState( $state );

		ob_start();
?>
<span class="pw-color-<?php echo $color; ?>" title="<?php echo $label; ?>"><?php echo $label; ?></span>
<?php
		return ob_get_clean();
	}

	public function presentState( $state )
	{
		list( $color, $label ) = $this->_presentState( $state );

		ob_start();
?>
<span class="pw-bg-<?php echo $color; ?> pw-color-white pw-rounded" title="<?php echo $label; ?>">&nbsp;<?php echo $label; ?>&nbsp;</span>
<?php
		return ob_get_clean();
	}

	private function _presentType( $modelOrType )
	{
		$type = ( $modelOrType instanceof _PW1_ZI3_Transactions_Model ) ? $modelOrType->type : $modelOrType;

		$options = array();
		$options[ PW1_ZI3_Transactions_Model::TYPE_PURCHASE ] = array( '&rarr;', '__Purchase__' );
		$options[ PW1_ZI3_Transactions_Model::TYPE_SALE ] = array( '&larr;', '__Sale__' );

		$ret = $options[ $type ];
		return $ret;
	}

	public function presentType( $modelOrType )
	{
		$ret = $this->_presentType( $modelOrType );
		return $ret[1];
	}

	public function presentTypeOld( $modelOrType )
	{
		$ret = $this->_presentType( $modelOrType );

		ob_start();
?>

<span title="<?php echo $ret[1]; ?>"><?php echo $ret[0]; ?> <?php echo $ret[1]; ?></span>

<?php
		return trim( ob_get_clean() );
	}

	public function presentTypeTitle( $modelOrType )
	{
		$ret = $this->_presentType( $modelOrType );

		ob_start();
?>
<?php echo $ret[1]; ?>
<?php
		return ob_get_clean();
	}

	public function presentTypeShort( $modelOrType )
	{
		$ret = $this->_presentType( $modelOrType );
		ob_start();
?>
<span title="<?php echo $ret[1]; ?>"><?php echo $ret[0]; ?></span>
<?php
		return ob_get_clean();
	}
}