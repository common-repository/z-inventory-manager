<?php
class PW1_ZI3_Transactions0Audit_Command extends _PW1
{
	public $q;
	public $transactionsQuery;
	public $auditModel;
	public $auditCommand;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_Query $transactionsQuery,

		PW1_ZI3_Audit_Model $auditModel,
		PW1_ZI3_Audit_Command $auditCommand
	)
	{}

	public function listenDelete( _PW1_ZI3_Transactions_Model $model )
	{
		$q = $this->q
			->where( 'object_class', '=', $model::_CLASS )
			->where( 'object_id', '=', $model->id )
			;
		$this->auditCommand->delete( $q );
	}

	public function listenStateChange( _PW1_ZI3_Transactions_Model $old, _PW1_ZI3_Transactions_Model $new )
	{
		if( $new->state == $old->state ) return;

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $old::_CLASS;
		$auditModel->objectId = $old->id;
		$auditModel->eventName = 'change-state';
		$auditModel->eventData = array( 'from' => $old->state, 'to' => $new->state );

		$this->auditCommand->create( $auditModel );
	}

	public function listenDateChange( _PW1_ZI3_Transactions_Model $old, _PW1_ZI3_Transactions_Model $new )
	{
		if( $new->created_date == $old->created_date ) return;

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $old::_CLASS;
		$auditModel->objectId = $old->id;
		$auditModel->eventName = 'change-date';
		$auditModel->eventData = array( 'from' => $old->created_date, 'to' => $new->created_date );

		$this->auditCommand->create( $auditModel );
	}

	public function listenRefnoChange( _PW1_ZI3_Transactions_Model $old, _PW1_ZI3_Transactions_Model $new )
	{
		if( $new->refno == $old->refno ) return;

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $old::_CLASS;
		$auditModel->objectId = $old->id;
		$auditModel->eventName = 'change-refno';
		$auditModel->eventData = array( 'from' => $old->refno, 'to' => $new->refno );

		$this->auditCommand->create( $auditModel );
	}

	public function wrapCreate( _PW1_ZI3_Transactions_Model $model )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		$ret = call_user_func_array( $context->parentFunc, $args );
		if( $ret instanceof PW1_Error ) return $ret;

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $ret::_CLASS;
		$auditModel->objectId = $ret->id;
		$auditModel->eventName = 'create';

		$this->auditCommand->create( $auditModel );

		return $ret;
	}

	public function listenLinesDelete( _PW1_ZI3_Transactions_Lines_Model $lineModel )
	{
		if( ! $lineModel->transaction_id ) return;

		$model = $this->transactionsQuery->findById( $lineModel->transaction_id );

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $model::_CLASS;
		$auditModel->objectId = $model->id;
		$auditModel->eventName = 'delete-line';
		$auditModel->eventData = array( 'item_id' => $lineModel->item_id, 'qty' => $lineModel->qty, 'price' => $lineModel->price );

		$this->auditCommand->create( $auditModel );
	}

	public function listenLinesCreate( _PW1_ZI3_Transactions_Lines_Model $lineModel )
	{
		if( ! $lineModel->transaction_id ) return;

		$model = $this->transactionsQuery->findById( $lineModel->transaction_id );

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $model::_CLASS;
		$auditModel->objectId = $model->id;
		$auditModel->eventName = 'create-line';
		$auditModel->eventData = array( 'item_id' => $lineModel->item_id, 'qty' => $lineModel->qty, 'price' => $lineModel->price );

		$this->auditCommand->create( $auditModel );
	}

	public function listenLinesUpdate( _PW1_ZI3_Transactions_Lines_Model $old, _PW1_ZI3_Transactions_Lines_Model $new )
	{
		if( ! $old->transaction_id ) return;

		if( ! (($old->price != $new->price) OR ($old->qty != $new->qty)) ){
			return;
		}

		$model = $this->transactionsQuery->findById( $old->transaction_id );

		$auditModel = $this->auditModel->construct();

		$auditModel->objectClass = $model::_CLASS;
		$auditModel->objectId = $model->id;
		$auditModel->eventName = 'update-line';

		$eventData = array( 'item_id' => $old->item_id );
		$eventData['price_old'] = $old->price;
		$eventData['qty_old'] = $old->qty;
		$eventData['price_new'] = $new->price;
		$eventData['qty_new'] = $new->qty;

		$auditModel->eventData = $eventData;

		$this->auditCommand->create( $auditModel );
	}
}