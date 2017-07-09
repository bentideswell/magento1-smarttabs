<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_System_Config_Source_Content_Type extends Varien_Object
{
	/**
	 * Cache for the options array
	 *
	 * @var null|array
	 */
	protected $_options = null;
	
	/**
	 * Retrieve the option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		if (is_null($this->_options)) {
			$this->_options = array(
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_LAYOUT_BLOCK,
					'label' => Mage::helper('adminhtml')->__('Layout Block')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_BLOCK_TAG,
					'label' => Mage::helper('adminhtml')->__('Magento Block Tag')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_PRODUCT_ATTRIBUTE,
					'label' => Mage::helper('adminhtml')->__('Product Attribute')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_STATIC_BLOCK,
					'label' => Mage::helper('adminhtml')->__('Static Block')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_XML,
					'label' => Mage::helper('review')->__('XML')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_TEXT,
					'label' => Mage::helper('review')->__('Text')
				),
			);
		}
		
		return $this->_options;
	}
}
