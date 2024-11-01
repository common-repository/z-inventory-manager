<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items_Index0Filter extends _PW1
{
	public $widget;
	public $query;
	public $model;

	public function __construct(
		PW1_ZI3_Items00Html_Widget $widget,
		PW1_ZI3_Items_Query $query,
		PW1_ZI3_Items_Model $model
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

		if( isset($post['filter-search']) ){
			$response->params['filter-search'] = $post['filter-search'];
		}
		else {
			$response->params['filter-search'] = NULL;
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

	// search
		$v = $this->self->findValueSearch( $request );
		if( $v ){
			$q = $this->query->whereSearch( $v, $q );
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

<?php echo $this->self->renderFormState( $request ); ?>
<?php echo $this->self->renderFormSearch( $request ); ?>

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

	public function findValueState( PW1_Request $request )
	{
		$ret = isset( $request->params['filter-state'] ) ? $request->params['filter-state'] : array( PW1_ZI3_Items_Model::STATE_ACTIVE );
		if( ! $ret ) $ret = array();
		return $ret;
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

	public function findValueSearch( PW1_Request $request )
	{
		$ret = isset( $request->params['filter-search'] ) ? $request->params['filter-search'] : NULL;
		return $ret;
	}

	public function renderFormSearch( PW1_Request $request )
	{
		$v = $this->self->findValueSearch( $request );

		ob_start();
?>

<div>
<label>
<span>__Search__</span>
<input type="text" name="filter-search" value="<?php echo esc_attr( $v ); ?>">
</label>
</div>

<?php 
		return ob_get_clean();
	}
}