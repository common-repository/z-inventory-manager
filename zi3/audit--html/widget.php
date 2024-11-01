<?php
class PW1_ZI3_Audit00Html_Widget extends _PW1
{
	public function present( _PW1_ZI3_Audit_Model $model )
	{
		$ret = $model->objectClass . '@' . $model->eventName;
		return $ret;
	}
}