<?php
class PW1_ZI3_Transactions0Audit00Html_Widget extends _PW1
{
	public $financeWidget;
	public $tf;
	public $itemsQuery;
	public $itemsWidget;
	public $transactionStatesQuery;
	public $transactionsWidget;

	public function __construct(
		PW1_ZI3_Finance00Html_Widget $financeWidget,
		PW1_Time_Format $tf,
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Items00Html_Widget $itemsWidget,
		PW1_ZI3_Transactions_States_Query $transactionStatesQuery,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	private function _viewQtyPrice( $qty, $price )
	{
		$ret = $price ? $qty . ' x ' . $this->financeWidget->renderPrice( $price ) : $qty;
		return $ret;
	}

	public function presentUpdateLine( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@update-line' !== $model->objectClass . '@' . $model->eventName ) return;

		$itemId = $model->eventData['item_id'];
		$item = $this->itemsQuery->findById( $itemId );
		$itemView = $item ? $this->itemsWidget->presentTitle( $item, TRUE ) : '__Unknown Item__';

		$oldQtyPrice = $this->_viewQtyPrice( $model->eventData['qty_old'], $model->eventData['price_old'] );
		$newQtyPrice = $this->_viewQtyPrice( $model->eventData['qty_new'], $model->eventData['price_new'] );

		ob_start();
?>

<span title="__Item Changed__">
<?php echo $itemView; ?> <span style="text-decoration: line-through2;"><?php echo $oldQtyPrice; ?></span> &rarr; <?php echo $newQtyPrice; ?></span>
</span>

<?php
		return trim( ob_get_clean() );
	}

	public function presentCreateLine( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@create-line' !== $model->objectClass . '@' . $model->eventName ) return;

		$itemId = $model->eventData['item_id'];
		$qty = $model->eventData['qty'];
		$price = $model->eventData['price'];

		$item = $this->itemsQuery->findById( $itemId );
		$itemView = $item ? $this->itemsWidget->presentTitle( $item, TRUE ) : '__Unknown Item__';

		$qtyView = $this->_viewQtyPrice( $qty, $price );

		ob_start();
?>

<span title="__Item Added__">
<?php echo $itemView; ?> +<?php echo $qtyView; ?>
</span>

<?php
		return trim( ob_get_clean() );
	}

	public function presentDeleteLine( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@delete-line' !== $model->objectClass . '@' . $model->eventName ) return;

		$itemId = $model->eventData['item_id'];
		$qty = $model->eventData['qty'];
		$price = $model->eventData['price'];

		$item = $this->itemsQuery->findById( $itemId );
		$itemView = $item ? $this->itemsWidget->presentTitle( $item, TRUE ) : '__Unknown Item__';

		$qtyView = $this->_viewQtyPrice( $qty, $price );

		ob_start();
?>

<span title="__Item Deleted__" style="text-decoration: line-through;">
<?php echo $itemView; ?> <?php echo $qtyView; ?>
</span>

<?php
		return trim( ob_get_clean() );
	}

	public function presentChangeState( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@change-state' !== $model->objectClass . '@' . $model->eventName ) return;

		$fromState = isset( $model->eventData['from'] ) ? $model->eventData['from'] : NULL;
		$toState = isset( $model->eventData['to'] ) ? $model->eventData['to'] : NULL;

		ob_start();
?>

<ul class="pw-inline">

<li>
<?php echo $this->transactionsWidget->presentState( $fromState ); ?>
</li>

<li class="pw-xs-hide">&rarr;</li>
<li class="pw-lg-hide">&darr;</li>

<li>
<?php echo $this->transactionsWidget->presentState( $toState ); ?>
</li>

</ul>

<?php
		return ob_get_clean();
	}

	public function presentChangeRefno( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@change-refno' !== $model->objectClass . '@' . $model->eventName ) return;

		$from = isset( $model->eventData['from'] ) ? $model->eventData['from'] : NULL;
		$to = isset( $model->eventData['to'] ) ? $model->eventData['to'] : NULL;

		ob_start();
?>

<ul class="pw-inline">

<li>
__Reference__:
</li>

<li>
<?php echo esc_html( $from ); ?>
</li>

<li class="pw-xs-hide">&rarr;</li>
<li class="pw-lg-hide">&darr;</li>

<li>
<?php echo esc_html( $to ); ?>
</li>

</ul>

<?php
		return ob_get_clean();
	}

	public function presentChangeDate( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@change-date' !== $model->objectClass . '@' . $model->eventName ) return;

		$from = isset( $model->eventData['from'] ) ? $model->eventData['from'] : NULL;
		$to = isset( $model->eventData['to'] ) ? $model->eventData['to'] : NULL;

		ob_start();
?>

<ul class="pw-inline">

<li>
<?php echo $this->tf->formatDate( $from ); ?>
</li>

<li class="pw-xs-hide">&rarr;</li>
<li class="pw-lg-hide">&darr;</li>

<li>
<?php echo $this->tf->formatDate( $to ); ?>
</li>

</ul>

<?php
		return ob_get_clean();
	}

	public function presentCreate( _PW1_ZI3_Audit_Model $model )
	{
		if( 'transaction@create' !== $model->objectClass . '@' . $model->eventName ) return;

		ob_start();
?>

__Transaction Created__

<?php
		return trim( ob_get_clean() );
	}
}