<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings_X_Backup_Export extends PW1_Common_Backup_Export
{
	public function __invoke()
	{
		$ret = call_user_func( $this->parent );

		$lines = call_user_func( $this->load );
		if( ! $lines ){
			return $ret;
		}

		$ret['settings'] = array();
		foreach( $lines as $name => $value ){
			$e = array( 'name' => $name, 'value' => $value );
			$ret['settings'][] = $e;
		}

		return $ret;
	}
}