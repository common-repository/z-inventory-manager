<?php if (! defined('ABSPATH')) exit;
class PW1_Sql_Forge extends _PW1
{
	public $fields		= array();
	public $keys		= array();
	public $primary_keys	= array();
	public $db_char_set	= '';
	protected $_drop_database	= 'DROP DATABASE %s';
	protected $_create_table	= "%s %s (%s\n)";
	protected $_create_table_if	= 'CREATE TABLE IF NOT EXISTS';
	protected $_drop_table_if	= 'DROP TABLE IF EXISTS';
	protected $_rename_table	= 'ALTER TABLE %s RENAME TO %s;';
	protected $_default		= ' DEFAULT ';
	protected $_create_database	= 'CREATE DATABASE %s CHARACTER SET %s COLLATE %s';
	protected $_create_table_keys	= TRUE;
	protected $_unsigned		= array(
		'TINYINT',
		'SMALLINT',
		'MEDIUMINT',
		'INT',
		'INTEGER',
		'BIGINT',
		'REAL',
		'DOUBLE',
		'DOUBLE PRECISION',
		'FLOAT',
		'DECIMAL',
		'NUMERIC'
	);

	protected $_null = 'NULL';
	public $db;

	public function __construct(
		PW1_Sql_	$db
		)
	{}

