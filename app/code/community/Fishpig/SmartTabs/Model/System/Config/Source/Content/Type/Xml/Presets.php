<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_SmartTabs_Model_System_Config_Source_Content_Type_Xml_Presets extends Varien_Object
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
	public function toOptionArray($includeEmpty = false)
	{
		$options = array();
		
		if ($includeEmpty) {
			$options = array(array(
				'value' => '',
				'label' => Mage::helper('adminhtml')->__('-- Please Select --'),
			));
		}

		foreach($this->_getOptions() as $value => $data) {
			$options[] = array(
				'value' => $value,
				'label' => $data['label'],
			);
		}
		
		return $options;
	}
	
	/**
	 * Get all options
	 *
	 * @return array
	 */
	protected function _getOptions()
	{
		if (is_null($this->_options)) {
			$this->_options = array(
				'catalog_product_reviews' => array(
					'label' => Mage::helper('adminhtml')->__('Product Reviews'),
					'default' => '<block type="review/product_view_list" name="smartTabs.reviews" template="review/product/view/list.phtml" output="toHtml">
	<block type="review/form" name="review_form" />
</block>',
				),
				'wordpress_related_posts' => array(
					'label' => Mage::helper('adminhtml')->__('WordPress Related Posts'),
					'default' => '<block type="wordpress/post_associated" name="wordpress.posts.related" template="wordpress/post/associated.phtml" output="toHtml">
	<action method="setTitle" translate="title" module="wordpress"><title><![CDATA[Related Blog Posts]]></title></action>
	<action method="setEntity"><type>product</type></action>
	<!--<action method="setCount"><count>5</count></action>-->
</block>', 
				),
			);
		}
		
		return $this->_options;
	}

	public function getAllDefaults()
	{
		$defaults = array();
		
		foreach($this->_getOptions() as $type => $data) {
			$defaults[$type] = $data['default'];
		}
		
		return $defaults;
	}
}	
