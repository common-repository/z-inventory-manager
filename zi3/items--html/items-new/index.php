<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0New_Index extends _PW1
{
	public $index;
	public $model;
	public $form;
	public $command;

	public function __construct(
		PW1_ZI3_Items00Html_Items_Index $index,
		PW1_ZI3_Items_Model $model,
		PW1_ZI3_Items00Html_Widget_Form $form,
		PW1_ZI3_Items_Command $command
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response  )
	{
		$response->title = '__New Item__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$model = $this->model->construct();
		$model = $this->form->grab( $request->formValues, $model );

		ob_start();
?>

<form method="post" action="URI:.">

<?php echo $this->form->render( $model ); ?>

<p>
<button type="submit">__Add New Item__</button>
</p>

</form>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';
		$post = $request->data;

	// VALIDATE POST
		$response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		$model = $this->model->construct();
		$model = $this->form->grab( $post, $model );

		$res = $this->command->create( $model );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$model = $res;

		$mode = $this->index->whichMode( $request );

		if( PW1_ZI3_Items00Html_Items_Index::MODE_SELECT == $mode ){
			$backParam = isset( $request->params['_backparam'] ) ? $request->params['_backparam'] : 'id';
			$back = isset( $request->params['__' . $backParam] ) ? $request->params['__' . $backParam] : array();
			$back = array_merge( $back, array($model->id) );
			$response->params[$backParam] = $back;
			// $response->params['offset'] = NULL;
			$response->redirect = '../..';
		}
		else {
			$response->redirect = '..';
		}

		$response->addMessage( '__Item Saved__' );

		return $response;
	}
}