<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id_Index extends _PW1
{
	public $query;

	public function __construct(
		PW1_ZI3_Items_Query $query
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		if( is_array($id) && (count($id) > 1) ) return;

		$model = $this->query->findById( $id );
		$response->title = esc_html( $model->title );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		return $response;
	}
}