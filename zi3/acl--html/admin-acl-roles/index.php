<?php
class PW1_ZI3_Acl00Html_Admin0Acl0Roles_Index extends _PW1
{
	public $q;
	public $query;
	public $widget;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Acl_Roles_Query $query,
		PW1_ZI3_Acl00Html_Widget_Roles $widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Permission Roles__';

		$response->menu[ '11-new' ] = array( './new', '<span>+</span>__Add New__</a>' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$q = $this->q->construct();
		$models = $this->query->find( $q );

		ob_start();
?>

<table>
<thead>
	<tr>
		<td class="pw-col-3">__Role Name__</td>
		<td>__Permissions__</td>
	</tr>
</thead>

<tbody>
<?php foreach( $models as $e ) : ?>
	<tr>
		<td>
			<?php echo $this->widget->presentTitle( $e, TRUE ); ?>
		</td>
		<td>
			<?php echo $this->widget->presentPermissions( $e ); ?>
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