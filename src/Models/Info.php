<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $guarded = [];
    protected $appends = [
        'startTime',
    ];

    public function getStartTimeAttribute()
    {
        return new Carbon($this->attributes['startTime']);
    }

    public function getSessionIdAttribute()
    {
        return str_replace($this->accountId, '', $this->engagementId);
    }
}
