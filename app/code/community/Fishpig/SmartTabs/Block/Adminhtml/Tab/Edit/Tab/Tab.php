<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit_Tab_Tab extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('smarttabs_');
        $form->setFieldNameSuffix('smarttabs');
        
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('smarttabs_tab', array(
			'legend'=> $this->helper('adminhtml')->__('Tab Information'),
		));
		
		$fieldset->addField('title', 'text', array(
			'name' => 'title',
			'label' => $this->helper('adminhtml')->__('Title'),
			'title' => $this->helper('adminhtml')->__('Title'),
			'required' => true,
			'class' => 'required-entry',
		));
		
		$fieldset->addField('alias', 'text', array(
			'name' => 'alias',
			'label' => $this->helper('adminhtml')->__('Alias'),
			'title' => $this->helper('adminhtml')->__('Alias'),
			'required' => true,
			'class' => 'required-entry validate-identifier',
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$field = $fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => Mage::helper('cms')->__('Store View'),
				'title' => Mage::helper('cms')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));

			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			$field->setRenderer($renderer);
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
				'name'      => 'stores[]',
				'value'     => Mage::app()->getStore(true)->getId()
			));
			
			if (($tab = Mage::registry('smarttabs_tab')) !== null) {
				$tab->setStoreId(Mage::app()->getStore(true)->getId());
			}
		}

		$fieldset->addField('status', 'select', array(
			'name' => 'status',
			'title' => $this->helper('adminhtml')->__('Status'),
			'label' => $this->helper('adminhtml')->__('Status'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('sort_order', 'text', array(
			'name' => 'sort_order',
			'label' => $this->helper('adminhtml')->__('Sort Order'),
			'title' => $this->helper('adminhtml')->__('Sort Order'),
			'required' => false,
		));
		
		$form->setValues($this->_getFormData());

		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
		if (($tab = Mage::registry('smarttabs_tab')) !== null) {
			return $tab->getData();
		}

		return array('status' => 1);
	}
}
