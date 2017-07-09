<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
	/**
	 * Retrieve currently edited splash page
	 *
	 * @return Fishpig_AttributeSplash_Model_Page
	*/
	public function getProduct()
	{
		return $this->getSmartTab();
	}

	/**
	 * Retrieve currently edited splash page
	 *
	 * @return Fishpig_AttributeSplash_Model_Page
	*/	
	public function getSmartTab()
	{
		return Mage::registry('smarttabs_tab');
	}
	
	/**
	 * Checks when this block is readonly
	 *
	 * @return boolean
	*/
	public function isReadonly()
	{
		return false;
	}
	
	/**
	 * Return an empty array if tab doesn't exist
	 *
	 * @return array
	 */
	public function getCategoryIds()
	{
		if (!$this->getProduct()) {
			return array();
		}
		
		return parent::getCategoryIds();
	}
}