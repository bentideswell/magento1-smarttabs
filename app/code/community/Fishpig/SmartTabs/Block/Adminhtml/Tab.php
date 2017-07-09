<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Set the block options
	 *
	 * @return void
	 */
	public function __construct()
	{	
		parent::__construct();

		$this->_controller = 'adminhtml_tab';
		$this->_blockGroup = 'smarttabs';
		$this->_headerText = $this->__('SmartTabs:') . ' ' . $this->__('Manage Tabs');
		$this->_addButtonLabel = $this->__('Add New Tab');
	}
}