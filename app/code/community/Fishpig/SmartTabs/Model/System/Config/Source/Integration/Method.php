<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_System_Config_Source_Integration_Method extends Varien_Object
{
	const METHOD_DEFAULT = 'info_tabs';
	const METHOD_DETAILED_INFO = 'detailed_info';
	
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
					'value' => Fishpig_SmartTabs_Model_System_Config_Source_Integration_Method::METHOD_DEFAULT,
					'label' => Mage::helper('adminhtml')->__('Default (info_tabs)')
				),
				array(
					'value' => Fishpig_SmartTabs_Model_System_Config_Source_Integration_Method::METHOD_DETAILED_INFO,
					'label' => Mage::helper('adminhtml')->__('Child Group (detailed_info)')
				),
			);
		}
		
		return $this->_options;
	}
}