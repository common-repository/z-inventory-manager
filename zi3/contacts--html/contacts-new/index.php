<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Contacts0New_Index extends _PW1
{
	public $model;
	public $command;
	public $form;

	public function __construct(
		PW1_ZI3_Contacts_Model $model,
		PW1_ZI3_Contacts_Command $command,
		PW1_ZI3_Contacts00Html_Widget_Form $form
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];
		if( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER == $type )	$response->title = '__New Customer__';
		if( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER == $type )	$response->title = '__New Supplier__';

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];
		$model = $this->model->construct();
		$model = $this->form->grab( $request->formValues, $model );

		ob_start();
?>

<form method="post" action="URI:.">

<?php echo $this->form->render( $model ); ?>

<p>
<button type="submit">__Save__</button>
</p>

</form>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];

		$response->redirect = '.';
		$post = $request->data;

	// VALIDATE POST
		$response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		$model = $this->model->construct();

		$type = $request->args[0];
		if( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER == $type )	$model->is_customer = TRUE;
		if( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER == $type )	$model->is_supplier = TRUE;

		$model = $this->form->grab( $post, $model );

		$res = $this->command->create( $model );

		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';

		if( $model->is_customer ){
			$response->addMessage( '__New Customer Created__' );
		}
		if( $model->is_supplier ){
			$response->addMessage( '__New Supplier Created__' );
		}

		$response->data[ 'model' ] = $res;

		return $response;
	}

	public function afterPostSelector( PW1_Request $request, PW1_Response $response )
	{
		if( $response->getErrors() ) return $response;

		$model = $response->data[ 'model' ];

		$backParam = isset( $request->params['_backparam'] ) ? $request->params['_backparam'] : 'id';
		$response->redirect = '../..?' . $backParam . '=' . $model->id;

		return $response;
	}
}