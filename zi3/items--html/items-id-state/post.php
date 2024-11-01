<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id0State_Post extends _PW1
{
	public $q;
	public $command;
	public $query;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Items_Command $command,
		PW1_ZI3_Items_Query $query
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];

		$to = '..';
		$response->redirect = $to;

		$post = $request->data;

		$state = isset( $request->params['state'] ) ? $request->params['state'] : NULL;
		$state = isset( $post['state'] ) ? $post['state'] : NULL;
		if( ! $state ){
			$response->formErrors['state'] = '__Required Field__';
			return $response;
		}

		$model = $this->query->findById( $id );

		$res = $this->command->changeState( $model, $state );

		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->addMessage( '__Item Saved__' );

		return $response;
	}
}