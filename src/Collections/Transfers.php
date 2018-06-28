<?php
/**
 * Transfers
 *
 * @package LivePersonInc\LiveEngageLaravel\Collections
 */

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Transfer;

/**
 * Transfers class.
 * 
 * @extends Collection
 */
class Transfers extends Collection
{
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Transfer') ? $item : new Transfer((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	/**
	 * toSkillIds function.
	 * 
	 * @access public
	 * @return array
	 */
	public function toSkillIds()
	{
		$array = array_map(function($item) {
			return $item['targetSkillId'];
		}, $this->toArray());
		return $array;
	}
	
	/**
	 * find function.
	 * 
	 * @access public
	 * @param mixed $skillId
	 * @return \LivePersonInc\LiveEngageLaravel\Models\Transfer
	 */
	public function find($skillId)
	{
		$result = $this->filter(function($value) use ($skillId) {
			return $value->targetSkillId == $skillId;
		});
		
		return $result->first();
	}
}