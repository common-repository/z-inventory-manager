<?php if (! defined('ABSPATH')) exit;
class PW1_Sql_QueryBuilder extends _PW1
{
	public function __construct()
	{}

	public function construct()
	{
		$class = '_' . __CLASS__;
		$ret = new $class;
		return $ret;
	}
}
class _PW1_Sql_QueryBuilder
{
	public $dbprefix		= '';
	public $char_set		= 'utf8';
	public $swap_pre		= '';
	public $db_debug		= FALSE;
	public $db_debug_level	= 'low';
	public $benchmark		= 0;
	public $bind_marker		= '?';
	public $save_queries		= TRUE;
	public $queries			= array();
	public $query_times		= array();
	public $trans_enabled		= TRUE;
	public $trans_strict		= TRUE;
	protected $_trans_depth		= 0;
	protected $_trans_status	= TRUE;
	protected $_trans_failure	= FALSE;
	public $cache_on		= FALSE;
	public $cachedir		= '';
	public $cache_autodel		= FALSE;
	public $CACHE;
	protected $_protect_identifiers		= TRUE;
	protected $_reserved_identifiers	= array('*');
	protected $_escape_char = '`';
	protected $_like_escape_str = " ESCAPE '%s' ";
	protected $_like_escape_chr = '!';
	protected $_random_keyword = array('RAND()', 'RAND(%d)');
	protected $_count_string = 'SELECT COUNT(*) AS ';
	protected $return_delete_sql		= FALSE;
	protected $reset_delete_data		= FALSE;
	protected $qb_select			= array();
	protected $qb_distinct			= FALSE;
	protected $qb_from			= array();
	protected $qb_join			= array();
	protected $qb_join_tables	= array();
	protected $qb_where			= array();
	protected $qb_groupby			= array();
	protected $qb_having			= array();
	protected $qb_keys			= array();
	protected $qb_limit			= FALSE;
	protected $qb_offset			= FALSE;
	protected $qb_orderby			= array();
	protected $qb_set			= array();
	protected $qb_aliased_tables		= array();
	protected $qb_where_group_started	= FALSE;
	protected $qb_where_group_count		= 0;
	protected $qb_caching				= FALSE;
	protected $qb_cache_exists			= array();
	protected $qb_cache_select			= array();
	protected $qb_cache_from			= array();
	protected $qb_cache_join			= array();
	protected $qb_cache_where			= array();
	protected $qb_cache_groupby			= array();
	protected $qb_cache_having			= array();
	protected $qb_cache_orderby			= array();
	protected $qb_cache_set				= array();
	protected $qb_no_escape 			= array();
	protected $qb_cache_no_escape			= array();

	public function __construct()
	{}

