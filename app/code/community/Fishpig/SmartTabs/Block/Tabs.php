<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Tabs extends Mage_Catalog_Block_Product_View_Tabs
{
	/**
	 * Determine whether this is enabled for the product page
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('smarttabs/product/enabled');
	}

	/**
	 * Determine whether this is enabled for the product page
	 *
	 * @return bool
	 */
	public function getTabIntegrationMethod()
	{
		return trim(Mage::getStoreConfig('smarttabs/product/integration_method'));
	}

	/**
	 * Determine whether to use the detailed_info integration method
	 *
	 * @return bool
	 */
	public function isIntegratedUsingChildGroup()
	{
		return $this->getTabIntegrationMethod() === Fishpig_SmartTabs_Model_System_Config_Source_Integration_Method::METHOD_DETAILED_INFO || Mage::getSingleton('core/design_package')->getPackageName() === 'rwd';
	}
	
	/**
	 * Determine whether to use the info_tabs integration method
	 *
	 * @return bool
	 */
	public function isDefaultIntegrationMethod()
	{
		return !$this->getTabIntegrationMethod() 
			|| $this->getTabIntegrationMethod() === Fishpig_SmartTabs_Model_System_Config_Source_Integration_Method::METHOD_DEFAULT;
	}

	/**
	 * Retrieve the current product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */	
	public function getProduct()
	{
		if (!$this->hasProduct()) {
			$this->setProduct(false);
			
			if ($product = Mage::registry('product')) {
				$this->setProduct($product);
			}
			else if ($productId = (int)$this->getRequest()->getParam('product')) {
				$product = Mage::getModel('catalog/product')->load($productId);
				
				if ($product->getId()) {
					$this->setProduct($product);
				}
			}
		}
		
		return $this->_getData('product');
	}

	/**
	 * Add a SmartTab to the list
	 *
	 * @param string $alias
	 * @param string $title
	 * @param Mage_Core_Block_Abstract $block
	 * @return false|$this
	 */
	function addSmartTab($alias, $title, $block)
	{
		if (!$this->isEnabled() || !$title || !$block) {
			return false;
		}
	
		$existingIndex = false;
		
		foreach($this->_tabs as $index => $tab) {
			if ($tab['alias'] === $alias) {
				$existingIndex = $index;
				break;
			}
		}

		if ($existingIndex !== false) {
			$this->_tabs[$existingIndex] = array(
				'alias' => $alias,
				'title' => $title
			);
		}
		else {
			$this->_tabs[] = array(
				'alias' => $alias,
				'title' => $title
			);
		}
	
		$this->setChild($alias, $block);
		
		return $this;
	}	

	/**
	 * Add blocks to detailed_info
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		if ($this->isEnabled()) {
			if ($this->isIntegratedUsingChildGroup()) {
				if ($tabs = $this->getSmartTabs()) {
					$productInfo = $this->getLayout()->getBlock('product.info');
					$tabsMethod = Mage::getStoreConfigFlag('smarttabs/product/show_tabs_before') ? 'insert' : 'append';

					foreach($tabs as $tab) {
						if ($tab->canDisplay() && ($tabBlock = $tab->getBlock()) !== false) {
							$productInfo->$tabsMethod($tabBlock)->addToChildGroup('detailed_info', $tabBlock);
						}
					}
				}
			}
		}
		
		return parent::_prepareLayout();
	}

	
	/**
	 * Prepare the tabs before displaying
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		if ($this->isEnabled()) {
			if ($tabs = $this->getSmartTabs()) {
				foreach($tabs as $tab) {
					if ($tab->canDisplay()) {
						if (($tabBlock = $tab->getBlock()) !== false) {
							$this->addSmartTab($tab->getAlias(), $tab->getTitle(), $tabBlock);
						}
					}
				}
			}
		}

		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the tabs collection
	 *
	 * @return Fishpig_SmartTabs_Model_Resource_Tab_Collection
	 */
	public function getSmartTabs()
	{
		return Mage::helper('smarttabs')->getSmartTabs($this->getProduct());
	}	
	
	/**
	 * Get the JS file URL
	 *
	 * @return string
	 */
	public function getTabsJsUrl()
	{
		return Mage::getBaseUrl('js') . 'fishpig/smarttabs/tabs.js';
	}
}
