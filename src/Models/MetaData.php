<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
	protected $guarded = [];
	
	public $next;
	public $pref;
	public $start;
	public $end;
}