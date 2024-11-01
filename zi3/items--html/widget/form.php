<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Widget_Form extends _PW1
{
	public $model;
	public $widget;

	public function __construct(
		PW1_ZI3_Items_Model $model,
		PW1_ZI3_Items00Html_Widget $widget
	)
	{}

	public function render( _PW1_ZI3_Items_Model $model )
	{
		ob_start();
?>

<div class="pw-grid">
	<div class="pw-col-5">
		<label>
			<span>__Title__</span>
			<input type="text" name="title" value="<?php echo esc_attr( $model->title ); ?>" required>
		</label>
	</div>

	<div class="pw-col-3">
		<label>
		<span>__SKU__</span>
		<input type="text" name="sku" value="<?php echo esc_attr( $model->sku ); ?>" required>
		</label>
	</div>

		<div class="pw-col-4">
			<fieldset>
				<legend>__State__</legend>

				<?php $options = $this->model->getStates(); ?>
				<ul class="pw-inline">
					<?php foreach( $options as $e ) : ?>
					<li>
						<label>
							<input type="radio" name="state" value="<?php echo $e; ?>"<?php if( $e == $model->state ): ?> checked<?php endif; ?>><?php echo $this->widget->presentState( $e ); ?>
						</label>
					</li>
					<?php endforeach; ?>
				</li>
			</fieldset>
		</div>
</div>

<div>
	<label>
	<span>__Description__</span>
	<textarea name="description"><?php echo esc_attr( $model->description ); ?></textarea>
	</label>
</div>

<div class="pw-grid">
	<div class="pw-col-6">
		<label>
		<span>__Default Cost__</span>
		<input type="text" name="default_cost" value="<?php echo esc_attr( $model->default_cost ); ?>">
		</label>
	</div>

	<div class="pw-col-6">
		<label>
		<span>__Default Price__</span>
		<input type="text" name="default_price" value="<?php echo esc_attr( $model->default_price ); ?>">
		</label>
	</div>
</div>

<?php 
		return ob_get_clean();
	}

	public function errors( array $post )
	{
		$ret = array();

		if( ! strlen($post['title']) ){
			$ret['title'] = '__Required Field__';
		}

		if( ! strlen($post['sku']) ){
			$ret['sku'] = '__Required Field__';
		}

		return $ret;
	}

	public function grab( array $post, _PW1_ZI3_Items_Model $model )
	{
		if( isset($post['title']) )
			$model->title = $post['title'];

		if( isset($post['sku']) )
			$model->sku = $post['sku'];

		if( isset($post['description']) )
			$model->description = $post['description'];

		if( isset($post['default_cost']) )
			$model->default_cost = strlen( $post['default_cost'] ) ? $post['default_cost'] : NULL;

		if( isset($post['default_price']) )
			$model->default_price = strlen( $post['default_price'] ) ? $post['default_price'] : NULL;

		if( array_key_exists('state', $post) ){
			$model->state = $post['state'];
		}

		return $model;
	}
}