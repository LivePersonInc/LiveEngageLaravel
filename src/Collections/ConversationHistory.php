<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;

class ConversationHistory extends Collection
{
    private $instance;

    public function __construct(array $models = [], LiveEngageLaravel $instance = null)
    {
        $this->instance = $instance;

        parent::__construct($models);
    }
    
    public function find($engagementID)
    {
	    $result = $this->filter(function ($value, $key) use ($engagementID) {
		    return $value->info->conversationId == $engagementID;
	    });
	    
	    return $result->first();
    }

    public function next()
    {
        if (! $this->instance) {
            return false;
        }

        $instance = $this->instance;

        $next = $instance->retrieveMsgHistory($instance->start, $instance->end, $instance->next);
        if (property_exists($next->_metadata, 'next')) {
            $instance->next = $next->_metadata->next->href;

            $history = [];
            foreach ($next->conversationHistoryRecords as $item) {
                $history[] = new Conversation((array) $item);
            }

            return $this->merge(new self($history));
        } else {
            return false;
        }
    }

    public function prev()
    {
        if (! $this->instance) {
            return false;
        }

        $instance = $this->instance;

        $prev = $instance->retrieveMsgHistory($instance->start, $instance->end, $instance->prev);
        if (property_exists($prev->_metadata, 'prev')) {
            $instance->prev = $prev->_metadata->prev->href;

            $history = [];
            foreach ($next->conversationHistoryRecords as $item) {
                $history[] = new Conversation((array) $item);
            }

            return $this->merge(new self($history));
        } else {
            return false;
        }
    }
}
