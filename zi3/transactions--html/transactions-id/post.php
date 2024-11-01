<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id_Post extends _PW1
{
	public $command;
	public $query;

	public function __construct(
		PW1_ZI3_Items_Command $command,
		PW1_ZI3_Items_Query $query
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$ids = $request->args[0];

		$post = $request->data;
		$actionName = isset( $post['action'] ) ? $post['action'] : NULL;
		if( ! $actionName ){
			return $response;
		}

		$q = $this->query->q();
		$q = $this->query->whereId( $q, $ids );
		$models = call_user_func( $this->query, $q );

		$response = $this->self->action( $actionName, $models, $request, $response );

		return $response;
	}

	public function action( $actionName, array $models, PW1_Request $request, PW1_Response $response )
	{
		if( 'duplicate' == $actionName )	$response = $this->self->actionDuplicate( $models, $request, $response );
		if( 'restore' == $actionName )	$response = $this->self->actionRestore( $models, $request, $response );
		if( 'archive' == $actionName )	$response = $this->self->actionArchive( $models, $request, $response );
		if( 'delete' == $actionName )		$response = $this->self->actionDelete( $models, $request, $response );

		return $response;
	}

	public function actionDuplicate( array $models, PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		$ids = $request->args[0];
		$post = $request->data;

	// VALIDATE POST
		// $response->formErrors = $this->form->errors( $post );
		// if( $response->formErrors ){
			// return $response;
		// }

		foreach( $models as $model ){
			$res = $this->command->duplicate( $model );
			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
				return $response;
			}
		}

		if( ( count($models) > 1 ) ){
			$to = '..';
		}
		else {
			$newModel = $res;
			$to = '../' . $newModel->id;
		}

		$response->redirect = $to;
		$response->addMessage( '__Item Saved__' );

		return $response;
	}

	public function actionArchive( array $models, PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		$ids = $request->args[0];
		$post = $request->data;

	// VALIDATE POST
		// $response->formErrors = $this->form->errors( $post );
		// if( $response->formErrors ){
			// return $response;
		// }

		foreach( $models as $model ){
			$res = $this->command->archive( $model );
			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
				return $response;
			}
		}

		$to = is_array($ids) ? '..' : '.';
		$response->redirect = $to;
		$response->addMessage( '__Item Saved__' );

		return $response;
	}

	public function actionDelete( array $models, PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '..';

		$post = $request->data;

	// VALIDATE POST
		// $sure = isset( $post['sure'] ) ? $post['sure'] : FALSE;
		// if( ! $sure ){
			// $response->formErrors[ 'sure' ] = '__Required Field__';
		// }

		// $response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		foreach( $models as $model ){
			$res = $this->command->delete( $model );
			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
				return $response;
			}
		}

		$response->redirect = '..';
		$response->addMessage( '__Item Deleted__' );

		return $response;
	}

	public function actionRestore( array $models, PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		$ids = $request->args[0];
		$post = $request->data;

	// VALIDATE POST
		// $response->formErrors = $this->form->errors( $post );
		// if( $response->formErrors ){
			// return $response;
		// }

		foreach( $models as $model ){
			$res = $this->command->restore( $model );
			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
				return $response;
			}
		}

		$to = is_array($ids) ? '..' : '.';
		$response->redirect = $to;
		$response->addMessage( '__Item Saved__' );

		return $response;
	}
}