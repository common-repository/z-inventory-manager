<?php if (! defined('ABSPATH')) exit;
class PW1_Html_Widget_Pager extends _PW1
{
	private $_offsetParam = 'offset_';
	private $_limitParam = 'limit_';

	public function slice( array $entries )
	{
		$ret = array_slice( $entries, $this->offset, $this->limit, TRUE );
		return $ret;
	}

	public function getLimitOffset( array $params, $defaultLimit = 10, $defaultOffset = NULL )
	{
		$limit = isset( $params[$this->_limitParam] ) ? $params[$this->_limitParam] : $defaultLimit;
		$offset = isset( $params[$this->_offsetParam] ) ? $params[$this->_offsetParam] : $defaultOffset;
		$ret = array( $limit, $offset );
		return $ret;
	}

	public function render( $totalCount, $limit = 10, $offset = NULL )
	{
		$ret = '';

		if( ! $totalCount ) return $ret;
		if( $totalCount <= $limit ) return $ret;

		$displayed1 = $totalCount ? $offset + 1 : 0;
		$displayed2 = $limit ? min( $offset + $limit, $totalCount ) : $totalCount;

		$prevOffset = NULL;
		if( $limit && $offset ){
			$prevOffset = max( $offset - $limit, 0 );
		}

		$firstOffset = NULL;
		if( $offset && $prevOffset ){
			$firstOffset = 0;
		}

		$nextOffset = NULL;
		if( $limit && ($totalCount > $displayed2) ){
			$nextOffset = $offset + $limit;
		}

		$lastOffset = NULL;
		if( $limit && ($totalCount > $limit) ){
			$lastOffset = ( ceil($totalCount / $limit) - 1 ) * $limit;
			if( $lastOffset == $nextOffset ) $lastOffset = NULL;
			if( $lastOffset == $offset ) $lastOffset = NULL;
		}

		ob_start();
?>

<nav>
<ul class="pw-inline pw-valign-middle">
	<?php if( NULL !== $firstOffset ) : ?>
		<?php if( ! $firstOffset ) $firstOffset = 'NULL'; ?>
		<li>
			<a href="URI:.?<?php echo $this->_offsetParam; ?>=<?php echo $firstOffset; ?>" title="__First Page__">&laquo; __First Page__</a>
		</li>
	<?php endif; ?>

	<?php if( NULL !== $prevOffset ) : ?>
		<?php if( ! $prevOffset ) $prevOffset = 'NULL'; ?>
		<li>
			<a href="URI:.?<?php echo $this->_offsetParam; ?>=<?php echo $prevOffset; ?>" title="__Previous Page__">&lsaquo; __Previous Page__</a>
		</li>
	<?php endif; ?>

	<li>
		<strong><?php echo $displayed1; ?> - <?php echo $displayed2; ?> / <?php echo $totalCount; ?></strong>
	</li>

	<?php if( $nextOffset ) : ?>
		<li>
			<a href="URI:.?<?php echo $this->_offsetParam; ?>=<?php echo $nextOffset; ?>" title="__Next Page__">__Next Page__ &rsaquo;</a>
		</li>
	<?php endif; ?>

	<?php if( $lastOffset ) : ?>
		<li>
			<a href="URI:.?<?php echo $this->_offsetParam; ?>=<?php echo $lastOffset; ?>" title="__Last Page__">__Last Page__ &raquo;</a>
		</li>
	<?php endif; ?>
</ul>
</nav>

<?php
		$ret = ob_get_clean();
		return $ret;
	}
}