<?php
/**
 * ConversationHistory
 *
 * @package LivePersonInc\LiveEngageLaravel\Collections
 */

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;
use LivePersonInc\LiveEngageLaravel\Traits\Pageable;

/**
 * ConversationHistory class.
 * 
 * @extends Collection
 */
class ConversationHistory extends Collection
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
	 * Required for the Pageable trait (default value: 'retrieveMsgHistory')
	 * 
	 * @var string
	 * @access protected
	 */
	protected $historyFunction = 'retrieveMsgHistory';

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
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Conversation') ? $item : new Conversation((array) $item);
		}, $models ?: []);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	/**
	 * find function.
	 * 
	 * @access public
	 * @param mixed $engagementID
	 * @return Conversation
	 */
	public function find($engagementID)
	{
		$result = $this->filter(function($value) use ($engagementID) {
			return $value->info->conversationId == $engagementID;
		});
		
		return $result->first();
	}
	
	/**
	 * merge function.
	 * 
	 * @access public
	 * @param mixed $collection
	 * @return ConversationHistory
	 */
	public function merge($collection) {
		
		$meta = $collection->metaData;
		$collection = parent::merge($collection);
		$this->metaData = $meta;
		$collection->metaData = $meta;
		
		return $collection;
		
	}
	
	public function toArray()
	{
		return $this->map(function($conversation) {
			return $conversation->export;
		});
	}
}
