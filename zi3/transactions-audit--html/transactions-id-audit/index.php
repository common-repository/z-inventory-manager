<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Audit00Html_Transactions0Id0Audit_Index extends _PW1
{
	public $q;
	public $usersQuery;
	public $usersWidget;
	public $transactionsQuery;
	public $auditQuery;
	public $auditWidget;
	public $tf;

	public function __construct(
		PW1_Q $q,

		PW1_ZI3_Users_Query $usersQuery,
		PW1_ZI3_Users00Html_Widget $usersWidget,

		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Audit_Query $auditQuery,
		PW1_ZI3_Audit00Html_Widget $auditWidget,
		PW1_Time_Format $tf
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__History__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$transaction = $this->transactionsQuery->findById( $id );

		$q = $this->q
			->where( 'objectClass', '=', $transaction::_CLASS )
			->where( 'objectId', '=', $transaction->id )
			;

		$models = $this->auditQuery->find( $q );

		ob_start();
?>

<table>
<thead>
<tr>
	<td class="pw-col-3">
		__Time__
	</td>
	<td>
		__Event__
	</td>
	<td class="pw-col-3">
		__User__
	</td>
</tr>
</thead>

<tbody>
<?php foreach( $models as $e ) : ?>
	<tr>
		<td>
			<?php echo $this->tf->formatDateWithWeekday( $e->eventDateTime ); ?> <?php echo $this->tf->formatTime( $e->eventDateTime ); ?>
		</td>
		<td>
			<?php echo $this->auditWidget->present( $e ); ?>
		</td>
		<td>
			<?php
			$user = $e->user_id ? $this->usersQuery->findById( $e->user_id ) : NULL;
			?>
			<?php if( $user ) : ?>
				<?php echo $this->usersWidget->presentTitle( $user, TRUE ); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>

</table>

<?php
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;

	}
}