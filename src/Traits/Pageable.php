<?php
	
namespace LivePersonInc\LiveEngageLaravel\Traits;

use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
	
/**
 * Pageable trait.
 */
trait Pageable
{
	
	/**
	 * next function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function next()
	{
		/** @scrutinizer ignore-call */
		if ($this->metaData->next) {
			/** @scrutinizer ignore-call */
			$func = $this->historyFunction;
			$next = LiveEngage::$func($this->metaData->start, $this->metaData->end, $this->metaData->next->href);
			if ($next) {
		
				$next->_metadata->start = $this->metaData->start;
				$next->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $next->_metadata);
				
				$collection = new self($next->records);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return null;
			}
		}
		
		return null;
		
	}

	/**
	 * prev function.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function prev()
	{
		/** @scrutinizer ignore-call */
		if ($this->metaData->prev) {
			/** @scrutinizer ignore-call */
			$func = $this->historyFunction;
			$prev = LiveEngage::$func($this->metaData->start, $this->metaData->end, $this->metaData->prev->href);
			if ($prev) {
		
				$prev->_metadata->start = $this->metaData->start;
				$prev->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $prev->_metadata);
				
				$collection = new self($prev->records);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return null;
			}
		}
		
		return null;
		
	}
	
}