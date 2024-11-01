<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions_Index0Filter extends _PW1
{
	public $itemsQuery;
	public $itemsWidget;
	public $widget;
	public $query;
	public $model;

	public function __construct(
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Items00Html_Widget $itemsWidget,
		PW1_ZI3_Transactions00Html_Widget $widget,
		PW1_ZI3_Transactions_Query $query,
		PW1_ZI3_Transactions_Model $model
	)
	{}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		$post = $request->data;

		if( isset($post['filter-state']) ){
			$response->params['filter-state'] = $post['filter-state'];
		}
		else {
			$response->params['filter-state'] = NULL;
		}

		if( isset($post['filter-type']) ){
			$response->params['filter-type'] = $post['filter-type'];
		}
		else {
			$response->params['filter-type'] = NULL;
		}

		return $response;
	}

	public function beforeGet( PW1_Request $request, PW1_Response $response )
	{
		$q = $request->params['*q'];

	// state
		$v = $this->self->findValueState( $request );
		if( $v ){
			$q->where( 'state', '=', $v );
		}

	// type
		$v = $this->self->findValueType( $request );
		if( $v ){
			$q->where( 'type', '=', $v );
		}

	// item
		$v = $this->self->findValueItem( $request );
		if( $v ){
			$q = $this->query->whereItem( $v, $q );
		}

		$request->params['*q'] = $q;

		return $request;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		ob_start();
?>

<section>

<h2>__Filter__</h2>

<form method="post" action="URI:.">

<?php echo $this->self->renderFormItem( $request ); ?>
<?php echo $this->self->renderFormState( $request ); ?>
<?php echo $this->self->renderFormType( $request ); ?>

<p>
<button type="submit">__Apply Filter__</button>
</p>

</form>
</section>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}

	public function findValueItem( PW1_Request $request )
	{
		$ret = isset( $request->params['filter-item'] ) ? $request->params['filter-item'] : NULL;
		return $ret;
	}

	public function findValueState( PW1_Request $request )
	{
		$ret = isset( $request->params['filter-state'] ) ? $request->params['filter-state'] : array( PW1_ZI3_Transactions_Model::STATE_DRAFT, PW1_ZI3_Transactions_Model::STATE_ISSUED );
		if( ! $ret ) $ret = array();
		return $ret;
	}

	public function renderFormItem( PW1_Request $request )
	{
		$v = $this->self->findValueItem( $request );
		if( ! $v ) return;

		$v = $this->itemsQuery->findById( $v );
		if( ! $v ) return;

		ob_start();
?>

<div>
	<dl>
		<dt>
			__Item__
		</dt>
		<dd>
			<?php echo $this->itemsWidget->presentTitle( $v, TRUE ); ?>
		</dd>
	</dl>
</div>

<?php 
		return ob_get_clean();
	}

	public function renderFormState( PW1_Request $request )
	{
		$v = $this->self->findValueState( $request );
		$states = $this->model->getStates();

		ob_start();
?>

<div>
<fieldset>
<legend>__State__</legend>

<ul class="pw-inline">
<?php foreach( $states as $state ) : ?>
	<li>
		<label>
			<input type="checkbox" name="filter-state[]" value="<?php echo $state; ?>"<?php if( in_array($state, $v) ){ echo ' checked'; }; ?>>
			<?php echo $this->widget->presentState( $state ); ?>
		</label>
	</li>
<?php endforeach; ?>
</ul>
</fieldset>
</div>

<?php 
		return ob_get_clean();
	}

	public function findValueType( PW1_Request $request )
	{
		$ret = isset( $request->params['filter-type'] ) ? $request->params['filter-type'] : array( PW1_ZI3_Transactions_Model::TYPE_PURCHASE, PW1_ZI3_Transactions_Model::TYPE_SALE );
		if( ! $ret ) $ret = array();
		return $ret;
	}

	public function renderFormType( PW1_Request $request )
	{
		$v = $this->self->findValueType( $request );
		$types = $this->model->getTypes();

		ob_start();
?>

<div>
<fieldset>
<legend>__Type__</legend>

<ul class="pw-inline">
<?php foreach( $types as $type ) : ?>
	<li>
		<label>
			<input type="checkbox" name="filter-type[]" value="<?php echo $type; ?>"<?php if( in_array($type, $v) ){ echo ' checked'; }; ?>>
			<?php echo $this->widget->presentType( $type ); ?>
		</label>
	</li>
<?php endforeach; ?>
</ul>
</fieldset>
</div>

<?php 
		return ob_get_clean();
	}
}