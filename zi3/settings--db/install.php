<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Settings00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['settings'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
		return $ret;
	}

	public function up1()
	{
		$this->dbForge->addField(
			array(
				// 'id' => array(
					// 'type' => 'INTEGER',
					// 'null' => FALSE,
					// 'auto_increment' => TRUE
					// ),
				'name' => array(
					'type' => 'VARCHAR(255)',
					'null' => FALSE,
					),
				'value' => array(
					'type'		=> 'TEXT',
					'null'		=> TRUE,
					),
				)
			);
		// $this->dbForge->addKey( 'id', TRUE );
		$this->dbForge->addKey( 'name', TRUE );
		$this->dbForge->createTable( $this->_->tableName() );
	}

	public function down1()
	{
		$this->dbForge->dropTable( $this->_->tableName() );
	}
}