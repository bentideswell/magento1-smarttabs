<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Set the tab block options
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('smarttabs_tab_edit_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Tab Information'));
	}
	
	/**
	 * Add tabs to the tabs block
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$layout = $this->getLayout();
		
		$this->addTab('tab', array(
			'label' => $this->helper('adminhtml')->__('General'),
			'title' => $this->helper('adminhtml')->__('General'),
			'content' => $layout->createBlock('smarttabs/adminhtml_tab_edit_tab_tab')->toHtml(),
		));
		
		$this->addTab('content', array(
			'label' => $this->helper('adminhtml')->__('Content'),
			'title' => $this->helper('adminhtml')->__('Content'),
			'content' => $layout->createBlock('smarttabs/adminhtml_tab_edit_tab_content')->toHtml(),
		));
		
		$this->addTab('filters', array(
			'label' => $this->helper('adminhtml')->__('Attribute Filters'),
			'title' => $this->helper('adminhtml')->__('Attribute Filters'),
			'content' => $layout->createBlock('smarttabs/adminhtml_tab_edit_tab_filters')->toHtml(),
		));

		$this->addTab('categories', array(
			'label' => $this->helper('adminhtml')->__('Category Filters'),
			'title' => $this->helper('adminhtml')->__('Category Filters'),
			'content' => $layout->createBlock('smarttabs/adminhtml_tab_edit_tab_categories')->toHtml(),
		));

		return parent::_beforeToHtml();
	}
}
