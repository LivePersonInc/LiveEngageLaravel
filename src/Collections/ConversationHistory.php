<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;

class ConversationHistory extends Collection {
	
	private $instance;
	
	public function __construct(array $models = [], LiveEngageLaravel $instance = null) {
		
		$this->instance = $instance;
		return parent::__construct($models);
		
	}
	
	public function next() {
		
		if (!$this->instance) return false;
		
		$instance = $this->instance;
		
		$next = $instance->retrieveMsgHistory($instance->start, $instance->end, $instance->next);
		if (property_exists($next->_metadata, 'next')) {
			$instance->next = $next->_metadata->next->href;
			
			$history = [];
			foreach ($next->conversationHistoryRecords as $item) {
				$history[] = new Conversation((array) $item);
			}
			
			return $this->merge(new ConversationHistory($history));
			
		} else {
			return false;
		}
		
	}
	
	public function prev() {
		
		if (!$this->instance) return false;
		
		$instance = $this->instance;
		
		$prev = $instance->retrieveMsgHistory($instance->start, $instance->end, $instance->prev);
		if (property_exists($prev->_metadata, 'prev')) {
			$instance->prev = $prev->_metadata->prev->href;
			
			$history = [];
			foreach ($next->conversationHistoryRecords as $item) {
				$history[] = new Conversation((array) $item);
			}
			
			return $this->merge(new ConversationHistory($history));
			
		} else {
			return false;
		}
		
	}
	
}