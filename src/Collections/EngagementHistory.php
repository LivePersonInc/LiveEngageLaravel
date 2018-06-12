<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;
use LivePersonInc\LiveEngageLaravel\Models\Engagement;

class EngagementHistory extends Collection
{
    private $instance;

    public function __construct(array $models = [], LiveEngageLaravel $instance = null)
    {
        $this->instance = $instance;

        parent::__construct($models);
    }

    public function next()
    {
        if (! $this->instance) {
            return false;
        }

        $instance = $this->instance;

        $next = $instance->retrieveHistory($instance->start, $instance->end, $instance->next);
        if (property_exists($next->_metadata, 'next')) {
            $instance->next = $next->_metadata->next->href;

            $history = [];
            foreach ($next->interactionHistoryRecords as $item) {
                $history[] = new Engagement((array) $item);
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

        $prev = $instance->retrieveHistory($instance->start, $instance->end, $instance->prev);
        if (property_exists($prev->_metadata, 'prev')) {
            $instance->prev = $prev->_metadata->prev->href;

            $history = [];
            foreach ($next->interactionHistoryRecords as $item) {
                $history[] = new Engagement((array) $item);
            }

            return $this->merge(new self($history));
        } else {
            return false;
        }
    }
}
