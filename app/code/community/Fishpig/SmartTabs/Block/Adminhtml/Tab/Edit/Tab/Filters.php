<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit_Tab_Filters extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$this->setForm(new Varien_Data_Form());

        $this->getForm()->setHtmlIdPrefix('smarttabs_')
        	->setFieldNameSuffix('smarttabs');

		$this->_addProductFilters();
		$this->_addPriceFilters();		
		$this->_addAttributeFilters();

        $formData = $this->_getFormData();

        foreach (['smarttabs_filter_product', 'smarttabs_filters', 'smarttabs_filter_price'] as $fieldsetId) {
            $fieldset = $this->getForm()->getElement($fieldsetId);
            
            foreach ($fieldset->getElements() as $element) {
                if (isset($formData[$element->getId()])) {
                    $element->addClass('has-selected-value');
                }
            }
        }


		$this->getForm()->setValues($formData);

		return parent::_prepareForm();
	}

	protected function _addProductFilters()
	{
		$fieldset = $this->getForm()->addFieldset('smarttabs_filter_product', array(
			'legend'=> Mage::helper('adminhtml')->__('Product Filters'),
		));

		$fieldset->addField('filters_product_sku', 'text', array(
			'name' => 'filters[product][sku]',
			'title' => $this->helper('adminhtml')->__('SKU'),
			'label' => $this->helper('adminhtml')->__('SKU'),
			'note' => 'Comma separate SKU\'s for multiple SKU\'s',
		));
		
		$fieldset->addField('filters_product_type_id', 'multiselect', array(
			'name' => 'filters[product][type_id]',
			'title' => $this->helper('adminhtml')->__('Type ID'),
			'label' => $this->helper('adminhtml')->__('Type ID'),
			'values' => Mage::getSingleton('catalog/product_type')->getOptions(),
			'note' => 'Leave empty for all',
		));
		
		$fieldset->addField('filters_product_tax_class_id', 'multiselect', array(
			'name' => 'filters[product][tax_class_id]',
			'title' => $this->helper('adminhtml')->__('Tax Class ID'),
			'label' => $this->helper('adminhtml')->__('Tax Class ID'),
			'values' => Mage::getSingleton('tax/class_source_product')->getAllOptions(false),
			'note' => 'Leave empty for all',
		));

		$productAttributeSets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(
				Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId()
			)
			->load();
		
		$attributeSetOptions = array();
			
		foreach($productAttributeSets as $productAttributeSet){
			$attributeSetOptions[] = array(
				'value' => $productAttributeSet->getId(),
				'label' => $productAttributeSet->getAttributeSetName(),
			);
		}
		
		$fieldset->addField('filters_product_attribute_set_id', 'multiselect', array(
			'name' => 'filters[product][attribute_set_id]',
			'title' => $this->helper('adminhtml')->__('Attribute Set'),
			'label' => $this->helper('adminhtml')->__('Attribute Set'),
			'values' => $attributeSetOptions,
			'note' => 'Leave empty for all',
		));
	}
	
	/**
	 * Add the Attribute Filters to the fieldset
	 *
	 * @param $form
	 * @return $this
	 */
	protected function _addAttributeFilters()
	{
		$attributes = $this->_getAttributes();

		$fieldset = $this->getForm()->addFieldset('smarttabs_filters', array(
			'legend'=> Mage::helper('adminhtml')->__('Attribute Filters'),
		));
					
		foreach($attributes as $attribute) {
			if (!in_array($attribute->getSourceModel(), array('eav/entity_attribute_source_table', 'eav/entity_attribute_source_boolean', ''))) {
				continue;
			}
			try {
				$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute->getAttributeCode());
	
				if ($attribute->usesSource()) {				
					$fieldType = $attribute->getFrontendInput() === 'boolean' ? 'multiselect' : 'multiselect'; # Purposely both multiselect
					$options = $attribute->getSource()->getAllOptions(false, true);
					
					if ($fieldType === 'select') {
						array_unshift($options, array('value' => '', 'label' => ''));
					}
					
					$fieldset->addField('filters_attribute_' . $attribute->getAttributeCode(), $fieldType, array(
						'name' => 'filters[attribute][' . $attribute->getAttributeCode() . ']',
						'title' => $this->helper('adminhtml')->__($attribute->getFrontendLabel()),
						'label' => $this->helper('adminhtml')->__($attribute->getFrontendLabel()),
						'values' => $options,
					));
				}
			}
			catch (Exception $e) {
				Mage::logException($e);
			}
		}
		
		return $this;
	}
	
	protected function _addPriceFilters()
	{
		$fieldset = $this->getForm()->addFieldset('smarttabs_filter_price', array(
			'legend'=> Mage::helper('adminhtml')->__('Price Filters'),
		));

		$attributeOptions = array('price' => 'Price');

		foreach($attributeOptions as $attributeCode => $attributeLabel) {
			foreach(array('min' => 'Minimum', 'max' => 'Maximum') as $key => $label) {
				$fieldset->addField('filters_price_' . $attributeCode . '_' . $key, 'text', array(
					'name' => 'filters[price][' . $attributeCode . '][' . $key . ']',
					'title' => $this->helper('adminhtml')->__($label . ' ' . $attributeLabel),
					'label' => $this->helper('adminhtml')->__($label . ' ' . $attributeLabel),
				));
			}
		}
        
        $isOnSaleOptions = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
        
        array_unshift($isOnSaleOptions, ['value' => '', 'label' => '-- Not Applied --']);

		$fieldset->addField('filters_price_is_on_sale', 'select', array(
			'name' => 'filters[price][is_on_sale]',
			'title' => $this->helper('adminhtml')->__('Is On Sale'),
			'label' => $this->helper('adminhtml')->__('Is On Sale'),
			'required' => false,
			'values' =>$isOnSaleOptions,
		));


		return $this;
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
    	return ($page = Mage::registry('smarttabs_tab')) !== null 
            ? ($page->getAdminData())
            : [];
	}
	
	/**
	 * Retrieve all attributes that can be used as option
	 * filters for splash pages
	 *
	 * @return
	 */
	protected function _getAttributes()
	{
		$productEntityTypeId = Mage::getResourceModel('catalog/product')->getTypeId();
		
		$collection = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($productEntityTypeId)
			->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect', 'boolean')))
			->addFieldToFilter('attribute_code', array('nin' => array('gift_message_available', 'is_recurring', 'enable_googlecheckout')))
			->load();
		
		return $collection;
	}
	
	/**
     *
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $html .= '<style>.has-selected-value{border:4px solid #2eb72e}</style>';
        
        
        $formData = $this->_getFormData();

        if (!isset($formData['filters_price_is_on_sale'])) {
            if (preg_match('/<select[^>]+id="smarttabs_filters_price_is_on_sale"[^>]*>.*<\/select>/Uis', $html, $matches)) {
                $selectHtml = $matches[0];

                if (strpos($selectHtml, 'value="" selected') !== false) {
                    $selectHtml = str_replace('value="0" selected="selected"', 'value="0"', $selectHtml);
                    
                    $html = str_replace($matches[0], $selectHtml, $html);
                }
            }
        }

        return $html;
    }
}
