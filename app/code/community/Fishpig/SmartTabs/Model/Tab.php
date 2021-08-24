<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_Tab extends Mage_Core_Model_Abstract
{
	/**
	 * Status flags for tabs
	 *
	 * @const int
	 */
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	/**
	 * Content source types
	 *
	 * @const int
	 */
	const CONTENT_TYPE_ID_STATIC_BLOCK = 1;
	const CONTENT_TYPE_ID_PRODUCT_ATTRIBUTE = 2;
	const CONTENT_TYPE_ID_BLOCK_TAG = 3;
	const CONTENT_TYPE_ID_LAYOUT_BLOCK = 4;
	const CONTENT_TYPE_ID_XML = 5;
	const CONTENT_TYPE_ID_TEXT = 6;
	
	/**
	 * Init the entity type
	 *
	 */
	public function _construct()
	{
		$this->_init('smarttabs/tab');
	}
	
	/**
	 * Retrieve the store ID of the tab
	 * This isn't always the only store it's associated with
	 * but the current store ID
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if (!$this->hasStoreId()) {
			$this->setStoreId((int)Mage::app()->getStore(true)->getId());
		}
		
		return (int)$this->_getData('store_id');
	}

	/**
	 * Determine whether the tab is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return (int)$this->getStatus() === self::STATUS_ENABLED;
	}
	
	/**
	 * Retrieve the tab type ID
	 *
	 * @return false|int
	 */
	public function getTypeId()
	{
		if (!$this->hasTypeId()) {
			$this->setTypeId(false);
			
			if (is_array($content = $this->_getData('content'))) {
				$this->setTypeId(isset($content['type']) ? (int)$content['type'] : false);
			}
		}
		
		return $this->_getData('type_id');
	}

	/**
	 * Determine whether the tab is a static block
	 *
	 * @return bool
	 */
	public function isStaticBlock()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_STATIC_BLOCK;
	}
	
	/**
	 * Determine whether the tab is a product attribute
	 *
	 * @return bool
	 */
	public function isProductAttribute()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_PRODUCT_ATTRIBUTE;
	}
	
	/**
	 * Determine whether the tab is a Magento block tag
	 *
	 * @return bool
	 */
	public function isBlockTag()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_BLOCK_TAG;
	}

	/**
	 * Determine whether the tab is a layout block
	 *
	 * @return true
	 */	
	public function isLayoutBlock()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_LAYOUT_BLOCK;
	}
	
	/**
	 * Determine whether the tab is a layout block
	 *
	 * @return true
	 */	
	public function isXml()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_XML;
	}

	/**
	 * Determine whether the tab is a text tab
	 *
	 * @return bool
	 */
	public function isText()
	{
		return $this->getTypeId() === self::CONTENT_TYPE_ID_TEXT;
	}
	
	/**
	 * Retrieve the current product
	 *
	 * @return null|Mage_Catalog_Model_Product
	 */
	public function getProduct()
	{
		if ($this->hasProduct()) {
			return $this->_getData('product');
		}

		return Mage::registry('current_product');
	}
	
	/**
	 * Generate and retrieve the block for the current tab
	 *
	 * @return false|Mage_Core_Block_Template
	 */
	public function getBlock()
	{
		if (!$this->hasBlock()) {
			$this->setBlock(false);

			if (($sourceData = $this->getSourceData()) !== false) {
				$block = false;
				$layout = Mage::getSingleton('core/layout');
				
				if ($this->isStaticBlock()) {
					$block = $layout->createBlock('cms/block')->setBlockId($sourceData->getBlockId());
				}
				else if ($this->isProductAttribute()) {
					$block = $layout->createBlock('smarttabs/tab_product_attribute')
						->setTemplate($sourceData->getTemplate() ? $sourceData->getTemplate() : 'smarttabs/tab/product-attribute.phtml')
						->setAttributeCode($sourceData->getAttributeCode());
				}
				else if ($this->isBlockTag()) {
					$value = trim(Mage::helper('cms')->getBlockTemplateProcessor()->filter($sourceData->getTag()));

					if ($value !== '') {
						$block = $layout->createBlock('core/text')->setText($value);
					}
				}
				else if ($this->isLayoutBlock()) {
					if ($sourceData->getBlockName()) {
						$block = $layout->getBlock($sourceData->getBlockName());
					}
				}
				else if ($this->isXml()) {
					if ($xml = $sourceData->getCode()) {
						$clayout = Mage::getModel('core/layout');
						$clayout->getUpdate()->addUpdate($xml);
						$clayout->generateXml();
						$clayout->generateBlocks();

						if (($xmlHtml = trim($clayout->getOutput())) !== '') {
							$block = $layout->createBlock('core/text')->setText($xmlHtml);
						}
					}
				}
				else if ($this->isText()) {
					$value = trim(Mage::helper('cms')->getBlockTemplateProcessor()->filter($sourceData->getText()));
					
					if ($value !== '') {
						$block = $layout->createBlock('core/text')->setText($value);
					}
				}
				
				if ($block) {
					$this->setBlock(
						$block->setCacheLifetime(null)
							->setTitle($this->getTitle())
							->setProduct($this->getProduct())
					);
				}
			}
		}
		
		return $this->_getData('block');
	}
	
	/**
	 * Retrieve the source data
	 *
	 * @return false|Varien_Object
	 */
	public function getSourceData()
	{
		if (!$this->hasSourceData()) {
			$this->setSourceData(false);

			if (is_array($content = $this->_getData('content'))) {
				if (isset($content['type']) && isset($content['sources'][$content['type']])) {
					$this->setSourceData(new Varien_Object($content['sources'][$content['type']]));
				}
			}
		}
		
		return $this->_getData('source_data');
	}
	
	/**
	 * Retrieve an array of category IDS
	 *
	 * @return array
	 */
	public function getCategoryIds()
	{
		if (is_array(($filters = $this->getFilters()))) {
			if (isset($filters['category']) && isset($filters['category']['ids'])) {
				return (array)$filters['category']['ids'];
			}
		}
		
		return array();
	}
	
	/**
	 * Set the category ID's
	 *
	 * @param array $categoryIds
	 * @return $this
	 */
	public function setCategoryIds(array $categoryIds)
	{
		if (!is_array(($filters = $this->getFilters()))) {
			$filters = array();
		}
		
		$filters['category'] = array('ids' => $categoryIds);
			
		$this->setFilters($filters);
		
		return $this;
	}

	/**
	 * Determine whether the tab can display
	 *
	 * @return bool
	 */
	public function canDisplay()
	{
		if ($this->_getData('can_display')) {
			return $this->_getData('can_display');
		}

		if (!($_product = $this->getProduct())) {
			return true;
		}

		if (!is_array(($filters = $this->getFilters()))) {
			return true;
		}
		
		if (isset($filters['category']['ids'])) {
			$filterIds = (array)$filters['category']['ids'];
			
			if (count($filterIds) > 0) {
				return count(array_intersect((array)$_product->getCategoryIds(), $filterIds)) > 0;
			}
		}
		
		return true;
	}
	
	public function canApplyToProduct(Mage_Catalog_Model_Product $product)
	{
		if ($filters = $this->getFilters()) {
			if (isset($filters['attribute'])) {
				foreach($filters['attribute'] as $attribute => $values) {
					$productValue = $product->_getData($attribute);
					
					if (!is_array($productValue)) {
						$productValue = explode(',', $productValue);
					}
					
					if (!array_intersect($values, $productValue)) {
						return false;
					}
				}
			}
			
			if (isset($filters['price'])) {
				if (isset($filters['price']['price'])) {
					$productPrice = $product->getFinalPrice();
				
					$minPrice = isset($filters['price']['price']['min']) ? (float)$filters['price']['price']['min'] : null;
					$maxPrice = isset($filters['price']['price']['max']) ? (float)$filters['price']['price']['max'] : null;
					
					if (!is_null($minPrice)) {
						if ($productPrice < $minPrice) {
							return false;
						}
					}
					
					if (!is_null($maxPrice)) {
						if ($maxPrice > $productPrice) {
							return false;
						}
					}
				}
			}

			if (isset($filters['price']['is_on_sale'])) {
				$isOnSale = (int)$filters['price']['is_on_sale'];
				
				if ($isOnSale && !$product->getSpecialPrice()) {
					return false;
				}
			}

			if (isset($filters['product'])) {
				foreach($filters['product'] as $attribute => $value) {
					if ($attribute === 'sku') {
						$value = explode(',', $value);
					}

					if ($attributeValue = $product->getData($attribute)) {
						if (!in_array($attributeValue, $value)) {
							return false;
						}
					}
				}
			}
		}
		
		return true;
	}
	
	public function getFilters()
	{
		$filters = $this->_getData('filters');

		if (isset($filters['price']) && isset($filters['price']['is_on_sale']) && $filters['price']['is_on_sale'] === '') {
			unset($filters['price']['is_on_sale']);
			
			if (!$filters['price']) {
				unset($filters['price']);
			}
		}
		
		if (isset($filters['attribute'])) {
			foreach($filters['attribute'] as $attribute => $values) {
				if (is_array($values)) {
					foreach($values as $key => $value) {
						if ($value === '') {
							unset($values[$key]);
						}
					}
					
					if (count($values) === 0) {
						unset($filters['attribute'][$attribute]);
					}
					else {
						$filters['attribute'][$attribute] = $values;
					}
				}
				else if ($values === '') {
					unset($filters['attribute'][$attribute]);
				}	
			}
		}

		if ($filters) {
			if (count($filters) === 1 && isset($filters['product']) && empty($filters['product'])) {
				return false;
			}
		}
		
		return $filters;
	}
	
	/**
	 * Get the HTML for the tab
	 *
	 * @return string
	 */
	public function getHtml()
	{
		if (!$this->hasHtml()) {
			$this->setHtml(
				$this->getBlock() ? trim($this->getBlock()->toHtml()) : ''
			);
		}
		
		return $this->_getData('html');
	}
	
	/**
	 * Determine whether the tab is visible (ie. whether it has HTML content)
	 *
	 * @return bool
	 */
	public function isVisible()
	{
		return $this->getHtml() && $this->getHtml() !== '';
	}
}
