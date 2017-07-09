<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_Resource_Tab_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	/**
	 * Init the entity type
	 *
	 */
	public function _construct()
	{
		$this->_init('smarttabs/tab');

		$this->_map['fields']['tab_id'] = 'main_table.tab_id';
		$this->_map['fields']['store'] = 'store_table.store_id';
	}
	
	/**
	 * Add group_id and sort_order ORDER clauses
	 *
	 * @return $this
	 */
	protected function _beforeLoad()
	{
		
		$this->addOrder('group_id', 'ASC');
		$this->addOrder('sort_order', 'ASC');
		
		return parent::_beforeLoad();		
	}
	/**
	 * Add filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 * @return Mage_Cms_Model_Resource_Page_Collection
	*/
	public function addStoreFilter($store, $withAdmin = true)
	{
		if (!$this->getFlag('store_filter_added')) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
	
			if (!is_array($store)) {
				$store = array($store);
			}
	
			if ($withAdmin) {
				$store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
			}
	
			$this->addFilter('store', array('in' => $store), 'public');
		}

		return $this;
	}
	
	/**
	 * Unserialize the serialized fields after loading the collection
	 *
	 * @return $this
	 */
	protected function _afterLoad()
	{
		foreach($this->getItems() as $item) {
			$item->getResource()->unserializeFields($item);
		}

		return parent::_afterLoad();
	}

	/**
	 * Join store relation table if there is store filter
	 *
	 * @return $this
	*/
	protected function _renderFiltersBefore()
	{
		if ($this->getFilter('store')) {
			$this->getSelect()->join(
				array('store_table' => $this->getTable('smarttabs/tab_store')),
				'main_table.tab_id = store_table.tab_id',
				array()
			)->group('main_table.tab_id');
		}

		return parent::_renderFiltersBefore();
	}
}