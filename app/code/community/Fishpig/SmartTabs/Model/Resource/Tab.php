<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_Resource_Tab extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Fields to be serialized before saving
	 * This applies to the filter fields
	 *
	 * @var array
	 */
     protected $_serializableFields = array(
     	'filters' => array('a:0:{}', array()),
     	'content' => array('a:0:{}', array()),
     );

	/**
	 * Init the entity type
	 *
	 */
	public function _construct()
	{
		$this->_init('smarttabs/tab', 'tab_id');
	}
	
	/**
	 * Retrieve select object for load object data
	 * This gets the default select, plus the attribute id and code
	 *
	 * @param   string $field
	 * @param   mixed $value
	 * @return  Zend_Db_Select
	*/
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()))
			->where("`main_table`.`{$field}` = ?", $value)
			->limit(1);
			
		$adminId = Mage_Core_Model_App::ADMIN_STORE_ID;
		
		$storeId = $object->getStoreId();
		
		if ($storeId !== $adminId) {
			$select->join(
				array('store' => $this->getTable('smarttabs/tab_store')),
				$this->_getReadAdapter()->quoteInto('`store`.`tab_id` = `main_table`.`tab_id` AND `store`.`store_id` IN (?)', array($adminId, $storeId))
			);
			
			$select->order('store.store_id DESC');
		}
		
		return $select;
	}
	
	/**
	 * Function called after a model is loaded (but not when a collection of models are loaded)
	 * If filters set, unserialize (convert to an array)
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$stores = $this->lookupStoreIds($object->getId());

			$object->setData('store_id', $stores);
		}
		
		if ($this->isAdmin()) {
			$this->_loadAdminData($object);
		}
		
		return parent::_afterLoad($object);
	}

	/**
	 * Ensure that the content field is cleaned before saving
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	public function save(Mage_Core_Model_Abstract $object)
	{
		$this->cleanContentField($object);
		$this->cleanFilterField($object);
		
		return parent::save($object);
	}

	/**
	 * Function called before a model is saved
	 * Serializes the filters array (if set) and performs other data checks
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if ($object->isObjectNew()) {
			$object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
		}

		$object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
		
		return parent::_beforeSave($object);
	}

	/**
	 * Function called after a model is saved
	 * Save store associations
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$oldStores = $this->lookupStoreIds($object->getId());
			$newStores = (array)$object->getStores();
	
			if (empty($newStores)) {
				$newStores = (array)$object->getStoreId();
			}
	
			$table  = $this->getTable('smarttabs/tab_store');
			$insert = array_diff($newStores, $oldStores);
			$delete = array_diff($oldStores, $newStores);
			
			if ($delete) {
				$this->_getWriteAdapter()->delete($table, array('tab_id = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete));
			}
			
			if ($insert) {
				$data = array();
			
				foreach ($insert as $storeId) {
					$data[] = array(
						'tab_id'  => (int) $object->getId(),
						'store_id' => (int) $storeId
					);
				}
				
				if (count($data) > 1) {
					$this->_getWriteAdapter()->insertMultiple($table, $data);
				}
				else {
					$this->_getWriteAdapter()->insertMultiple($table, $data[0]);
				}
			}
		}
		
		return parent::_afterSave($object);
	}
	
	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	*/
	public function lookupStoreIds($pageId)
	{
		$select = $this->_getReadAdapter()->select()
			->from($this->getTable('smarttabs/tab_store'), 'store_id')
			->where('tab_id = ?', (int)$pageId);
	
		return $this->_getReadAdapter()->fetchCol($select);
	}
	
	/**
	 * Determine whether the current store is the Admin store
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return (int)Mage::app()->getStore()->getId() === Mage_Core_Model_App::ADMIN_STORE_ID;
	}
	
	/**
	 * Clean the content field
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	public function cleanContentField(Mage_Core_Model_Abstract $object)
	{
		if (is_array(($content = $object->getContent()))) {
			if (isset($content['type'])) {
				if (isset($content['sources'][$content['type']])) {
					foreach($content['sources'] as $typeId => $type) {
						if ($typeId != $content['type']) {
							unset($content['sources'][$typeId]);
						}
					}
				}
				else {
					$content['sources'] = array();
				}
			}
			else {
				$content = null;
			}
			
			$object->setContent($content);
		}
		
		return $this;
	}
	
	public function cleanFilterField(Mage_Core_Model_Abstract $object)
	{
		if (is_array(($filters = $object->getFilters()))) {
			if (isset($filters['product']) && is_array($filters['product'])) {
				foreach($filters['product'] as $attribute => $values) {
					if ($attribute === 'sku') {
						if (trim($values, ', ') === '') {
							unset($filters['product'][$attribute]);
						}
						else {
							$values = explode(',', $values);
						}
					}
					if (!is_array($values) || count($values) === 0) {
						unset($filters['product'][$attribute]);
					}
				}
			}
			
			if (isset($filters['price']) && is_array(($prices = $filters['price']))) {
				foreach($prices as $attribute => $values) {
					if (!is_array($values)) {
						continue;
					}

					if (!isset($values['min']) || !$values['min']) {
						unset($prices[$attribute]['min']);
					}
					
					if (!isset($values['max']) || !$values['max']) {
						unset($prices[$attribute]['max']);
					}
					
					if (count($prices[$attribute]) === 0) {
						unset($prices[$attribute]);
					}
				}
				
				if (count($prices) > 0) {
					$filters['price'] = $prices;
				}
				else {
					unset($filters['price']);
				}
			}
			
			if (count($filters) > 0) {
				$object->setFilters($filters);
			}
			else {
				$object->setFilters(null);
			}
		}
		else {
			$object->setFilters(null);
		}

		return $this;	
	}
	
	/**
	 * Convert the filter data into a format used to pre-fill Admin forms
	 *
	 * @param Fishpig_AttributeSplashPro_Model_Page $page
	 * @return array
	 */
	protected function _loadAdminData(Mage_Core_Model_Abstract $object)
	{
		$adminData = array();
		
		if (is_array($data = $object->getContent())) {
			if (isset($data['type'])) {
				$typeId = $data['type'];
				$adminData['content_type'] = $typeId;

				if (isset($data['sources']) && isset($data['sources'][$typeId])) {
					foreach($data['sources'][$typeId] as $key => $value) {
						$adminData['content_' . $typeId . '_' . $key] = $value;
					}
				}
			}
		}
				
		if (is_array($data = $object->getFilters())) {
			if (isset($data['price'])) {
				foreach($data['price'] as $attribute => $values) {
					if (is_array($values)) {
						foreach($values as $key => $value) {
							$adminData['filters_price_' . $attribute . '_' . $key] = $value;		
						}
					}
					else if ($values !== '0') {
						$adminData['filters_price_' . $attribute] = $values;
					} elseif ($attribute === 'is_on_sale' && $values === '0') {
						$adminData['filters_price_' . $attribute] = $values;    					
					}
				}
			}
			
			if (isset($data['product'])) {
				foreach($data['product'] as $key => $value) {
					$adminData['filters_product_'  . $key] = $value;
				}
			}
		}

		if (is_array($data = $object->getFilters())) {
			if (isset($data['attribute'])) {
				foreach($data['attribute'] as $attribute => $value) {
					$adminData['filters_attribute_' . $attribute] = $value;
				}
			}
		}

		$object->setAdminData($adminData);
		
		return $this;
	}
}
