<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Contacts00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['contacts'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
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
				'title' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'state' => array(
					'type' => 'VARCHAR(16)',
					'null' => TRUE,
					),

				'email' => array(
					'type' => 'VARCHAR(128)',
					'null' => TRUE,
					),
				'phone' => array(
					'type' => 'VARCHAR(128)',
					'null' => TRUE,
					),
				'description' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),

				'is_customer' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				'is_supplier' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),
				)
			);
		$this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->createTable( $this->_->tableName() );
	}

	public function down1()
	{
		$this->dbForge->dropTable( $this->_->tableName() );
	}
}