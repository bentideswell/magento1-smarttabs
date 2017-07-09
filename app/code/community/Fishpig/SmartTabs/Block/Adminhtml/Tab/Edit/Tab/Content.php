<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Block_Adminhtml_Tab_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('smarttabs_')
        	->setFieldNameSuffix('smarttabs');
        
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('smarttabs_tab_content', array(
			'legend'=> $this->helper('adminhtml')->__('Content'),
			'class' => 'fieldset-wide',
		));

		$fieldset->addField('content_type', 'select', array(
			'name' => 'content[type]',
			'title' => $this->helper('adminhtml')->__('Source'),
			'label' => $this->helper('adminhtml')->__('Source'),
			'values' => $this->_getContentTypes(),
		));
		
		$fieldset = $form->addFieldset('smarttabs_tab_content_static_block', array(
			'legend'=> $this->helper('adminhtml')->__('Static Block'),
			'class' => 'fieldset-wide',
		));
		
		$staticBlockTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_STATIC_BLOCK;
		
		$fieldset->addField('content_' . $staticBlockTypeId . '_block_id', 'select', array(
			'name' => 'content[sources][' . $staticBlockTypeId . '][block_id]',
			'title' => $this->helper('adminhtml')->__('Static Block'),
			'label' => $this->helper('adminhtml')->__('Static Block'),
			'values' => Mage::getResourceModel('cms/block_collection')->toOptionArray(),
		));
		
		$productAttributeTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_PRODUCT_ATTRIBUTE;
		 
		$fieldset = $form->addFieldset('smarttabs_tab_content_product_attribute', array(
			'legend'=> $this->helper('adminhtml')->__('Product Attribute'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('content_' . $productAttributeTypeId . '_attribute_code', 'select', array(
			'name' => 'content[sources][' . $productAttributeTypeId . '][attribute_code]',
			'title' => $this->helper('adminhtml')->__('Attribute'),
			'label' => $this->helper('adminhtml')->__('Attribute'),
			'values' => $this->_getProductAttributes(),
		));
		
		$fieldset->addField('content_' . $productAttributeTypeId . '_template', 'text', array(
			'name' => 'content[sources][' . $productAttributeTypeId . '][template]',
			'title' => $this->helper('adminhtml')->__('Template'),
			'label' => $this->helper('adminhtml')->__('Template'),
			'note' => Mage::helper('adminhtml')->__('Leave empty to use the default template (app/design/frontend/base/default/template/smarttabs/tab/product-attribute.phtml)'),
		));

		$blockTagAttributeTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_BLOCK_TAG;
		 
		$fieldset = $form->addFieldset('smarttabs_tab_content_block_tag', array(
			'legend'=> $this->helper('adminhtml')->__('Magento Block Tag'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('content_' . $blockTagAttributeTypeId . '_tag', 'editor', array(
			'name' => 'content[sources][' . $blockTagAttributeTypeId . '][tag]',
			'title' => $this->helper('adminhtml')->__('Tag'),
			'label' => $this->helper('adminhtml')->__('Tag'),
			'note' => Mage::helper('adminhtml')->__('Enter a valid Magento block. EG. {{block type="core/template" name="custom.tab" template="your/custom/template.phtml"}}'),
		));
		
		$layoutBlockAttributeTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_LAYOUT_BLOCK;
		 
		$fieldset = $form->addFieldset('smarttabs_tab_content_layout_block', array(
			'legend'=> $this->helper('adminhtml')->__('XML Layout Block'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('content_' . $layoutBlockAttributeTypeId . '_block_name', 'text', array(
			'name' => 'content[sources][' . $layoutBlockAttributeTypeId . '][block_name]',
			'title' => $this->helper('adminhtml')->__('Block Name'),
			'label' => $this->helper('adminhtml')->__('Block Name'),
			'note' => Mage::helper('adminhtml')->__('Enter the name of a block declared via an XML layout file.'),
		));

		$xmlAttributeTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_XML;
		 
		$fieldset = $form->addFieldset('smarttabs_tab_content_product_reviews', array(
			'legend'=> $this->helper('review')->__('XML'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('content_' . $xmlAttributeTypeId . '_preset', 'select', array(
			'name' => 'content[sources][' . $xmlAttributeTypeId . '][preset]',
			'title' => $this->helper('adminhtml')->__('Presets'),
			'label' => $this->helper('adminhtml')->__('Presets'),
			'values' => Mage::getSingleton('smarttabs/system_config_source_content_type_xml_presets')->toOptionArray(true),
		));
		
		$fieldset->addField('content_' . $xmlAttributeTypeId . '_code', 'editor', array(
			'name' => 'content[sources][' . $xmlAttributeTypeId . '][code]',
			'title' => $this->helper('adminhtml')->__('XML'),
			'label' => $this->helper('adminhtml')->__('XML'),
		));
		
		$textTypeId = Fishpig_SmartTabs_Model_Tab::CONTENT_TYPE_ID_TEXT;
		 
		$fieldset = $form->addFieldset('smarttabs_tab_content_text', array(
			'legend'=> $this->helper('adminhtml')->__('Text'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('content_' . $textTypeId . '_text', 'editor', array(
			'name' => 'content[sources][' . $textTypeId . '][text]',
			'title' => $this->helper('adminhtml')->__('Text'),
			'label' => $this->helper('adminhtml')->__('Text'),
			'style' => 'width:100%; height:400px;',
			'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
				'add_widgets' => true,
				'add_variables' => true,
				'add_image' => true,
				'files_browser_window_url' => $this->getUrl('adminhtml/cms_wysiwyg_images/index')
			)),
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
		return ($page = Mage::registry('smarttabs_tab')) !== null ? $page->getAdminData() : array();
	}
	
	/**
	 * Retrieve an array of different tab content sources
	 *
	 * @return array
	 */
	protected function _getContentTypes()
	{
		return Mage::getSingleton('smarttabs/system_config_source_content_type')->toOptionArray();
	}
	
	/**
	 * Get a an array of product attributes that can be used as a tab source
	 *
	 * @return array
	 */
	protected function _getProductAttributes()
	{
		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter(Mage::getResourceSingleton('catalog/product')->getTypeId())
			->addFieldToFilter('backend_type', 'text')
			->setOrder('frontend_label', 'ASC')
			->load();

		$options = array();
		
		foreach($attributes as $attribute) {
			if ($attribute->getBackendModel()) {
				continue;
			}

			$options[] = array(
				'value' => $attribute->getAttributeCode(),
				'label' => $attribute->getFrontendLabel(),
			);
		}
		
		return $options;
	}
}