	public function addField( array $field )
	{
		$this->fields = array_merge( $this->fields, $field );
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Create database
	 *
	 * @param	string	$db_name
	 * @return	bool
	 */
	public function create_database($db_name)
	{
		if ($this->_create_database === FALSE)
		{
			return $this->display_error('db_unsupported_feature');
		}
		elseif ( ! $this->sql->query(sprintf($this->_create_database, $db_name, $this->db->char_set, $this->db->dbcollat)))
		{
			return $this->display_error('db_unable_to_drop');
		}

		if ( ! empty($this->db->data_cache['db_names']))
		{
			$this->db->data_cache['db_names'][] = $db_name;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @param	string	$db_name
	 * @return	bool
	 */
	public function drop_database($db_name)
	{
		if ($this->_drop_database === FALSE)
		{
			return $this->display_error('db_unsupported_feature');
		}
		elseif ( ! $this->sql->query(sprintf($this->_drop_database, $db_name)) )
		{
			return $this->display_error('db_unable_to_drop');
		}

		if ( ! empty($this->db->data_cache['db_names']))
		{
			$key = array_search(strtolower($db_name), array_map('strtolower', $this->db->data_cache['db_names']), TRUE);
			if ($key !== FALSE)
			{
				unset($this->db->data_cache['db_names'][$key]);
			}
		}

		return TRUE;
	}

	public function addKey($key, $primary = FALSE)
	{
		if( TRUE === $primary ){
			$this->primary_keys[] = $key;
		}
		else {
			$this->keys[] = $key;
		}
		return $this;
	}

	public function createTable( $table, $if_not_exists = TRUE, array $attributes = array() )
	{
		$ret = array();

		if( ! strlen($table) ){
			exit( __METHOD__ . ': ' . __LINE__ . ': ' . 'A table name is required for that operation.' );
		}

		if( ! count($this->fields) ){
			exit( __METHOD__ . ': ' . __LINE__ . ': ' . 'Field information is required.' );
		}

		$ret[] = $this->_create_table( $table, $if_not_exists, $attributes );
		if ( ! empty($this->keys)){
			$ret = array_merge( $ret, $this->_process_indexes($table) );
		}

		foreach( $ret as $sql ){
			$this->db->query( $sql );
		}

		$this->_reset();

		return $ret;
	}

	protected function _create_table( $table, $if_not_exists, $attributes )
	{
		if ($if_not_exists === TRUE && $this->_create_table_if === FALSE){
			if ($this->db->table_exists($table)){
				return TRUE;
			}
			else {
				$if_not_exists = FALSE;
			}
		}

		$sql = ( $if_not_exists ) ? sprintf($this->_create_table_if, $table) : 'CREATE TABLE';

		$columns = $this->_process_fields(TRUE);
		for ($i = 0, $c = count($columns); $i < $c; $i++)
		{
			$columns[$i] = ($columns[$i]['_literal'] !== FALSE)
					? "\n\t".$columns[$i]['_literal']
					: "\n\t".$this->_process_column($columns[$i]);
		}

		$columns = implode(',', $columns)
				.$this->_process_primary_keys($table);

		// Are indexes created from within the CREATE TABLE statement? (e.g. in MySQL)
		if ($this->_create_table_keys === TRUE)
		{
			$columns .= $this->_process_indexes($table);
		}

		// _create_table will usually have the following format: "%s %s (%s\n)"
		$sql = sprintf( $this->_create_table.'%s',
			$sql,
			$table,
			$columns,
			$this->_create_table_attr($attributes)
		);

		return $sql;
	}

	public function dropTable( $table, $if_exists = TRUE )
	{
		$ret = array();

		if( ! strlen($table) ){
			exit( __METHOD__ . ': ' . __LINE__ . ': ' . 'A table name is required for that operation.' );
		}

		if (($query = $this->_drop_table($table, $if_exists)) === TRUE){
			return $ret;
		}

		$ret[] = $query;

		foreach( $ret as $sql ){
			$this->db->query( $sql );
		}

		return $ret;
	}

	protected function _drop_table( $table, $if_exists )
	{
		$sql = 'DROP TABLE';

		if( $if_exists ){
			if ($this->_drop_table_if === FALSE){
				if ( ! $this->db->table_exists($table)){
					return TRUE;
				}
			}
			else {
				$sql = sprintf( $this->_drop_table_if, $table );
			}
		}

		return $sql . ' ' . $table;
	}

	public function field_exists( $fieldName, $table )
	{
		$return = FALSE;
		$sql = 'SHOW COLUMNS FROM '. $this->queryBuilder->getPrefix() . $table;

		$currentFields = array();
		$result = $this->sql->query( $sql );

		if( ! $result ){
			return $return;
		}

		foreach( $result as $r ){
			if( array_key_exists('Field', $r) ){ // mysql
				$thisFieldName = $r['Field'];
			}
			else { // sqlite
				$thisFieldName = $r['name'];
			}

			$currentFields[ $thisFieldName ] = $r;
		}

		if( array_key_exists($fieldName, $currentFields) ){
			$return = TRUE;
		}

		return $return;
	}

	public function add_index( $table, $field )
	{
		$dbPrefix = $this->queryBuilder->getPrefix();
		$sql = "ALTER TABLE `{PRFX}$table` ADD INDEX (`$field`)";
		$sql = str_replace('{PRFX}', $dbPrefix, $sql );
		$this->sql->query( $sql );
	}

	/**
	 * Column Add
	 *
	 * @todo	Remove deprecated $_after option in 3.1+
	 * @param	string	$table	Table name
	 * @param	array	$field	Column definition
	 * @param	string	$_after	Column for AFTER clause (deprecated)
	 * @return	bool
	 */
	public function add_column( $table, $field, $_after = NULL )
	{
		// Work-around for literal column definitions
		is_array($field) OR $field = array($field);

		foreach( array_keys($field) as $k ){
			if( $this->field_exists($k, $table) ){
				continue;
			}

			// Backwards-compatibility work-around for MySQL/CUBRID AFTER clause (remove in 3.1+)
			if ($_after !== NULL && is_array($field[$k]) && ! isset($field[$k]['after'])){
				$field[$k]['after'] = $_after;
			}

			$this->add_field(array($k => $field[$k]));
		}

		$sqls = $this->_alter_table('ADD', $this->queryBuilder->getPrefix().$table, $this->_process_fields());
		$this->_reset();
		if ($sqls === FALSE){
			return $this->display_error('db_unsupported_feature');
		}

		for ($i = 0, $c = count($sqls); $i < $c; $i++){
			if( $this->sql->query($sqls[$i]) === FALSE ){
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Column Drop
	 *
	 * @param	string	$column_name	Column name
	 * @param	string	$table		Table name
	 * @return	bool
	 */
	public function drop_column($table, $column_name)
	{
		$sql = $this->_alter_table('DROP', $this->queryBuilder->getPrefix().$table, $column_name);
		if ($sql === FALSE)
		{
			return $this->display_error('db_unsupported_feature');
		}
		return $this->sql->query( $sql );
	}

	// --------------------------------------------------------------------

	/**
	 * Column Modify
	 *
	 * @param	string	$table	Table name
	 * @param	string	$field	Column definition
	 * @return	bool
	 */
	public function modify_column($table, $field)
	{
		// Work-around for literal column definitions
		is_array($field) OR $field = array($field);

		foreach (array_keys($field) as $k)
		{
			$this->add_field(array($k => $field[$k]));
		}

		if (count($this->fields) === 0)
		{
			show_error('Field information is required.');
		}

		$sqls = $this->_alter_table('CHANGE', $this->queryBuilder->getPrefix().$table, $this->_process_fields());
		$this->_reset();
		if ($sqls === FALSE)
		{
			return $this->display_error('db_unsupported_feature');
		}

		for ($i = 0, $c = count($sqls); $i < $c; $i++)
		{
			if( $this->sql->query($sqls[$i]) === FALSE )
			{
				return FALSE;
			}
		}

		return TRUE;
	}


	/**
	 * Process fields
	 *
	 * @param	bool	$create_table
	 * @return	array
	 */
	protected function _process_fields($create_table = FALSE)
	{
		$fields = array();

		foreach ($this->fields as $key => $attributes)
		{
			if (is_int($key) && ! is_array($attributes))
			{
				$fields[] = array('_literal' => $attributes);
				continue;
			}

			$attributes = array_change_key_case($attributes, CASE_UPPER);

			if ($create_table === TRUE && empty($attributes['TYPE']))
			{
				continue;
			}

			isset($attributes['TYPE']) && $this->_attr_type($attributes);

			$field = array(
				'name'			=> $key,
				'new_name'		=> isset($attributes['NAME']) ? $attributes['NAME'] : NULL,
				'type'			=> isset($attributes['TYPE']) ? $attributes['TYPE'] : NULL,
				'length'		=> '',
				'unsigned'		=> '',
				'null'			=> '',
				'unique'		=> '',
				'default'		=> '',
				'auto_increment'	=> '',
				'_literal'		=> FALSE
			);

			isset($attributes['TYPE']) && $this->_attr_unsigned($attributes, $field);

			if ($create_table === FALSE)
			{
				if (isset($attributes['AFTER']))
				{
					$field['after'] = $attributes['AFTER'];
				}
				elseif (isset($attributes['FIRST']))
				{
					$field['first'] = (bool) $attributes['FIRST'];
				}
			}

			$this->_attr_default($attributes, $field);

			if (isset($attributes['NULL']))
			{
				if ($attributes['NULL'] === TRUE)
				{
					$field['null'] = empty($this->_null) ? '' : ' '.$this->_null;
				}
				else
				{
					$field['null'] = ' NOT NULL';
				}
			}
			elseif ($create_table === TRUE)
			{
				$field['null'] = ' NOT NULL';
			}

			$this->_attr_auto_increment($attributes, $field);
			$this->_attr_unique($attributes, $field);

			if (isset($attributes['COMMENT']))
			{
				// $field['comment'] = $this->db->escape($attributes['COMMENT']);
				$field['comment'] = $attributes['COMMENT'];
			}

			if (isset($attributes['TYPE']) && ! empty($attributes['CONSTRAINT']))
			{
				switch (strtoupper($attributes['TYPE']))
				{
					case 'ENUM':
					case 'SET':
						// $attributes['CONSTRAINT'] = $this->db->escape($attributes['CONSTRAINT']);
						$attributes['CONSTRAINT'] = $attributes['CONSTRAINT'];
					default:
						$field['length'] = is_array($attributes['CONSTRAINT'])
							? '('.implode(',', $attributes['CONSTRAINT']).')'
							: '('.$attributes['CONSTRAINT'].')';
						break;
				}
			}

			$fields[] = $field;
		}

		return $fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute TYPE
	 *
	 * Performs a data type mapping between different databases.
	 *
	 * @param	array	&$attributes
	 * @return	void
	 */
	protected function _attr_type(&$attributes)
	{
		// Usually overridden by drivers
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute UNSIGNED
	 *
	 * Depending on the _unsigned property value:
	 *
	 *	- TRUE will always set $field['unsigned'] to 'UNSIGNED'
	 *	- FALSE will always set $field['unsigned'] to ''
	 *	- array(TYPE) will set $field['unsigned'] to 'UNSIGNED',
	 *		if $attributes['TYPE'] is found in the array
	 *	- array(TYPE => UTYPE) will change $field['type'],
	 *		from TYPE to UTYPE in case of a match
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_unsigned(&$attributes, &$field)
	{
		if (empty($attributes['UNSIGNED']) OR $attributes['UNSIGNED'] !== TRUE)
		{
			return;
		}

		// Reset the attribute in order to avoid issues if we do type conversion
		$attributes['UNSIGNED'] = FALSE;

		if (is_array($this->_unsigned))
		{
			foreach (array_keys($this->_unsigned) as $key)
			{
				if (is_int($key) && strcasecmp($attributes['TYPE'], $this->_unsigned[$key]) === 0)
				{
					$field['unsigned'] = ' UNSIGNED';
					return;
				}
				elseif (is_string($key) && strcasecmp($attributes['TYPE'], $key) === 0)
				{
					$field['type'] = $key;
					return;
				}
			}

			return;
		}

		$field['unsigned'] = ($this->_unsigned === TRUE) ? ' UNSIGNED' : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute DEFAULT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_default(&$attributes, &$field)
	{
		if( $this->_default === FALSE ){
			return;
		}

		if (array_key_exists('DEFAULT', $attributes))
		{
			if ($attributes['DEFAULT'] === NULL)
			{
				$field['default'] = empty($this->_null) ? '' : $this->_default.$this->_null;

				// Override the NULL attribute if that's our default
				$attributes['NULL'] = TRUE;
				$field['null'] = empty($this->_null) ? '' : ' '.$this->_null;
			}
			else
			{
				// $field['default'] = $this->_default.$qb->escape($attributes['DEFAULT']);
				$field['default'] = $this->_default . $attributes['DEFAULT'];
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute UNIQUE
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_unique(&$attributes, &$field)
	{
		if ( ! empty($attributes['UNIQUE']) && $attributes['UNIQUE'] === TRUE)
		{
			$field['unique'] = ' UNIQUE';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute AUTO_INCREMENT
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	protected function _attr_auto_increment(&$attributes, &$field)
	{
		if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos($field['type'], 'int') !== FALSE)
		{
			$field['auto_increment'] = ' AUTO_INCREMENT';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Process primary keys
	 *
	 * @param	string	$table	Table name
	 * @return	string
	 */
	protected function _process_primary_keys($table)
	{
		$sql = '';

		for ($i = 0, $c = count($this->primary_keys); $i < $c; $i++)
		{
			if ( ! isset($this->fields[$this->primary_keys[$i]])){
				unset($this->primary_keys[$i]);
			}
		}

		if (count($this->primary_keys) > 0)
		{
			$sql .= ",\n\tCONSTRAINT ". 'pk_'.$table
				.' PRIMARY KEY('.implode(', ', $this->primary_keys).')';
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Reset
	 *
	 * Resets table creation vars
	 *
	 * @return	void
	 */
	protected function _reset()
	{
		$this->fields = $this->keys = $this->primary_keys = array();
	}

	// --------------------------------------------------------------------

	/**
	 * CREATE TABLE attributes
	 *
	 * @param	array	$attributes	Associative array of table attributes
	 * @return	string
	 */
	protected function _create_table_attr($attributes)
	{
		$sql = '';

		foreach (array_keys($attributes) as $key)
		{
			if (is_string($key))
			{
				$sql .= ' '.strtoupper($key).' = '.$attributes[$key];
			}
		}

		if ( ! empty($this->db->char_set) && ! strpos($sql, 'CHARACTER SET') && ! strpos($sql, 'CHARSET'))
		{
			$sql .= ' DEFAULT CHARACTER SET = '.$this->db->char_set;
		}

		if ( ! empty($this->db->dbcollat) && ! strpos($sql, 'COLLATE'))
		{
			$sql .= ' COLLATE = '.$this->db->dbcollat;
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _parent_alter_table($alter_type, $table, $field)
	{
		$qb = $this->queryBuilder->construct();

		$sql = 'ALTER TABLE '.$qb->escape_identifiers($table).' ';

		// DROP has everything it needs now.
		if ($alter_type === 'DROP')
		{
			return $sql.'DROP COLUMN '.$qb->escape_identifiers($field);
		}

		$sql .= ($alter_type === 'ADD')
			? 'ADD '
			: $alter_type.' COLUMN ';

		$sqls = array();
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			$sqls[] = $sql
				.($field[$i]['_literal'] !== FALSE ? $field[$i]['_literal'] : $this->_process_column($field[$i]));
		}

		return $sqls;
	}

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	protected function _alter_table($alter_type, $table, $field)
	{
		$qb = $this->queryBuilder->construct();

		if ($alter_type === 'DROP')
		{
			return $this->_parent_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE '.$qb->escape_identifiers($table);
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				$field[$i] = ($alter_type === 'ADD')
						? "\n\tADD ".$field[$i]['_literal']
						: "\n\tMODIFY ".$field[$i]['_literal'];
			}
			else
			{
				if ($alter_type === 'ADD')
				{
					$field[$i]['_literal'] = "\n\tADD ";
				}
				else
				{
					$field[$i]['_literal'] = empty($field[$i]['new_name']) ? "\n\tMODIFY " : "\n\tCHANGE ";
				}

				$field[$i] = $field[$i]['_literal'].$this->_process_column($field[$i]);
			}
		}

		return array($sql.implode(',', $field));
	}

	// --------------------------------------------------------------------

	/**
	 * Process column
	 *
	 * @param	array	$field
	 * @return	string
	 */
	protected function _process_column($field)
	{
		// $extra_clause = isset( $field['after'] ) ? ' AFTER '.$qb->escape_identifiers($field['after']) : '';
		$extra_clause = '';

		if (empty($extra_clause) && isset($field['first']) && $field['first'] === TRUE)
		{
			$extra_clause = ' FIRST';
		}

		return $field['name']
			.(empty($field['new_name']) ? '' : ' '.$field['new_name'])
			.' '.$field['type'].$field['length']
			.$field['unsigned']
			.$field['null']
			.$field['default']
			.$field['auto_increment']
			.$field['unique']
			.(empty($field['comment']) ? '' : ' COMMENT '.$field['comment'])
			.$extra_clause;
	}

	// --------------------------------------------------------------------

	/**
	 * Process indexes
	 *
	 * @param	string	$table	(ignored)
	 * @return	string
	 */
	protected function _process_indexes($table)
	{
		$sql = '';

		for ($i = 0, $c = count($this->keys); $i < $c; $i++)
		{
			if (is_array($this->keys[$i]))
			{
				for ($i2 = 0, $c2 = count($this->keys[$i]); $i2 < $c2; $i2++)
				{
					if ( ! isset($this->fields[$this->keys[$i][$i2]]))
					{
						unset($this->keys[$i][$i2]);
						continue;
					}
				}
			}
			elseif ( ! isset($this->fields[$this->keys[$i]]))
			{
				unset($this->keys[$i]);
				continue;
			}

			is_array($this->keys[$i]) OR $this->keys[$i] = array($this->keys[$i]);

			$sql .= ",\n\tKEY ".implode('_', $this->keys[$i])
				.' ('.implode(', ', $this->keys[$i]).')';
		}

		$this->keys = array();

		return $sql;
	}

	public function display_error( $error = '' )
	{
		if( ! $this->debug ){
			return FALSE;
		}

		$heading = 'A Database Error Occurred';
		$message = array();
		$message[] = $error;

		$trace = debug_backtrace();
		foreach ($trace as $call){
			if (isset($call['file']) && strpos($call['file'], 'database') === FALSE){
				// Found it - use a relative path for safety
				$safe_file_name = str_replace(array(), '', $call['file']);
				$safe_file_name = basename( $safe_file_name );

				// $message[] = 'Filename: ' . $safe_file_name;
				// $message[] = 'Line Number: '. $call['line'];

				break;
			}
		}

		if( is_array($message) ){
			$message = join('<br>', $message);
		}

		echo $heading . '<br>' . $message;
		exit;
		// echo hc_show_detailed_error($heading, $message, 500);
		// exit;
	}
}
