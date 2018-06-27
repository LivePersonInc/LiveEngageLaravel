<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;
use LivePersonInc\LiveEngageLaravel\Models\Info;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;
use LivePersonInc\LiveEngageLaravel\Models\Campaign;

/**
 * ConversationHistory class.
 * 
 * @extends Collection
 */
class ConversationHistory extends Collection
{
	public $metaData;

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param array $models (default: [])
	 * @return void
	 */
	public function __construct(array $models = [])
	{
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Conversation') ? $item : new Conversation((array) $item);
		}, $models);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	/**
	 * find function.
	 * 
	 * @access public
	 * @param mixed $engagementID
	 * @return Conversation
	 */
	public function find($engagementID)
	{
		$result = $this->filter(function($value) use ($engagementID) {
			return $value->info->conversationId == $engagementID;
		});
		
		return $result->first();
	}

	/**
	 * next function.
	 * 
	 * @access public
	 * @return ConversationHistory
	 */
	public function next()
	{
		/** @scrutinizer ignore-call */
		if ($this->metaData->next) {
			/** @scrutinizer ignore-call */
			$next = LiveEngage::retrieveMsgHistory($this->metaData->start, $this->metaData->end, $this->metaData->next->href);
			if ($next) {
		
				$next->_metadata->start = $this->metaData->start;
				$next->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $next->_metadata);
				
				$collection = new self($next->conversationHistoryRecords);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return new self();
			}
		}
		
		return new self();
		
	}

	/**
	 * prev function.
	 * 
	 * @access public
	 * @return ConversationHistory
	 */
	public function prev()
	{
		/** @scrutinizer ignore-call */
		if ($this->metaData->prev) {
			/** @scrutinizer ignore-call */
			$prev = LiveEngage::retrieveMsgHistory($this->metaData->start, $this->metaData->end, $this->metaData->prev->href);
			if ($prev) {
		
				$prev->_metadata->start = $this->metaData->start;
				$prev->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $prev->_metadata);
				
				$collection = new self($prev->conversationHistoryRecords);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return new self();
			}
		}
		
		return new self();
		
	}
	
	/**
	 * merge function.
	 * 
	 * @access public
	 * @param mixed $collection
	 * @return ConversationHistory
	 */
	public function merge($collection) {
		
		$meta = $collection->metaData;
		$collection = parent::merge($collection);
		$this->metaData = $meta;
		$collection->metaData = $meta;
		
		return $collection;
		
	}
}
