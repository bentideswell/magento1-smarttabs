<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit  extends Mage_Adminhtml_Block_Widget_Form_Container
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
		$this->_headerText = $this->_getHeaderText();
		
		$this->_addButton('save_and_edit_button', array(
			'label' => Mage::helper('catalog')->__('Save and Continue Edit'),
			'onclick' => 'editForm.submit(\''. $this->getUrl('*/*/save', array('_current' => true, 'back' => 'edit')) .'\')',
			'class' => 'save',
		));
		
		$this->_removeButton('reset');
	}
    
	/**
	 * Enable WYSIWYG editor
	 *
	 */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        
        return $this;
    }
    
    /**
     * Retrieve the header text
     *
     * @return string
     */
	protected function _getHeaderText()
	{
		if (($tab = Mage::registry('smarttabs_tab')) !== null) {
			return $this->__("Edit Tab '%s'", $tab->getTitle());
		}
	
		return $this->__('New Tab');
	}
}
