<?php
class PW1_ZI3_Acl0Wp00Html_Acl0Wp0Reset_Index extends _PW1
{
	public $connectionsCommand;
	public $connectionsQuery;
	public $q;

	public function __construct(
		PW1_ZI3_Acl0Wp_Connections_Command $connectionsCommand,
		PW1_ZI3_Acl0Wp_Connections_Query $connectionsQuery,
		PW1_Q $q
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		if( ! current_user_can('update_plugins') ){
			echo 'you are not allowed to do this';
			exit;
		}

		$q = $this->q->construct();
		$q->where( 'id', '>', 0 );
		$connections = $this->connectionsQuery->find( $q );

		foreach( $connections as $e ){
			$this->connectionsCommand->delete( $e );
		}

		$response->redirect = '';
		$response->addMessage( '__Permissions Reset__' );
		return $response;
	}
}