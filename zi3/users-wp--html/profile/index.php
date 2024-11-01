<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp00Html_Profile_Index extends _PW1
{
	public $query;
	public $widget;

	public function __construct(
		PW1_ZI3_Users_Query $query,
		PW1_ZI3_Users0Wp00Html_Widget $widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__My Profile__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->currentUserId;
		$model = $this->query->findById( $id );

		ob_start();
?>

<div class="pw-box">

<dl>
	<dt>
		__WordPress User__
	</dt>
	<dd>
		<?php echo $this->widget->presentTitle( $model, TRUE ); ?>
	</dd>
</dl>

</div>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}
}