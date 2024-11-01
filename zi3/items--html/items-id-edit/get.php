<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id0Edit_Get extends _PW1
{
	public $query;
	public $form;

	public function __construct(
		PW1_ZI3_Items_Query $query,
		PW1_ZI3_Items00Html_Widget_Form $form
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Edit__';
		return $response;
	}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$response->content = $this->self->render( $request );
		return $response;
	}

	public function render( $request )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		ob_start();
?>

<form method="post" action="URI:.">
<?php echo $this->form->render( $model ); ?>

<p>
<button type="submit">__Update Item__</button>
</p>

</form>

<?php
		return ob_get_clean();
	}
}