<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    protected $appends = [
        'plaint_text',
        'time',
    ];

    public function getTextAttribute()
    {
        if ($this->type == 'PLAIN') {
            return $this->messageData->msg->text;
        } elseif ($this->type == 'RICH_CONTENT') {
            return 'RICH_CONTENT';
        } else {
            return isset($this->attributes['text']) ? $this->attributes['text'] : '';
        }
    }

    public function getPlainTextAttribute()
    {
        return strip_tags($this->text);
    }

    public function getTimeAttribute()
    {
        return new Carbon($this->attributes['time']);
    }

    public function __toString()
    {
        if ($this->type == 'TEXT_PLAIN') {
            return $this->messageData->msg->text;
        } elseif ($this->type == 'RICH_CONTENT') {
            return 'RICH_CONTENT';
        }
    }
}
