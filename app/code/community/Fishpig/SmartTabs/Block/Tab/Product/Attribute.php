<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Tab_Product_Attribute extends Mage_Core_Block_Template
{
	/**
	 * Generate the attribute value before the block is rendered
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		if ($this->getAttributeCode() && $this->getProduct()) {
			$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getAttributeCode());
			
			$this->setAttributeModel($attribute);
			
			if (!$attribute->getSourceModel()) {
				$this->setAttributeValue($this->getProduct()->getData($this->getAttributeCode()));
			}
			else {
				$this->setAttributeValue($this->getProduct()->getAttributeText($this->getAttributeCode()));
			}
		}

		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the current product model
	 *
	 * @return false|Mage_Catalog_Model_Product
	 */
	public function getProduct()
	{
		if (!$this->hasProduct()) {
			$this->setProduct(Mage::registry('current_product'));
		}
		
		return $this->_getData('product');
	}
}