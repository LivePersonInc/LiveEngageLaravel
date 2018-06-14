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

class ConversationHistory extends Collection
{
	private $instance;
	public $metaData;

	public function __construct(array $models = [], LiveEngageLaravel $instance = null)
	{
		$this->instance = $instance;
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	public function find($engagementID)
	{
		$result = $this->filter(function($value, $key) use ($engagementID) {
			return $value->info->conversationId == $engagementID;
		});
		
		return $result->first();
	}

	public function next()
	{
		if ($this->metaData->next) {
			$next = LiveEngage::retrieveMsgHistory($this->metaData->start, $this->metaData->end, $this->metaData->next->href);
			if ($next) {
		
				$meta = new MetaData((array) $next->_metadata);
				
				$results = array_map(function($item) {
					return new Conversation((array) $item);
				}, $next->conversationHistoryRecords);
				
				$collection = new self($results);
				$meta->start = $this->metaData->start;
				$meta->end = $this->metaData->end;
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return false;
			}
		}
	}

	public function prev()
	{
		if ($this->metaData->prev) {
			$prev = LiveEngage::retrieveMsgHistory($this->metaData->start, $this->metaData->end, $this->metaData->prev->href);
			if ($prev) {
		
				$meta = new MetaData((array) $prev->_metadata);
				
				$results = array_map(function($item) {
					return new Conversation((array) $item);
				}, $prev->conversationHistoryRecords);
				
				$collection = new self($results);
				$meta->start = $this->metaData->start;
				$meta->end = $this->metaData->end;
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return false;
			}
		}
	}
	
	public function merge($collection) {
		
		$collection = parent::merge($collection);
		$this->metaData = $collection->metaData;
		
		return $collection;
		
	}
}
