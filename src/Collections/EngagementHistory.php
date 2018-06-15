<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;
use LivePersonInc\LiveEngageLaravel\Models\Engagement;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Models\Info;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;
use LivePersonInc\LiveEngageLaravel\Models\Campaign;

class EngagementHistory extends Collection
{
	public $metaData;

	public function __construct(array $models = [])
	{
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Engagement') ? $item : new Engagement((array) $item);
		}, $models);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	public function find($engagementID)
	{
		$result = $this->filter(function($value, $key) use ($engagementID) {
			return $value->info->sessionId == $engagementID;
		});
		
		return $result->first();
	}

	public function next()
	{
		
		if ($this->metaData->next) {
			$next = LiveEngage::retrieveHistory($this->metaData->start, $this->metaData->end, $this->metaData->next->href);
			if ($next) {
		
				$next->_metadata->start = $this->metaData->start;
				$next->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $next->_metadata);
				
				$collection = new self($next->interactionHistoryRecords);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return false;
			}
		}
		
		return false;
		
	}

	public function prev()
	{
		if ($this->metaData->prev) {
			$prev = LiveEngage::retrieveHistory($this->metaData->start, $this->metaData->end, $this->metaData->prev->href);
			if ($prev) {
		
				$prev->_metadata->start = $this->metaData->start;
				$prev->_metadata->end = $this->metaData->end;
		
				$meta = new MetaData((array) $prev->_metadata);
				
				$collection = new self($prev->interactionHistoryRecords);
				$collection->metaData = $meta;
				
				return $collection;
				
			} else {
				return false;
			}
		}
		
		return false;
		
	}
	
	public function merge($collection) {
		
		$collection = parent::merge($collection);
		$this->metaData = $collection->metaData;
		
		return $collection;
		
	}
}
