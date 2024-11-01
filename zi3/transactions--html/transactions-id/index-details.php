<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id_Index0Details extends _PW1
{
	public $q;
	public $tf;
	public $transactionsQuery;
	public $transactionsWidget;

	public function __construct(
		PW1_Q $q,
		PW1_Time_Format $tf,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		ob_start();
?>

<section>

<h2>__Details__</h2>

<article>
	<div class="pw-grid">
		<div class="pw-col-6">
			<dl>
				<dt>
					__Reference__
				</dt>
				<dd>
					<?php echo esc_html( $model->refno ); ?>
				</dd>
			</dl>
		</div>

		<div class="pw-col-4">
			<dl>
				<dt>
					__Date__
				</dt>
				<dd>
					<?php echo $this->tf->formatDateWithWeekday( $model->created_date ); ?>
				</dd>
			</dl>
		</div>

		<div class="pw-col-2">
			<dl>
				<dt>
					__State__
				</dt>
				<dd>
					<?php echo $this->transactionsWidget->presentState( $model->state ); ?>
				</dd>
			</dl>
		</div>
	</div>

	<?php if( strlen($model->description) ) : ?>
		<div>
			<dt>
				__Description__
			</dt>
			<dd>
				<?php echo esc_html( $model->description ); ?>
			</dd>
		</div>
	<?php endif; ?>
</article>

<nav>
<ul class="pw-inline">
<li>
	<a href="URI:./edit">__Edit__</a>
</li>
</ul>
</nav>

</section>

<?php
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}