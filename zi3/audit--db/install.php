<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Audit00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['audit'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
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

				'object_class' => array(
					'type' => 'VARCHAR(32)',
					'null' => FALSE,
					),
				'object_id' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),

				'event_datetime' => array(
					'type' => 'BIGINT',
					'null' => FALSE,
					),
				'event_name' => array(
					'type' => 'VARCHAR(64)',
					'null' => FALSE,
					),
				'user_id' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				)
			);
		$this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->createTable( $this->_->tableName() );

		$this->dbForge->addField(
			array(
				'id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					'auto_increment' => TRUE
					),
				'audit_id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'data_name' => array(
					'type' => 'VARCHAR(32)',
					'null' => FALSE,
					),
				'data_value' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				)
			);
		$this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->createTable( $this->_->tableNameData() );
	}

	public function down1()
	{
		$this->dbForge->dropTable( $this->_->tableName() );
		$this->dbForge->dropTable( $this->_->tableNameData() );
	}
}