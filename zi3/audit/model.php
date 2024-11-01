<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Audit_Model
{
	public $id;

	public $objectClass;
	public $objectId;

	public $eventDateTime;
	public $eventName;
	public $eventData;

	public $user_id;

	public function __set( $name, $value )
	{
		$msg = 'Invalid property for setting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}

	public function __get( $name )
	{
		$msg = 'Invalid property for getting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}
}

class PW1_ZI3_Audit_Model extends _PW1
{
	private $_currentUserId = NULL;
	public $t;

	public function __construct(
		PW1_Time_ $t
	)
	{}

	public function withCurrentUserId( $userId )
	{
		$this->_currentUserId = $userId;
		return $this;
	}

	public function construct()
	{
		$class = '_' . __CLASS__;

		$ret = new $class;
		$ret->eventDateTime = $this->t->setNow()->getDateTimeDb();
		$ret->user_id = $this->_currentUserId;

		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Audit_Model $ret = NULL )
	{
		if( NULL === $ret ){
			$ret = $this->self->construct();
		}

		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->eventDateTime = $array['event_datetime'];
		$ret->eventName = $array['event_name'];
		// $ret->eventData = isset($array['event_data']) && strlen($array['event_data']) ? json_decode( $array['event_data'], TRUE ) : array();
		$ret->eventData = isset($array['event_data']) ? $array['event_data'] : array();

		$ret->objectClass = $array['object_class'];
		$ret->objectId = isset( $array['object_id'] ) ? $array['object_id'] : NULL;
		$ret->user_id = isset( $array['user_id'] ) ? $array['user_id'] : NULL;

		return $ret;
	}

	public function toArray( _PW1_ZI3_Audit_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}

		$ret['event_datetime'] = $model->eventDateTime;
		$ret['event_name'] = $model->eventName;
		// $ret['event_data'] = $model->eventData ? json_encode( $model->eventData ) : NULL;
		$ret['event_data'] = $model->eventData ? $model->eventData : array();

		$ret['object_class'] = $model->objectClass;
		$ret['object_id'] = $model->objectId;
		$ret['user_id'] = $model->user_id;

		return $ret;
	}
}