	/**
	 * Select
	 *
	 * Generates the SELECT portion of the query
	 *
	 * @param	string
	 * @param	mixed
	 * @return	CI_DB_query_builder
	 */
	public function select($select = '*', $escape = NULL)
	{
		if (is_string($select))
		{
			$select = explode(',', $select);
		}

		// If the escape value was not set, we will base it on the global setting
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($select as $val)
		{
			$val = trim($val);

			if ($val !== '')
			{
				$this->qb_select[] = $val;
				$this->qb_no_escape[] = $escape;

				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_select[] = $val;
					$this->qb_cache_exists[] = 'select';
					$this->qb_cache_no_escape[] = $escape;
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Select Max
	 *
	 * Generates a SELECT MAX(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_max($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'MAX');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Min
	 *
	 * Generates a SELECT MIN(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_min($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'MIN');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Average
	 *
	 * Generates a SELECT AVG(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_avg($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'AVG');
	}

	// --------------------------------------------------------------------

	/**
	 * Select Sum
	 *
	 * Generates a SELECT SUM(field) portion of a query
	 *
	 * @param	string	the field
	 * @param	string	an alias
	 * @return	CI_DB_query_builder
	 */
	public function select_sum($select = '', $alias = '')
	{
		return $this->_max_min_avg_sum($select, $alias, 'SUM');
	}

	// --------------------------------------------------------------------

	/**
	 * SELECT [MAX|MIN|AVG|SUM]()
	 *
	 * @used-by	select_max()
	 * @used-by	select_min()
	 * @used-by	select_avg()
	 * @used-by	select_sum()
	 *
	 * @param	string	$select	Field name
	 * @param	string	$alias
	 * @param	string	$type
	 * @return	CI_DB_query_builder
	 */
	protected function _max_min_avg_sum($select = '', $alias = '', $type = 'MAX')
	{
		if ( ! is_string($select) OR $select === '')
		{
			$this->display_error('db_invalid_query');
		}

		$type = strtoupper($type);

		if ( ! in_array($type, array('MAX', 'MIN', 'AVG', 'SUM')))
		{
			show_error('Invalid function type: '.$type);
		}

		if ($alias === '')
		{
			$alias = $this->_create_alias_from_table(trim($select));
		}

		$sql = $type.'('.$this->protect_identifiers(trim($select)).') AS '.$this->escape_identifiers(trim($alias));

		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Determines the alias name based on the table
	 *
	 * @param	string	$item
	 * @return	string
	 */
	protected function _create_alias_from_table($item)
	{
		if (strpos($item, '.') !== FALSE)
		{
			$item = explode('.', $item);
			return end($item);
		}

		return $item;
	}

	// --------------------------------------------------------------------

	/**
	 * DISTINCT
	 *
	 * Sets a flag which tells the query string compiler to add DISTINCT
	 *
	 * @param	bool	$val
	 * @return	CI_DB_query_builder
	 */
	public function distinct($val = TRUE)
	{
		$this->qb_distinct = is_bool($val) ? $val : TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * From
	 *
	 * Generates the FROM portion of the query
	 *
	 * @param	mixed	$from	can be a string or array
	 * @return	CI_DB_query_builder
	 */
	public function from($from)
	{
		if( ! is_array($from) ){
			$from = array( '' . $from );
		}

		// foreach ((array) $from as $val)
		foreach( $from as $val )
		{
			if (strpos($val, ',') !== FALSE)
			{
				foreach (explode(',', $val) as $v)
				{
					$v = trim($v);
					$this->_track_aliases($v);

					$this->qb_from[] = $v = $this->protect_identifiers($v, TRUE, NULL, FALSE);

					if ($this->qb_caching === TRUE)
					{
						$this->qb_cache_from[] = $v;
						$this->qb_cache_exists[] = 'from';
					}
				}
			}
			else
			{
				$val = trim($val);

				// Extract any aliases that might exist. We use this information
				// in the protect_identifiers to know whether to add a table prefix
				$this->_track_aliases($val);

				$this->qb_from[] = $val = $this->protect_identifiers($val, TRUE, NULL, FALSE);

				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_from[] = $val;
					$this->qb_cache_exists[] = 'from';
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * JOIN
	 *
	 * Generates the JOIN portion of the query
	 *
	 * @param	string
	 * @param	string	the join condition
	 * @param	string	the type of join
	 * @param	string	whether not to try to escape identifiers
	 * @return	CI_DB_query_builder
	 */
	public function join($table, $cond, $type = '', $escape = NULL)
	{
		if ($type !== '')
		{
			$type = strtoupper(trim($type));

			if ( ! in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
			{
				$type = '';
			}
			else
			{
				$type .= ' ';
			}
		}

		// Extract any aliases that might exist. We use this information
		// in the protect_identifiers to know whether to add a table prefix
		$this->_track_aliases($table);

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if ( ! $this->_has_operator($cond))
		{
			$cond = ' USING ('.($escape ? $this->escape_identifiers($cond) : $cond).')';
		}
		elseif ($escape === FALSE)
		{
			$cond = ' ON '.$cond;
		}
		else
		{
			// Split multiple conditions
			if (preg_match_all('/\sAND\s|\sOR\s/i', $cond, $joints, PREG_OFFSET_CAPTURE))
			{
				$conditions = array();
				$joints = $joints[0];
				array_unshift($joints, array('', 0));

				for ($i = count($joints) - 1, $pos = strlen($cond); $i >= 0; $i--)
				{
					$joints[$i][1] += strlen($joints[$i][0]); // offset
					$conditions[$i] = substr($cond, $joints[$i][1], $pos - $joints[$i][1]);
					$pos = $joints[$i][1] - strlen($joints[$i][0]);
					$joints[$i] = $joints[$i][0];
				}
			}
			else
			{
				$conditions = array($cond);
				$joints = array('');
			}

			$cond = ' ON ';
			for ($i = 0, $c = count($conditions); $i < $c; $i++)
			{
				$operator = $this->_get_operator($conditions[$i]);
				$cond .= $joints[$i];
				$cond .= preg_match("/(\(*)?([\[\]\w\.'-]+)".preg_quote($operator)."(.*)/i", $conditions[$i], $match)
					? $match[1].$this->protect_identifiers($match[2]).$operator.$this->protect_identifiers($match[3])
					: $conditions[$i];
			}
		}

		// Do we want to escape the table name?
		if ($escape === TRUE)
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		// Assemble the JOIN statement
		$this->qb_join[] = $join = $type.'JOIN '.$table.$cond;
		$this->qb_join_tables[] = $table;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_join[] = $join;
			$this->qb_cache_exists[] = 'join';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function where($key, $value = NULL, $escape = NULL)
	{
		if( is_array($value) ){
			if( FALSE !== strpos($key, '<>') ){
				if( $value ){
					$key = str_replace( '<>', '', $key );
					return $this->where_not_in( $key, $value, $escape );
				}
				else {
					// echo 'LOLO';
					return;
					return $this->where( 1, 1 );
				}
			}
			else {
				if( $value ){
					if( FALSE !== strpos($key, '=') ) $key = str_replace( '=', '', $key );
					return $this->where_in( $key, $value, $escape );
				}
				else {
					return $this->where( 1, 0 );
				}
			}
		}
		return $this->_wh('qb_where', $key, $value, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE
	 *
	 * Generates the WHERE portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed
	 * @param	mixed
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function orWhere($key, $value = NULL, $escape = NULL)
	{
		if( is_array($value) ){
			return $this->orWhereIn( $key, $value, $escape );
		}
		return $this->_wh('qb_where', $key, $value, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE, HAVING
	 *
	 * @used-by	where()
	 * @used-by	or_where()
	 * @used-by	having()
	 * @used-by	or_having()
	 *
	 * @param	string	$qb_key	'qb_where' or 'qb_having'
	 * @param	mixed	$key
	 * @param	mixed	$value
	 * @param	string	$type
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _wh($qb_key, $key, $value = NULL, $type = 'AND ', $escape = NULL)
	{
		$qb_cache_key = ($qb_key === 'qb_having') ? 'qb_cache_having' : 'qb_cache_where';

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		// If the escape value was not set will base it on the global setting
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->$qb_key) === 0 && count($this->$qb_cache_key) === 0)
				? $this->_group_get_type('')
				: $this->_group_get_type($type);

			if ($v !== NULL)
			{
				if ($escape === TRUE)
				{
					$v = ' '.$this->escape($v);
				}

				if ( ! $this->_has_operator($k))
				{
					$k .= ' = ';
				}
			}
			elseif ( ! $this->_has_operator($k))
			{
				// value appears not to have been set, assign the test to IS NULL
				$k .= ' IS NULL';
			}
			elseif (preg_match('/\s*(!?=|<>|IS(?:\s+NOT)?)\s*$/i', $k, $match, PREG_OFFSET_CAPTURE))
			{
				$k = substr($k, 0, $match[0][1]).($match[1][0] === '=' ? ' IS NULL' : ' IS NOT NULL');
			}

			$this->{$qb_key}[] = array('condition' => $prefix.$k.$v, 'escape' => $escape);
			if ($this->qb_caching === TRUE)
			{
				$this->{$qb_cache_key}[] = array('condition' => $prefix.$k.$v, 'escape' => $escape);
				$this->qb_cache_exists[] = substr($qb_key, 3);
			}

		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE IN
	 *
	 * Generates a WHERE field IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function where_in($key = NULL, $values = NULL, $escape = NULL)
	{
		if( is_array($values) && count($values) == 1 ){
			return $this->where( $key, current($values), $escape);
		}
		return $this->_where_in($key, $values, FALSE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE IN
	 *
	 * Generates a WHERE field IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function orWhereIn($key = NULL, $values = NULL, $escape = NULL)
	{
		if( is_array($values) && count($values) == 1 ){
			return $this->orWhere( $key, current($values), $escape);
		}
		return $this->_where_in($key, $values, FALSE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * WHERE NOT IN
	 *
	 * Generates a WHERE field NOT IN('item', 'item') SQL query,
	 * joined with 'AND' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function where_not_in($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_where_in($key, $values, TRUE, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR WHERE NOT IN
	 *
	 * Generates a WHERE field NOT IN('item', 'item') SQL query,
	 * joined with 'OR' if appropriate.
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function orWhereNotIn($key = NULL, $values = NULL, $escape = NULL)
	{
		return $this->_where_in($key, $values, TRUE, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal WHERE IN
	 *
	 * @used-by	where_in()
	 * @used-by	or_where_in()
	 * @used-by	where_not_in()
	 * @used-by	or_where_not_in()
	 *
	 * @param	string	$key	The field to search
	 * @param	array	$values	The values searched on
	 * @param	bool	$not	If the statement would be IN or NOT IN
	 * @param	string	$type
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _where_in($key = NULL, $values = NULL, $not = FALSE, $type = 'AND ', $escape = NULL)
	{
		if ($key === NULL OR $values === NULL)
		{
			return $this;
		}

		if ( ! is_array($values))
		{
			$values = array($values);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		$not = ($not) ? ' NOT' : '';

		if ($escape === TRUE)
		{
			$where_in = array();
			foreach ($values as $value)
			{
				$where_in[] = $this->escape($value);
			}
		}
		else
		{
			$where_in = array_values($values);
		}

		$prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0)
			? $this->_group_get_type('')
			: $this->_group_get_type($type);

		$where_in = array(
			'condition' => $prefix.$key.$not.' IN('.implode(', ', $where_in).')',
			'escape' => $escape
		);

		$this->qb_where[] = $where_in;
		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_where[] = $where_in;
			$this->qb_cache_exists[] = 'where';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'AND ', $side, '', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * NOT LIKE
	 *
	 * Generates a NOT LIKE portion of the query.
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function not_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'AND ', $side, 'NOT', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR LIKE
	 *
	 * Generates a %LIKE% portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function orLike($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'OR ', $side, '', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR NOT LIKE
	 *
	 * Generates a NOT LIKE portion of the query.
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$side
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_not_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		return $this->_like($field, $match, 'OR ', $side, 'NOT', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * Internal LIKE
	 *
	 * @used-by	like()
	 * @used-by	or_like()
	 * @used-by	not_like()
	 * @used-by	or_not_like()
	 *
	 * @param	mixed	$field
	 * @param	string	$match
	 * @param	string	$type
	 * @param	string	$side
	 * @param	string	$not
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	protected function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '', $escape = NULL)
	{
		if ( ! is_array($field))
		{
			$field = array($field => $match);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;
		// lowercase $side in case somebody writes e.g. 'BEFORE' instead of 'before' (doh)
		$side = strtolower($side);

		foreach ($field as $k => $v)
		{
			$prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0)
				? $this->_group_get_type('') : $this->_group_get_type($type);

			if ($escape === TRUE)
			{
				$v = $this->escape_like_str($v);
			}

			if ($side === 'none')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '{$v}'";
			}
			elseif ($side === 'before')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}'";
			}
			elseif ($side === 'after')
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '{$v}%'";
			}
			else
			{
				$like_statement = "{$prefix} {$k} {$not} LIKE '%{$v}%'";
			}

			// some platforms require an escape sequence definition for LIKE wildcards
			if ($escape === TRUE && $this->_like_escape_str !== '')
			{
				$like_statement .= sprintf($this->_like_escape_str, $this->_like_escape_chr);
			}

			$this->qb_where[] = array('condition' => $like_statement, 'escape' => $escape);
			if ($this->qb_caching === TRUE)
			{
				$this->qb_cache_where[] = array('condition' => $like_statement, 'escape' => $escape);
				$this->qb_cache_exists[] = 'where';
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group.
	 *
	 * @param	string	$not	(Internal use only)
	 * @param	string	$type	(Internal use only)
	 * @return	CI_DB_query_builder
	 */
	public function groupStart( $not = '', $type = 'AND ' )
	{
		$type = $this->_group_get_type($type);

		$this->qb_where_group_started = TRUE;
		$prefix = (count($this->qb_where) === 0 && count($this->qb_cache_where) === 0) ? '' : $type;
		$where = array(
			'condition' => $prefix.$not.str_repeat(' ', ++$this->qb_where_group_count).' (',
			'escape' => FALSE
		);

		$this->qb_where[] = $where;
		if ($this->qb_caching)
		{
			$this->qb_cache_where[] = $where;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but ORs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function orGroupStart()
	{
		return $this->groupStart('', 'OR ');
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but NOTs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function notGroupStart()
	{
		return $this->groupStart('NOT ', 'AND ');
	}

	// --------------------------------------------------------------------

	/**
	 * Starts a query group, but OR NOTs the group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function orNotGroupStart()
	{
		return $this->groupStart('NOT ', 'OR ');
	}

	// --------------------------------------------------------------------

	/**
	 * Ends a query group
	 *
	 * @return	CI_DB_query_builder
	 */
	public function groupEnd()
	{
		$this->qb_where_group_started = FALSE;
		$where = array(
			'condition' => str_repeat(' ', $this->qb_where_group_count--).')',
			'escape' => FALSE
		);

		$this->qb_where[] = $where;
		if ($this->qb_caching)
		{
			$this->qb_cache_where[] = $where;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Group_get_type
	 *
	 * @used-by	group_start()
	 * @used-by	_like()
	 * @used-by	_wh()
	 * @used-by	_where_in()
	 *
	 * @param	string	$type
	 * @return	string
	 */
	protected function _group_get_type($type)
	{
		if ($this->qb_where_group_started)
		{
			$type = '';
			$this->qb_where_group_started = FALSE;
		}

		return $type;
	}

	// --------------------------------------------------------------------

	/**
	 * GROUP BY
	 *
	 * @param	string	$by
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function group_by($by, $escape = NULL)
	{
		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if (is_string($by))
		{
			$by = ($escape === TRUE)
				? explode(',', $by)
				: array($by);
		}

		foreach ($by as $val)
		{
			$val = trim($val);

			if ($val !== '')
			{
				$val = array('field' => $val, 'escape' => $escape);

				$this->qb_groupby[] = $val;
				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_groupby[] = $val;
					$this->qb_cache_exists[] = 'groupby';
				}
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * HAVING
	 *
	 * Separates multiple calls with 'AND'.
	 *
	 * @param	string	$key
	 * @param	string	$value
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function having($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_having', $key, $value, 'AND ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * OR HAVING
	 *
	 * Separates multiple calls with 'OR'.
	 *
	 * @param	string	$key
	 * @param	string	$value
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function or_having($key, $value = NULL, $escape = NULL)
	{
		return $this->_wh('qb_having', $key, $value, 'OR ', $escape);
	}

	// --------------------------------------------------------------------

	/**
	 * ORDER BY
	 *
	 * @param	string	$orderby
	 * @param	string	$direction	ASC, DESC or RANDOM
	 * @param	bool	$escape
	 * @return	CI_DB_query_builder
	 */
	public function orderBy( $orderby, $direction = '', $escape = NULL )
	{
		$direction = strtoupper(trim($direction));

		if ($direction === 'RANDOM')
		{
			$direction = '';

			// Do we have a seed value?
			$orderby = ctype_digit((string) $orderby)
				? sprintf($this->_random_keyword[1], $orderby)
				: $this->_random_keyword[0];
		}
		elseif (empty($orderby))
		{
			return $this;
		}
		elseif ($direction !== '')
		{
			$direction = in_array($direction, array('ASC', 'DESC'), TRUE) ? ' '.$direction : '';
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		if ($escape === FALSE)
		{
			$qb_orderby[] = array('field' => $orderby, 'direction' => $direction, 'escape' => FALSE);
		}
		else
		{
			$qb_orderby = array();
			foreach (explode(',', $orderby) as $field)
			{
				$qb_orderby[] = ($direction === '' && preg_match('/\s+(ASC|DESC)$/i', rtrim($field), $match, PREG_OFFSET_CAPTURE))
					? array('field' => ltrim(substr($field, 0, $match[0][1])), 'direction' => ' '.$match[1][0], 'escape' => TRUE)
					: array('field' => trim($field), 'direction' => $direction, 'escape' => TRUE);
			}
		}

		$this->qb_orderby = array_merge($this->qb_orderby, $qb_orderby);
		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_orderby = array_merge($this->qb_cache_orderby, $qb_orderby);
			$this->qb_cache_exists[] = 'orderby';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT
	 *
	 * @param	int	$value	LIMIT value
	 * @param	int	$offset	OFFSET value
	 * @return	CI_DB_query_builder
	 */
	public function limit($value, $offset = 0)
	{
		is_null($value) OR $this->qb_limit = (int) $value;
		empty($offset) OR $this->qb_offset = (int) $offset;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the OFFSET value
	 *
	 * @param	int	$offset	OFFSET value
	 * @return	CI_DB_query_builder
	 */
	public function offset($offset)
	{
		empty($offset) OR $this->qb_offset = (int) $offset;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT string
	 *
	 * Generates a platform-specific LIMIT clause.
	 *
	 * @param	string	$sql	SQL Query
	 * @return	string
	 */
	protected function _limit($sql)
	{
		return $sql.' LIMIT '.($this->qb_offset ? $this->qb_offset.', ' : '').$this->qb_limit;
	}

	// --------------------------------------------------------------------

	/**
	 * The "set" function.
	 *
	 * Allows key/value pairs to be set for inserting or updating
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function set($key, $value = '', $escape = NULL)
	{
		$key = $this->_object_to_array($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		foreach ($key as $k => $v)
		{
			$this->qb_set[$this->protect_identifiers($k, FALSE, $escape)] = ($escape)
				? $this->escape($v) : $v;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get SELECT query string
	 *
	 * Compiles a SELECT query string and returns the sql.
	 *
	 * @param	string	the table name to select from (optional)
	 * @param	bool	TRUE: resets QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function getCompiledSelect( $table = '', $reset = TRUE )
	{
		if ($table !== '')
		{
			$this->_track_aliases($table);
			$this->from($table);
		}

		$select = $this->_compile_select();

		if ($reset === TRUE)
		{
			$this->_reset_select();
		}

		return $select;
	}

	public function getCompiledCount( $table = '', $reset = TRUE )
	{
		if ($table !== ''){
			$this->_track_aliases( $table );
			$this->from( $table );
		}

		$return = $this->_count_string . $this->escape_identifiers('count') . ' FROM ' . $this->protect_identifiers($table, TRUE, NULL, FALSE);

		// Write the "JOIN" portion of the query
		if (count($this->qb_join) > 0){
			$return .= "\n".implode("\n", $this->qb_join);
		}

		$where = $this->get_compiled_where( $reset );
		if( strlen($where) ){
			$return .= ' WHERE ' . $where;
		}

		if ($reset === TRUE){
			$this->_reset_select();
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data.
	 *
	 * @param	string	$table	Table name
	 * @param	array	$keys	INSERT keys
	 * @param	array	$values	INSERT values
	 * @return	string
	 */
	protected function _insert_batch($table, $keys, $values)
	{
		return 'INSERT INTO '.$table.' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
	}

	// --------------------------------------------------------------------

	/**
	 * The "set_insert_batch" function.  Allows key/value pairs to be set for batch inserts
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	public function set_insert_batch($key, $value = '', $escape = NULL)
	{
		$key = $this->_object_to_array_batch($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		is_bool($escape) OR $escape = $this->_protect_identifiers;

		$keys = array_keys($this->_object_to_array(current($key)));
		sort($keys);

		foreach ($key as $row)
		{
			$row = $this->_object_to_array($row);
			if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0)
			{
				// batch function above returns an error on an empty array
				$this->qb_set[] = array();
				return;
			}

			ksort($row); // puts $row in the same order as our keys

			if ($escape !== FALSE)
			{
				$clean = array();
				foreach ($row as $value)
				{
					$clean[] = $this->escape($value);
				}

				$row = $clean;
			}

			$this->qb_set[] = '('.implode(',', $row).')';
		}

		foreach ($keys as $k)
		{
			$this->qb_keys[] = $this->protect_identifiers($k, FALSE, $escape);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get INSERT query string
	 *
	 * Compiles an insert query and returns the sql
	 *
	 * @param	string	the table to insert into
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function getCompiledInsert($table = '', $reset = TRUE)
	{
		if ($this->_validate_insert($table) === FALSE)
		{
			return FALSE;
		}

		$sql = $this->_insert(
			$this->protect_identifiers(
				$this->qb_from[0], TRUE, NULL, FALSE
			),
			array_keys($this->qb_set),
			array_values($this->qb_set)
		);

		if ($reset === TRUE)
		{
			$this->_reset_write();
		}

		return $sql;
	}

	public function getCompiledInsertBatch( $table = '', $set = NULL )
	{
		$return = array();
		$batchSize = 100;

		if( TRUE OR ! is_null($set) ){
			$this->set_insert_batch($set);
		}

		// Batch this baby
		for( $i = 0, $total = count($this->qb_set); $i < $total; $i = $i + $batchSize ) {
			$sql = $this->_insert_batch($this->protect_identifiers($table, TRUE, NULL, FALSE), $this->qb_keys, array_slice($this->qb_set, $i, $batchSize));
			$return[] = $sql;
		}

		$this->_reset_write();

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Insert
	 *
	 * This method is used by both insert() and get_compiled_insert() to
	 * validate that the there data is actually being set and that table
	 * has been chosen to be inserted into.
	 *
	 * @param	string	the table to insert data into
	 * @return	string
	 */
	protected function _validate_insert($table = '')
	{
		if (count($this->qb_set) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
		}

		if ($table !== '')
		{
			$this->qb_from[0] = $table;
		}
		elseif ( ! isset($this->qb_from[0]))
		{
			return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Replace statement
	 *
	 * Generates a platform-specific replace string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _replace($table, $keys, $values)
	{
		return 'REPLACE INTO '.$table.' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * FROM tables
	 *
	 * Groups tables in FROM clauses if needed, so there is no confusion
	 * about operator precedence.
	 *
	 * Note: This is only used (and overridden) by MySQL and CUBRID.
	 *
	 * @return	string
	 */
	protected function _from_tables()
	{
		return implode(', ', $this->qb_from);
	}

	// --------------------------------------------------------------------

	/**
	 * Get UPDATE query string
	 *
	 * Compiles an update query and returns the sql
	 *
	 * @param	string	the table to update
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function getCompiledUpdate( $table = '', $reset = TRUE )
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($this->_validate_update($table) === FALSE)
		{
			return FALSE;
		}

		$sql = $this->_update($this->qb_from[0], $this->qb_set);

		if ($reset === TRUE)
		{
			$this->_reset_write();
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Update
	 *
	 * This method is used by both update() and get_compiled_update() to
	 * validate that data is actually being set and that a table has been
	 * chosen to be update.
	 *
	 * @param	string	the table to update data on
	 * @return	bool
	 */
	protected function _validate_update($table)
	{
		if (count($this->qb_set) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_must_use_set') : FALSE;
		}

		if ($table !== '')
		{
			$this->qb_from = array($this->protect_identifiers($table, TRUE, NULL, FALSE));
		}
		elseif ( ! isset($this->qb_from[0]))
		{
			return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the truncate() command,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _truncate($table)
	{
		return 'TRUNCATE '.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * Get DELETE query string
	 *
	 * Compiles a delete query string and returns the sql
	 *
	 * @param	string	the table to delete from
	 * @param	bool	TRUE: reset QB values; FALSE: leave QB values alone
	 * @return	string
	 */
	public function getCompiledDelete( $table = '', $reset = TRUE )
	{
		$this->return_delete_sql = TRUE;
		$sql = $this->delete($table, '', NULL, $reset);
		$this->return_delete_sql = FALSE;
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete
	 *
	 * Compiles a delete string and runs the query
	 *
	 * @param	mixed	the table(s) to delete from. String or array
	 * @param	mixed	the where clause
	 * @param	mixed	the limit clause
	 * @param	bool
	 * @return	mixed
	 */
	public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		if ($table === '')
		{
			if ( ! isset($this->qb_from[0]))
			{
				return ($this->db_debug) ? $this->display_error('db_must_set_table') : FALSE;
			}

			$table = $this->qb_from[0];
		}
		elseif (is_array($table))
		{
			empty($where) && $reset_data = FALSE;

			foreach ($table as $single_table)
			{
				$this->delete($single_table, $where, $limit, $reset_data);
			}

			return;
		}
		else
		{
			$table = $this->protect_identifiers($table, TRUE, NULL, FALSE);
		}

		if ($where !== '')
		{
			$this->where($where);
		}

		if ( ! empty($limit))
		{
			$this->limit($limit);
		}

		if (count($this->qb_where) === 0)
		{
			return ($this->db_debug) ? $this->display_error('db_del_must_use_where') : FALSE;
		}

		$sql = $this->_delete($table);
		if ($reset_data)
		{
			$this->_reset_write();
		}

		return ($this->return_delete_sql === TRUE) ? $sql : $this->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	the table name
	 * @return	string
	 */
	protected function _delete($table)
	{
		// $sql = 'DELETE FROM '.$table.$this->_compile_wh('qb_where')
			// .($this->qb_limit ? ' LIMIT '.$this->qb_limit : '');

		if (count($this->qb_join) > 0){
			$sql = 'DELETE ' . $table;
			foreach( $this->qb_join_tables as $jt ){
				$sql .= ', ' . $jt;
			}
			$sql .= ' FROM '.$table;
		}
		else {
			$sql = 'DELETE FROM '.$table;
		}

	// Write the "JOIN" portion of the query
		if (count($this->qb_join) > 0){
			$sql .= "\n".implode("\n", $this->qb_join);
		}

		$sql .= $this->_compile_wh('qb_where')
			.($this->qb_limit ? ' LIMIT '.$this->qb_limit : '');

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Track Aliases
	 *
	 * Used to track SQL statements written with aliased tables.
	 *
	 * @param	string	The table to inspect
	 * @return	string
	 */
	protected function _track_aliases($table)
	{
		if (is_array($table))
		{
			foreach ($table as $t)
			{
				$this->_track_aliases($t);
			}
			return;
		}

		// Does the string contain a comma?  If so, we need to separate
		// the string into discreet statements
		if (strpos($table, ',') !== FALSE)
		{
			return $this->_track_aliases(explode(',', $table));
		}

		// if a table alias is used we can recognize it by a space
		if (strpos($table, ' ') !== FALSE)
		{
			// if the alias is written with the AS keyword, remove it
			$table = preg_replace('/\s+AS\s+/i', ' ', $table);

			// Grab the alias
			$table = trim(strrchr($table, ' '));

			// Store the alias, if it doesn't already exist
			if ( ! in_array($table, $this->qb_aliased_tables))
			{
				$this->qb_aliased_tables[] = $table;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the SELECT statement
	 *
	 * Generates a query string based on which functions were used.
	 * Should not be called directly.
	 *
	 * @param	bool	$select_override
	 * @return	string
	 */
	protected function _compile_select($select_override = FALSE)
	{
		// Combine any cached components with the current statements
		$this->_merge_cache();

		// Write the "select" portion of the query
		if ($select_override !== FALSE)
		{
			$sql = $select_override;
		}
		else
		{
			$sql = ( ! $this->qb_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

			if (count($this->qb_select) === 0)
			{
				$sql .= '*';
			}
			else
			{
				// Cycle through the "select" portion of the query and prep each column name.
				// The reason we protect identifiers here rather than in the select() function
				// is because until the user calls the from() function we don't know if there are aliases
				foreach ($this->qb_select as $key => $val)
				{
					$no_escape = isset($this->qb_no_escape[$key]) ? $this->qb_no_escape[$key] : NULL;
					$this->qb_select[$key] = $this->protect_identifiers($val, FALSE, $no_escape);
				}

				$sql .= implode(', ', $this->qb_select);
			}
		}

		// Write the "FROM" portion of the query
		if (count($this->qb_from) > 0)
		{
			$sql .= "\nFROM ".$this->_from_tables();
		}

		// Write the "JOIN" portion of the query
		if (count($this->qb_join) > 0)
		{
			$sql .= "\n".implode("\n", $this->qb_join);
		}

		$sql .= $this->_compile_wh('qb_where')
			.$this->_compile_group_by()
			.$this->_compile_wh('qb_having')
			.$this->_compile_order_by(); // ORDER BY

		// LIMIT
		if ($this->qb_limit)
		{
			return $this->_limit($sql."\n");
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile WHERE, HAVING statements
	 *
	 * Escapes identifiers in WHERE and HAVING statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of whether
	 * where(), or_where(), having(), or_having are called prior to from(),
	 * join() and dbprefix is added only if needed.
	 *
	 * @param	string	$qb_key	'qb_where' or 'qb_having'
	 * @return	string	SQL statement
	 */
	public function get_compiled_where( $reset = TRUE )
	{
		$return = $this->_compile_wh('qb_where');
		$return = trim($return);
		if( substr($return, 0, strlen('WHERE ')) == 'WHERE ' ){
			$return = substr($return, strlen('WHERE '));
		}

		if ($reset === TRUE){
			$this->_reset_select();
		}

		return $return;
	}

	protected function _compile_wh($qb_key)
	{
		if (count($this->$qb_key) > 0)
		{
			for ($i = 0, $c = count($this->$qb_key); $i < $c; $i++)
			{
				// Is this condition already compiled?
				if (is_string($this->{$qb_key}[$i]))
				{
					continue;
				}
				elseif ($this->{$qb_key}[$i]['escape'] === FALSE)
				{
					$this->{$qb_key}[$i] = $this->{$qb_key}[$i]['condition'];
					continue;
				}

				// Split multiple conditions
				$conditions = preg_split(
					'/((?:^|\s+)AND\s+|(?:^|\s+)OR\s+)/i',
					$this->{$qb_key}[$i]['condition'],
					-1,
					PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
				);

				for ($ci = 0, $cc = count($conditions); $ci < $cc; $ci++)
				{
					if (($op = $this->_get_operator($conditions[$ci])) === FALSE
						OR ! @preg_match('/^(\(?)(.*)('.preg_quote($op, '/').')\s*(.*(?<!\)))?(\)?)$/i', $conditions[$ci], $matches))
					{
						continue;
					}

					// $matches = array(
					//	0 => '(test <= foo)',	/* the whole thing */
					//	1 => '(',		/* optional */
					//	2 => 'test',		/* the field name */
					//	3 => ' <= ',		/* $op */
					//	4 => 'foo',		/* optional, if $op is e.g. 'IS NULL' */
					//	5 => ')'		/* optional */
					// );

					if ( ! empty($matches[4]))
					{
						$this->_is_literal($matches[4]) OR $matches[4] = $this->protect_identifiers(trim($matches[4]));
						$matches[4] = ' '.$matches[4];
					}

					$conditions[$ci] = $matches[1].$this->protect_identifiers(trim($matches[2]))
						.' '.trim($matches[3]).$matches[4].$matches[5];
				}

				$this->{$qb_key}[$i] = implode('', $conditions);
			}

			return ($qb_key === 'qb_having' ? "\nHAVING " : "\nWHERE ")
				.implode("\n", $this->$qb_key);
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Compile GROUP BY
	 *
	 * Escapes identifiers in GROUP BY statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of wether
	 * group_by() is called prior to from(), join() and dbprefix is added
	 * only if needed.
	 *
	 * @return	string	SQL statement
	 */
	protected function _compile_group_by()
	{
		if (count($this->qb_groupby) > 0)
		{
			for ($i = 0, $c = count($this->qb_groupby); $i < $c; $i++)
			{
				// Is it already compiled?
				if (is_string($this->qb_groupby[$i]))
				{
					continue;
				}

				$this->qb_groupby[$i] = ($this->qb_groupby[$i]['escape'] === FALSE OR $this->_is_literal($this->qb_groupby[$i]['field']))
					? $this->qb_groupby[$i]['field']
					: $this->protect_identifiers($this->qb_groupby[$i]['field']);
			}

			return "\nGROUP BY ".implode(', ', $this->qb_groupby);
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Compile ORDER BY
	 *
	 * Escapes identifiers in ORDER BY statements at execution time.
	 *
	 * Required so that aliases are tracked properly, regardless of wether
	 * order_by() is called prior to from(), join() and dbprefix is added
	 * only if needed.
	 *
	 * @return	string	SQL statement
	 */
	protected function _compile_order_by()
	{
		if (is_array($this->qb_orderby) && count($this->qb_orderby) > 0)
		{
			for ($i = 0, $c = count($this->qb_orderby); $i < $c; $i++)
			{
				if ($this->qb_orderby[$i]['escape'] !== FALSE && ! $this->_is_literal($this->qb_orderby[$i]['field']))
				{
					$this->qb_orderby[$i]['field'] = $this->protect_identifiers($this->qb_orderby[$i]['field']);
				}

				$this->qb_orderby[$i] = $this->qb_orderby[$i]['field'].$this->qb_orderby[$i]['direction'];
			}

			return $this->qb_orderby = "\nORDER BY ".implode(', ', $this->qb_orderby);
		}
		elseif (is_string($this->qb_orderby))
		{
			return $this->qb_orderby;
		}

		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class HC4_variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	protected function _object_to_array($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ( ! is_object($val) && ! is_array($val) && $key !== '_parent_name')
			{
				$array[$key] = $val;
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class HC4_variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	protected function _object_to_array_batch($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		$out = get_object_vars($object);
		$fields = array_keys($out);

		foreach ($fields as $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ($val !== '_parent_name')
			{
				$i = 0;
				foreach ($out[$val] as $data)
				{
					$array[$i++][$val] = $data;
				}
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Start Cache
	 *
	 * Starts QB caching
	 *
	 * @return	CI_DB_query_builder
	 */
	public function start_cache()
	{
		$this->qb_caching = TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Stop Cache
	 *
	 * Stops QB caching
	 *
	 * @return	CI_DB_query_builder
	 */
	public function stop_cache()
	{
		$this->qb_caching = FALSE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Flush Cache
	 *
	 * Empties the QB cache
	 *
	 * @return	CI_DB_query_builder
	 */
	public function flush_cache()
	{
		$this->_reset_run(array(
			'qb_cache_select'		=> array(),
			'qb_cache_from'			=> array(),
			'qb_cache_join'			=> array(),
			'qb_cache_where'		=> array(),
			'qb_cache_groupby'		=> array(),
			'qb_cache_having'		=> array(),
			'qb_cache_orderby'		=> array(),
			'qb_cache_set'			=> array(),
			'qb_cache_exists'		=> array(),
			'qb_cache_no_escape'	=> array()
		));

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Merge Cache
	 *
	 * When called, this function merges any cached QB arrays with
	 * locally called ones.
	 *
	 * @return	void
	 */
	protected function _merge_cache()
	{
		if (count($this->qb_cache_exists) === 0)
		{
			return;
		}
		elseif (in_array('select', $this->qb_cache_exists, TRUE))
		{
			$qb_no_escape = $this->qb_cache_no_escape;
		}

		foreach (array_unique($this->qb_cache_exists) as $val) // select, from, etc.
		{
			$qb_variable	= 'qb_'.$val;
			$qb_cache_var	= 'qb_cache_'.$val;
			$qb_new 	= $this->$qb_cache_var;

			for ($i = 0, $c = count($this->$qb_variable); $i < $c; $i++)
			{
				if ( ! in_array($this->{$qb_variable}[$i], $qb_new, TRUE))
				{
					$qb_new[] = $this->{$qb_variable}[$i];
					if ($val === 'select')
					{
						$qb_no_escape[] = $this->qb_no_escape[$i];
					}
				}
			}

			$this->$qb_variable = $qb_new;
			if ($val === 'select')
			{
				$this->qb_no_escape = $qb_no_escape;
			}
		}

		// If we are "protecting identifiers" we need to examine the "from"
		// portion of the query to determine if there are any aliases
		if ($this->_protect_identifiers === TRUE && count($this->qb_cache_from) > 0)
		{
			$this->_track_aliases($this->qb_from);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Is literal
	 *
	 * Determines if a string represents a literal value or a field name
	 *
	 * @param	string	$str
	 * @return	bool
	 */
	protected function _is_literal($str)
	{
		$str = trim($str);

		if (empty($str) OR ctype_digit($str) OR (string) (float) $str === $str OR in_array(strtoupper($str), array('TRUE', 'FALSE'), TRUE))
		{
			return TRUE;
		}

		static $_str;

		if (empty($_str))
		{
			$_str = ($this->_escape_char !== '"')
				? array('"', "'") : array("'");
		}

		return in_array($str[0], $_str, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Reset Query Builder values.
	 *
	 * Publicly-visible method to reset the QB values.
	 *
	 * @return	CI_DB_query_builder
	 */
	public function reset_query()
	{
		$this->_reset_select();
		$this->_reset_write();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder values.  Called by the get() function
	 *
	 * @param	array	An array of fields to reset
	 * @return	void
	 */
	protected function _reset_run($qb_reset_items)
	{
		foreach ($qb_reset_items as $item => $default_value)
		{
			$this->$item = $default_value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder values.  Called by the get() function
	 *
	 * @return	void
	 */
	protected function _reset_select()
	{
		$this->_reset_run(array(
			'qb_select'		=> array(),
			'qb_from'		=> array(),
			'qb_join'		=> array(),
			'qb_join_tables'	=> array(),
			'qb_where'		=> array(),
			'qb_groupby'		=> array(),
			'qb_having'		=> array(),
			'qb_orderby'		=> array(),
			'qb_aliased_tables'	=> array(),
			'qb_no_escape'		=> array(),
			'qb_distinct'		=> FALSE,
			'qb_limit'		=> FALSE,
			'qb_offset'		=> FALSE
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Resets the query builder "write" values.
	 *
	 * Called by the insert() update() insert_batch() update_batch() and delete() functions
	 *
	 * @return	void
	 */
	protected function _reset_write()
	{
		$this->_reset_run(array(
			'qb_set'	=> array(),
			'qb_from'	=> array(),
			'qb_join'	=> array(),
			'qb_where'	=> array(),
			'qb_orderby'	=> array(),
			'qb_keys'	=> array(),
			'qb_limit'	=> FALSE
		));
	}

	
	// --------------------------------------------------------------------

	/**
	 * Version number query string
	 *
	 * @return	string
	 */
	protected function _version()
	{
		return 'SELECT VERSION() AS ver';
	}

	// --------------------------------------------------------------------

	/**
	 * Disable Transactions
	 * This permits transactions to be disabled at run-time.
	 *
	 * @return	void
	 */
	public function trans_off()
	{
		$this->trans_enabled = FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Enable/disable Transaction Strict Mode
	 *
	 * When strict mode is enabled, if you are running multiple groups of
	 * transactions, if one group fails all subsequent groups will be
	 * rolled back.
	 *
	 * If strict mode is disabled, each group is treated autonomously,
	 * meaning a failure of one group will not affect any others
	 *
	 * @param	bool	$mode = TRUE
	 * @return	void
	 */
	public function trans_strict($mode = TRUE)
	{
		$this->trans_strict = is_bool($mode) ? $mode : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Start Transaction
	 *
	 * @param	bool	$test_mode = FALSE
	 * @return	bool
	 */
	public function trans_start($test_mode = FALSE)
	{
		if ( ! $this->trans_enabled)
		{
			return FALSE;
		}

		return $this->trans_begin($test_mode);
	}

	// --------------------------------------------------------------------

	/**
	 * Complete Transaction
	 *
	 * @return	bool
	 */
	public function trans_complete()
	{
		if ( ! $this->trans_enabled)
		{
			return FALSE;
		}

		// The query() function will set this flag to FALSE in the event that a query failed
		if ($this->_trans_status === FALSE OR $this->_trans_failure === TRUE)
		{
			$this->trans_rollback();

			// If we are NOT running in strict mode, we will reset
			// the _trans_status flag so that subsequent groups of
			// transactions will be permitted.
			if ($this->trans_strict === FALSE)
			{
				$this->_trans_status = TRUE;
			}

			log_message('debug', 'DB Transaction Failure');
			return FALSE;
		}

		return $this->trans_commit();
	}

	// --------------------------------------------------------------------

	/**
	 * Lets you retrieve the transaction flag to determine if it has failed
	 *
	 * @return	bool
	 */
	public function trans_status()
	{
		return $this->_trans_status;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction
	 *
	 * @param	bool	$test_mode
	 * @return	bool
	 */
	public function trans_begin($test_mode = FALSE)
	{
		if ( ! $this->trans_enabled)
		{
			return FALSE;
		}
		// When transactions are nested we only begin/commit/rollback the outermost ones
		elseif ($this->_trans_depth > 0)
		{
			$this->_trans_depth++;
			return TRUE;
		}

		// Reset the transaction failure flag.
		// If the $test_mode flag is set to TRUE transactions will be rolled back
		// even if the queries produce a successful result.
		$this->_trans_failure = ($test_mode === TRUE);

		if ($this->_trans_begin())
		{
			$this->_trans_depth++;
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction
	 *
	 * @return	bool
	 */
	public function trans_commit()
	{
		if ( ! $this->trans_enabled OR $this->_trans_depth === 0)
		{
			return FALSE;
		}
		// When transactions are nested we only begin/commit/rollback the outermost ones
		elseif ($this->_trans_depth > 1 OR $this->_trans_commit())
		{
			$this->_trans_depth--;
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */
	public function trans_rollback()
	{
		if ( ! $this->trans_enabled OR $this->_trans_depth === 0)
		{
			return FALSE;
		}
		// When transactions are nested we only begin/commit/rollback the outermost ones
		elseif ($this->_trans_depth > 1 OR $this->_trans_rollback())
		{
			$this->_trans_depth--;
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Determines if a query is a "write" type.
	 *
	 * @param	string	An SQL query string
	 * @return	bool
	 */
	public function is_write_type($sql)
	{
		return (bool) preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $sql);
	}

	// --------------------------------------------------------------------

	/**
	 * "Smart" Escape String
	 *
	 * Escapes data based on type
	 * Sets boolean and null types
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function escape($str)
	{
		if (is_array($str))
		{
			$str = array_map(array(&$this, 'escape'), $str);
			return $str;
		}
		elseif (is_string($str) OR (is_object($str) && method_exists($str, '__toString')))
		{
			return "'".$this->escape_str($str)."'";
		}
		elseif (is_bool($str))
		{
			return ($str === FALSE) ? 0 : 1;
		}
		elseif ($str === NULL)
		{
			return 'NULL';
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Escape String
	 *
	 * @param	string|string[]	$str	Input string
	 * @param	bool	$like	Whether or not the string will be used in a LIKE condition
	 * @return	string
	 */
	public function escape_str($str, $like = FALSE)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val, $like);
			}

			return $str;
		}

		$str = $this->_escape_str($str);

		// escape LIKE condition wildcards
		if ($like === TRUE)
		{
			return str_replace(
				array($this->_like_escape_chr, '%', '_'),
				array($this->_like_escape_chr.$this->_like_escape_chr, $this->_like_escape_chr.'%', $this->_like_escape_chr.'_'),
				$str
			);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Escape LIKE String
	 *
	 * Calls the individual driver for platform
	 * specific escaping for LIKE conditions
	 *
	 * @param	string|string[]
	 * @return	mixed
	 */
	public function escape_like_str($str)
	{
		return $this->escape_str($str, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependant string escape
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _escape_str($str)
	{
		return str_replace("'", "''", $this->removeInvisibleCharacters($str));
	}

	// --------------------------------------------------------------------

	/**
	 * Escape the SQL Identifiers
	 *
	 * This function escapes column and table names
	 *
	 * @param	mixed
	 * @return	mixed
	 */
	public function escape_identifiers($item)
	{
		if ($this->_escape_char === '' OR empty($item) OR in_array($item, $this->_reserved_identifiers))
		{
			return $item;
		}
		elseif (is_array($item))
		{
			foreach ($item as $key => $value)
			{
				$item[$key] = $this->escape_identifiers($value);
			}

			return $item;
		}
		// Avoid breaking functions and literal values inside queries
		elseif (ctype_digit($item) OR $item[0] === "'" OR ($this->_escape_char !== '"' && $item[0] === '"') OR strpos($item, '(') !== FALSE)
		{
			return $item;
		}

		static $preg_ec = array();

		if (empty($preg_ec))
		{
			if (is_array($this->_escape_char))
			{
				$preg_ec = array(
					preg_quote($this->_escape_char[0], '/'),
					preg_quote($this->_escape_char[1], '/'),
					$this->_escape_char[0],
					$this->_escape_char[1]
				);
			}
			else
			{
				$preg_ec[0] = $preg_ec[1] = preg_quote($this->_escape_char, '/');
				$preg_ec[2] = $preg_ec[3] = $this->_escape_char;
			}
		}

		foreach ($this->_reserved_identifiers as $id)
		{
			if (strpos($item, '.'.$id) !== FALSE)
			{
				return preg_replace('/'.$preg_ec[0].'?([^'.$preg_ec[1].'\.]+)'.$preg_ec[1].'?\./i', $preg_ec[2].'$1'.$preg_ec[3].'.', $item);
			}
		}

		return preg_replace('/'.$preg_ec[0].'?([^'.$preg_ec[1].'\.]+)'.$preg_ec[1].'?(\.)?/i', $preg_ec[2].'$1'.$preg_ec[3].'$2', $item);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate an insert string
	 *
	 * @param	string	the table upon which the query will be performed
	 * @param	array	an associative array data of key/values
	 * @return	string
	 */
	public function insert_string($table, $data)
	{
		$fields = $values = array();

		foreach ($data as $key => $val)
		{
			$fields[] = $this->escape_identifiers($key);
			$values[] = $this->escape($val);
		}

		return $this->_insert($this->protect_identifiers($table, TRUE, NULL, FALSE), $fields, $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert statement
	 *
	 * Generates a platform-specific insert string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the insert keys
	 * @param	array	the insert values
	 * @return	string
	 */
	protected function _insert($table, $keys, $values)
	{
		return 'INSERT INTO '.$table.' ('.implode(', ', $keys).') VALUES ('.implode(', ', $values).')';
	}

	// --------------------------------------------------------------------

	/**
	 * Generate an update string
	 *
	 * @param	string	the table upon which the query will be performed
	 * @param	array	an associative array data of key/values
	 * @param	mixed	the "where" statement
	 * @return	string
	 */
	public function update_string($table, $data, $where)
	{
		if (empty($where))
		{
			return FALSE;
		}

		$this->where($where);

		$fields = array();
		foreach ($data as $key => $val)
		{
			$fields[$this->protect_identifiers($key)] = $this->escape($val);
		}

		$sql = $this->_update($this->protect_identifiers($table, TRUE, NULL, FALSE), $fields);
		$this->_reset_write();
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	the table name
	 * @param	array	the update data
	 * @return	string
	 */
	protected function _update($table, $values)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key.' = '.$val;
		}

		return 'UPDATE '.$table.' SET '.implode(', ', $valstr)
			.$this->_compile_wh('qb_where')
			.$this->_compile_order_by()
			.($this->qb_limit ? ' LIMIT '.$this->qb_limit : '');
	}

	// --------------------------------------------------------------------

	/**
	 * Tests whether the string has an SQL operator
	 *
	 * @param	string
	 * @return	bool
	 */
	protected function _has_operator($str)
	{
		return (bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($str));
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the SQL string operator
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _get_operator($str)
	{
		static $_operators;

		if (empty($_operators))
		{
			$_les = ($this->_like_escape_str !== '')
				? '\s+'.preg_quote(trim(sprintf($this->_like_escape_str, $this->_like_escape_chr)), '/')
				: '';
			$_operators = array(
				'\s*(?:<|>|!)?=\s*',             // =, <=, >=, !=
				'\s*<>?\s*',                     // <, <>
				'\s*>\s*',                       // >
				'\s+IS NULL',                    // IS NULL
				'\s+IS NOT NULL',                // IS NOT NULL
				'\s+EXISTS\s*\(.*\)',        // EXISTS(sql)
				'\s+NOT EXISTS\s*\(.*\)',    // NOT EXISTS(sql)
				'\s+BETWEEN\s+',                 // BETWEEN value AND value
				'\s+IN\s*\(.*\)',            // IN(list)
				'\s+NOT IN\s*\(.*\)',        // NOT IN (list)
				'\s+LIKE\s+\S.*('.$_les.')?',    // LIKE 'expr'[ ESCAPE '%s']
				'\s+NOT LIKE\s+\S.*('.$_les.')?' // NOT LIKE 'expr'[ ESCAPE '%s']
			);

		}

		return preg_match('/'.implode('|', $_operators).'/i', $str, $match)
			? $match[0] : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Protect Identifiers
	 *
	 * This function is used extensively by the Query Builder class, and by
	 * a couple functions in this class.
	 * It takes a column or table name (optionally with an alias) and inserts
	 * the table prefix onto it. Some logic is necessary in order to deal with
	 * column names that include the path. Consider a query like this:
	 *
	 * SELECT hostname.database.table.column AS c FROM hostname.database.table
	 *
	 * Or a query with aliasing:
	 *
	 * SELECT m.member_id, m.member_name FROM members AS m
	 *
	 * Since the column name can include up to four segments (host, DB, table, column)
	 * or also have an alias prefix, we need to do a bit of work to figure this out and
	 * insert the table prefix (if it exists) in the proper position, and escape only
	 * the correct identifiers.
	 *
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @param	bool
	 * @return	string
	 */
	public function protect_identifiers($item, $prefix_single = FALSE, $protect_identifiers = NULL, $field_exists = TRUE)
	{
		if ( ! is_bool($protect_identifiers))
		{
			$protect_identifiers = $this->_protect_identifiers;
		}

		if (is_array($item))
		{
			$escaped_array = array();
			foreach ($item as $k => $v)
			{
				$escaped_array[$this->protect_identifiers($k)] = $this->protect_identifiers($v, $prefix_single, $protect_identifiers, $field_exists);
			}

			return $escaped_array;
		}

		// This is basically a bug fix for queries that use MAX, MIN, etc.
		// If a parenthesis is found we know that we do not need to
		// escape the data or add a prefix. There's probably a more graceful
		// way to deal with this, but I'm not thinking of it -- Rick
		//
		// Added exception for single quotes as well, we don't want to alter
		// literal strings. -- Narf
		if (strcspn($item, "()'") !== strlen($item))
		{
			return $item;
		}

		// Convert tabs or multiple spaces into single spaces
		$item = preg_replace('/\s+/', ' ', trim($item));

		// If the item has an alias declaration we remove it and set it aside.
		// Note: strripos() is used in order to support spaces in table names
		if ($offset = strripos($item, ' AS '))
		{
			$alias = ($protect_identifiers)
				? substr($item, $offset, 4).$this->escape_identifiers(substr($item, $offset + 4))
				: substr($item, $offset);
			$item = substr($item, 0, $offset);
		}
		elseif ($offset = strrpos($item, ' '))
		{
			$alias = ($protect_identifiers)
				? ' '.$this->escape_identifiers(substr($item, $offset + 1))
				: substr($item, $offset);
			$item = substr($item, 0, $offset);
		}
		else
		{
			$alias = '';
		}

		// Break the string apart if it contains periods, then insert the table prefix
		// in the correct location, assuming the period doesn't indicate that we're dealing
		// with an alias. While we're at it, we will escape the components
		if (strpos($item, '.') !== FALSE)
		{
			$parts = explode('.', $item);

			// Does the first segment of the exploded item match
			// one of the aliases previously identified? If so,
			// we have nothing more to do other than escape the item
			//
			// NOTE: The ! empty() condition prevents this method
			//       from breaking when QB isn't enabled.
			if ( ! empty($this->qb_aliased_tables) && in_array($parts[0], $this->qb_aliased_tables))
			{
				if ($protect_identifiers === TRUE)
				{
					foreach ($parts as $key => $val)
					{
						if ( ! in_array($val, $this->_reserved_identifiers))
						{
							$parts[$key] = $this->escape_identifiers($val);
						}
					}

					$item = implode('.', $parts);
				}

				return $item.$alias;
			}

			// Is there a table prefix defined in the config file? If not, no need to do anything
			if ($this->dbprefix !== '')
			{
				// We now add the table prefix based on some logic.
				// Do we have 4 segments (hostname.database.table.column)?
				// If so, we add the table prefix to the column name in the 3rd segment.
				if (isset($parts[3]))
				{
					$i = 2;
				}
				// Do we have 3 segments (database.table.column)?
				// If so, we add the table prefix to the column name in 2nd position
				elseif (isset($parts[2]))
				{
					$i = 1;
				}
				// Do we have 2 segments (table.column)?
				// If so, we add the table prefix to the column name in 1st segment
				else
				{
					$i = 0;
				}

				// This flag is set when the supplied $item does not contain a field name.
				// This can happen when this function is being called from a JOIN.
				if ($field_exists === FALSE)
				{
					$i++;
				}

				if( '#' != substr($parts[$i], 0, 1) ){
					// Verify table prefix and replace if necessary
					if ($this->swap_pre !== '' && strpos($parts[$i], $this->swap_pre) === 0)
					{
						$parts[$i] = preg_replace('/^'.$this->swap_pre.'(\S+?)/', $this->dbprefix.'\\1', $parts[$i]);
					}
					// We only add the table prefix if it does not already exist
					elseif (strpos($parts[$i], $this->dbprefix) !== 0)
					{
						$parts[$i] = $this->dbprefix.$parts[$i];
					}

					// Put the parts back together
					$item = implode('.', $parts);
				}
			}

			if ($protect_identifiers === TRUE)
			{
				$item = $this->escape_identifiers($item);
			}

			return $item.$alias;
		}

		// Is there a table prefix? If not, no need to insert it
		if ($this->dbprefix !== '')
		{
			if( '#' != substr($item, 0, 1) ){
				// Verify table prefix and replace if necessary
				if ($this->swap_pre !== '' && strpos($item, $this->swap_pre) === 0)
				{
					$item = preg_replace('/^'.$this->swap_pre.'(\S+?)/', $this->dbprefix.'\\1', $item);
				}
				// Do we prefix an item with no segments?
				elseif ($prefix_single === TRUE && strpos($item, (string) $this->dbprefix) !== 0)
				{
					$item = $this->dbprefix.$item;
				}
			}
		}

		if ($protect_identifiers === TRUE && ! in_array($item, $this->_reserved_identifiers))
		{
			$item = $this->escape_identifiers($item);
		}

		return $item.$alias;
	}

	function display_error($error = '')
	{
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

		echo hc_show_detailed_error($heading, $message, 500);
		// exit;
	}

	public function removeInvisibleCharacters( $str, $url_encoded = TRUE )
	{
		$non_displayables = array();

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}