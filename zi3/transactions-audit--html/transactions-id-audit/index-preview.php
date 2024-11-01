<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Audit00Html_Transactions0Id0Audit_Index0Preview extends _PW1
{
	public $q;
	public $transactionsQuery;
	public $auditQuery;
	public $tf;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Audit_Query $auditQuery,
		PW1_Time_Format $tf
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Last Updated__';
		$response->menu['11-audit'] = array( './audit', '__View History__' );
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$transaction = $this->transactionsQuery->findById( $id );

		$q = $this->q
			->where( 'objectClass', '=', $transaction::_CLASS )
			->where( 'objectId', '=', $transaction->id )
			->limit( 1 )
			;

		$auditModels = $this->auditQuery->find( $q );
		if( ! $auditModels ) return;

		$e = current( $auditModels );
		ob_start();
?>

<div class="pw-box">
	<?php echo $this->tf->formatDateWithWeekday( $e->eventDateTime ); ?> <?php echo $this->tf->formatTime( $e->eventDateTime ); ?>
</div>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}
}