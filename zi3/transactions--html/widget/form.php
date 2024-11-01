<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Widget_Form extends _PW1
{
	public $q;
	public $inputDate;
	public $model;
	public $widget;

	public function __construct(
		PW1_Q $q,
		PW1_Time00Html_Widget_Input_Date $inputDate,
		PW1_ZI3_Transactions_Model $model,
		PW1_ZI3_Transactions00Html_Widget $widget
	)
	{}

	public function render( _PW1_ZI3_Transactions_Model $model )
	{
		$states = $this->model->getStates( $model );
		$states = $this->model->getStates();

		ob_start();
?>

<div class="pw-grid pw-mb2">
	<div class="pw-col-5">
		<label>
			<span>__Reference__</span>
			<input type="text" name="refno" value="<?php echo esc_attr( $model->refno ); ?>" required>
		</label>
	</div>

	<div class="pw-col-3">
		<fieldset>
			<legend>__State__</legend>

			<ul class="pw-inline">
			<?php foreach( $states as $state ) : ?>
				<li>
					<label>
						<input type="radio" name="state" value="<?php echo $state; ?>"<?php if( $state == $model->state ): ?> checked<?php endif; ?>>
						<span><?php echo $this->widget->presentState( $state ); ?></span>
					</label>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>

	<div class="pw-col-4">
		<fieldset>
		<legend>__Date__</legend>
		<?php echo $this->inputDate->render( 'created_date', $model->created_date ); ?>
		</fieldset>
	</div>
</div>

<div>
	<label>
	<span>__Description__</span>
	<textarea name="description"><?php echo esc_attr( $model->description ); ?></textarea>
	</label>
</div>

<?php 
		return ob_get_clean();
	}

	public function errors( array $post )
	{
		$ret = array();

		if( ! strlen($post['refno']) ){
			$ret['refno'] = '__Required Field__';
		}

		return $ret;
	}

	public function grab( array $post, _PW1_ZI3_Transactions_Model $model )
	{
		$model->refno = $post['refno'];
		$model->description = $post['description'];

		if( array_key_exists('state', $post) ){
			$model->state = $post['state'];
		}

		$model->created_date = $this->inputDate->grab( 'created_date', $post );

		return $model;
	}
}