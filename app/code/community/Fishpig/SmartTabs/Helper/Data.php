<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_smartTabs = array();
	
	/**
	 * Get the full tabs html string
	 *
	 * @return string
	 */
	public function getFullTabsHtml()
	{
		return Mage::getSingleton('core/layout')
			->createBlock('smarttabs/tabs')
			->setTemplate('smarttabs/tabs.phtml')
			->toHtml();
	}
	
	/**
	 * Get a collection of smart tabs for $product
	 *
	 * @param Mage_Catalog_Model_Product $_product
	 * @return Fishpig_SmartTabs_Model_Resource_Tab_Collection
	 */
	public function getSmartTabs(Mage_Catalog_Model_Product $_product)
	{
		if (isset($this->_smartTabs[$_product->getId()])) {
			return $this->_smartTabs[$_product->getId()];
		}
		
		$tabs = Mage::getResourceModel('smarttabs/tab_collection')
			->addStoreFilter(Mage::app()->getStore())
			->addFieldToFilter('status', 1)
			->load()
			->walk('afterLoad');
		
		$tabsArray = array();
		
		foreach($tabs as $key => $tab) {
			if (!$tab->setProduct($_product)->canApplyToProduct($_product)) {
				unset($tabs[$key]);
			}
		}
		
		$smartTabs = array();
		
		foreach($tabs as $key => $tab) {
			$alias = $tab->getAlias();
			
			if (!isset($smartTabs[$alias])) {
				$smartTabs[$alias] = $tab;
			}
			else if (!$tab->getFilters()) {
				continue;
			}
			else if (!$smartTabs[$alias]->getFilters()) {
				$smartTabs[$alias] = $tab;
			}
			else {
				continue;
				print_r($smartTabs[$alias]->getFilters());exit;
			}
		}
		
		$this->_smartTabs[$_product->getId()] = $smartTabs;
		 
		 return $this->_smartTabs[$_product->getId()];
	}
}
