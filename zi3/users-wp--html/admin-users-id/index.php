<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp00Html_Admin0Users0Id_Index extends _PW1
{
	public $query;
	public $widget;
	public $q;

	public function __construct(
		PW1_ZI3_Users_Query		$query,
		PW1_ZI3_Users0Wp00Html_Widget	$widget,

		PW1_Q	$q
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		$response->title = esc_html( $model->title );
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
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