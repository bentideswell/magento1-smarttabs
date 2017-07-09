<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */
 
class Fishpig_SmartTabs_Adminhtml_SmartTabsController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Determine ACL permissions
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('catalog/smarttabs');
	}
	
	/**
	 * Display the list of all splash pages
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_title('Smart Tabs by FishPig');
		$this->renderLayout();
	}
	
	/**
	 * Allow the user to enter a new splash page
	 * This is just a wrapper for the edit action
	 *
	 * @return void
	 */
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	/**
	 * Edit an existing splash page
	 *
	 * @return void
	 */
	public function editAction()
	{
		$titles = array(
			$this->_title('Smart Tabs'),
		);

		if (($tab = $this->_getTab()) !== false) {
			$titles[] = $this->_title($tab->getTitle());
		}
		else {
			$titles[] = Mage::helper('cms')->__('New Tab');
		}

		$this->loadLayout();
		
		foreach($titles as $title) {
			$this->_title($title);
		}
			
		$this->renderLayout();
	}
	
	public function xmlPresetDefaultsAction()
	{
        $this->getResponse()
        	->setHeader('Content-Type', 'application/json')
        	->setBody(
				json_encode(
					Mage::getSingleton('smarttabs/system_config_source_content_type_xml_presets')->getAllDefaults()
				)
	        );
	}

	public function categoriesJsonAction()
	{
        if (($tab = $this->_getTab()) !== false) {
	        $this->getResponse()->setBody(
	            $this->getLayout()->createBlock('smarttabs/adminhtml_tab_edit_tab_categories')
	                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
	        );
	    }
	}

	/**
	 * Save a splash page
	 *
	 * @return void
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('smarttabs')) {
			$tab = Mage::getModel('smarttabs/tab')
				->setData($data)
				->setId($this->getRequest()->getParam('id', null));

			try {
				if (($categoryIds = trim($this->getRequest()->getPost('category_ids'), ',')) !== '') {
					$tab->setCategoryIds(explode(',', $categoryIds));
				}
				
				$tab->save();

				$this->_getSession()->addSuccess(Mage::helper('cms')->__('The tab has been saved.'));
			}
			catch (Exception $e) {
				echo sprintf('<h1>%s</h1><pre>%s</pre>', $e->getMessage(), $e->getTraceAsString());
				exit;
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($tab->getId() && $this->getRequest()->getParam('back', false)) {
				$this->_redirect('*/*/edit', array('id' => $tab->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}

		$this->_redirect('*/*');
	}

	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			$object = Mage::getModel('smarttabs/tab')->load($id);
			
			if ($object->getId()) {
				try {
					$object->delete();
					$this->_getSession()->addSuccess($this->__('The tab was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/*');
	}

	
	/**
	 * Retrieve the current page
	 *
	 * @return false|Fishpig_AttributeSplashPro_Model_Page
	 */
	protected function _getTab()
	{
		if (($page = Mage::registry('smarttabs_tab')) !== null) {
			return $page;
		}

		$tab = Mage::getModel('smarttabs/tab')->load($this->getRequest()->getParam('id', 0));

		if ($tab->getId()) {
			Mage::register('smarttabs_tab', $tab);
			return $tab;
		}
		
		return false;
	}
}