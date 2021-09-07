<?php
/**
 * AccountStatus
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountStatus extends Model
{
	protected $guarded = [];

	public function __construct(array $array)
	{
		$output = [];
		foreach ($array as $key=>$value) {
			$output[Str::snake($key)] = $value;
		}
		parent::__construct($output);
	}
}
