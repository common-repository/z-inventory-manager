<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Acl0Wp00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['acl_wp'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
		return $ret;
	}

	public function up1()
	{
		$this->dbForge->addField(
			array(
				'id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					'auto_increment' => TRUE
					),
				'wp_user_id' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				'wp_role_id' => array(
					'type' => 'VARCHAR(64)',
					'null' => TRUE,
					),
				'role_id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				)
			);
		$this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->createTable( $this->_->tableNameConnections() );
	}

	public function down1()
	{
		$this->dbForge->dropTable( $this->_->tableNameConnections() );
	}
}