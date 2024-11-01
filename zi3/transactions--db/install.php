<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Transactions00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['transactions'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
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
				'refno' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'created_date' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'description' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),

				'contact_id' => array(
					'type' => 'INTEGER',
					'null' => TRUE,
					),

				'state' => array(
					'type' => 'VARCHAR(16)',
					'null' => FALSE,
					),
				'type' => array(
					'type' => 'VARCHAR(16)',
					'null' => FALSE,
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
				'transaction_id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'item_id' => array(
					'type' => 'INTEGER',
					'null' => FALSE,
					),
				'qty' => array(
					'type' => 'FLOAT',
					'null' => FALSE,
					),
				'price' => array(
					'type' => 'FLOAT',
					'null' => TRUE,
					),
				)
			);
		$this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->createTable( $this->_->tableNameLines() );
	}

	public function down1()
	{
		$this->dbForge->dropTable( $this->_->tableName() );
		$this->dbForge->dropTable( $this->_->tableNameLines() );
	}
}