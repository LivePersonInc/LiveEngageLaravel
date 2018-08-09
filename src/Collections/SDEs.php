<?php
/**
 * SDEs
 *
 * @package LivePersonInc\LiveEngageLaravel\Collections
 */

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\SDE;

/**
 * SDEs class.
 * 
 * @extends Collection
 */
class SDEs extends Collection
{
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
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\SDE') ? $item : new SDE((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	/**
	 * getSDE function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return mixed
	 */
	public function getSDE($type)
	{
		
		$sorted = $this->sortByDesc(function($item) {
			return $item->serverTimeStamp;
		});
		
		return $sorted->firstWhere('sdeType', $type);
		
	}
	
}