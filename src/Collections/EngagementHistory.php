<?php
/**
 * EngagementHistory
 *
 * @package LivePersonInc\LiveEngageLaravel\Collections
 */

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Engagement;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Traits\Pageable;

/**
 * EngagementHistory class.
 * 
 * @extends Collection
 */
class EngagementHistory extends Collection
{
	use Pageable;
	
	/**
	 * metaData
	 * 
	 * @var \LivePersonInc\LiveEngageLaravel\Models\MetaData
	 * @access public
	 */
	public $metaData;
	
	/**
	 * historyFunction
	 * 
	 * Required for the Pageable trait (default value: 'retrieveHistory')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $historyFunction = 'retrieveHistory';

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param array $models (default: [])
	 * @return void
	 */
	public function __construct($models = [])
	{
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Engagement') ? $item : new Engagement((array) $item);
		}, $models ?: []);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	/**
	 * find function.
	 * 
	 * @access public
	 * @param mixed $engagementID
	 * @return Engagement
	 */
	public function find($engagementID)
	{
		$result = $this->filter(function($value) use ($engagementID) {
			return $value->info->sessionId == $engagementID;
		});
		
		return $result->first();
	}
	
	/**
	 * merge function.
	 * 
	 * @access public
	 * @param EngagementHistory $collection
	 * @return EngagementHistory
	 */
	public function merge($collection) {
		
		$meta = $collection->metaData;
		$collection = parent::merge($collection);
		$this->metaData = $meta;
		$collection->metaData = $meta;
		
		return $collection;
		
	}
}
