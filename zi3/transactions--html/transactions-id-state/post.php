<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0State_Post extends _PW1
{
	public $command;
	public $query;

	public function __construct(
		PW1_ZI3_Transactions_Command $command,
		PW1_ZI3_Transactions_Query $query
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];

		$to = '..';
		$response->redirect = $to;

		$state = isset( $request->params['state'] ) ? $request->params['state'] : NULL;
		if( NULL === $state ){
			$response->formErrors['state'] = '__Required Field__';
			return $response;
		}

		$model = $this->query->findById( $id );
		$res = $this->command->changeState( $model, $state );

		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->addMessage( '__Transaction Saved__' );

		return $response;
	}
}