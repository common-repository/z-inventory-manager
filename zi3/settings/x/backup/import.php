<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings_X_Backup_Import extends PW1_Common_Backup_Import
{
	public function __invoke( array $data )
	{
		$data = call_user_func( $this->parent, $data );

		$k = 'settings';
		if( ! isset($data[$k]) ){
			return $data;
		}

		foreach( $data[$k] as $e ){
			call_user_func( $this->saveSetting, $e['name'], $e['value'] );
		}

		unset( $data[$k] );
		return $data;
	}
}