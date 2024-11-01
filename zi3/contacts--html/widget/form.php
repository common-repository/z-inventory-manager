<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Widget_Form extends _PW1
{
	public $model;
	public $widget;

	public function __construct(
		PW1_ZI3_Contacts_Model $model,
		PW1_ZI3_Contacts00Html_Widget $widget
	)
	{}

	public function render( _PW1_ZI3_Contacts_Model $model )
	{
		ob_start();
?>

<?php if( $model->id ) : ?>

	<div class="pw-grid">
		<div class="pw-col-8">
			<label>
				<span>__Name__</span>
				<input type="text" name="title" value="<?php echo esc_attr( $model->title ); ?>" required>
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

<?php else : ?>

	<div>
		<label>
			<span>__Name__</span>
			<input type="text" name="title" value="<?php echo esc_attr( $model->title ); ?>" required>
		</label>
	</div>

<?php endif; ?>

<div class="pw-grid">
	<div class="pw-col-4">
		<label>
			<span>__Phone Number__</span>
			<input type="text" name="phone" value="<?php echo esc_attr( $model->phone ); ?>">
		</label>
	</div>

	<div class="pw-col-8">
		<label>
			<span>__Email__</span>
			<input type="text" name="email" value="<?php echo esc_attr( $model->email ); ?>">
		</label>
	</div>
</div>

<div>
	<label>
		<span>__Description__</span>
		<textarea name="description"><?php echo esc_html( $model->description ); ?></textarea>
	</label>
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

		return $ret;
	}

	public function grab( array $post, _PW1_ZI3_Contacts_Model $model )
	{
		if( isset($post['title']) )
			$model->title = $post['title'];

		if( isset($post['email']) )
			$model->email = $post['email'];

		if( isset($post['phone']) )
			$model->phone = $post['phone'];

		if( isset($post['state']) && $post['state'] ){
			$model->state = $post['state'];
		}

		return $model;
	}
}