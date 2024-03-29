<?php
/**
 * @category    Fishpig
 * @package    Fishpig_SmartTabs
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */
 
class Fishpig_SmartTabs_Block_Adminhtml_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Set the grid block options
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('smarttabs_tab_grid');
		$this->setSaveParametersInSession(false);
	}
	
	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('smarttabs/tab_collection');

		$this->setCollection($collection);
	
		return parent::_prepareCollection();
	}
	
	/**
	 * Add store information to pages
	 *
	 * @return $this
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');

		parent::_afterLoadCollection();
	}
	
	/**
	 * Apply the store filter
	 *
	 * @param $collection
	 * @param $column
	 * @return void
	 */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('tab_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'tab_id',
		));
		
		$this->addColumn('title', array(
			'header'	=> $this->__('Title'),
			'align'		=> 'left',
			'index'		=> 'title',
		));
		
		$this->addColumn('alias', array(
			'header'	=> $this->__('Alias'),
			'align'		=> 'left',
			'index'		=> 'alias',
		));
		
		$this->addColumn('description', array(
			'header'	=> $this->__('Description'),
			'align'		=> 'left',
			'index'		=> 'description',
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'	=> $this->__('Store'),
				'align'		=> 'left',
				'index'		=> 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
				'options' 	=> $this->getStores(),
			));
		}

		$this->addColumn('updated_at', array(
			'header' => Mage::helper('cms')->__('Last Modified'),
			'index' => 'updated_at',
			'type' => 'datetime',
		));
		
		$this->addColumn('status', array(
			'header'	=> $this->__('Enabled'),
			'width'		=> '90px',
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> array(
				Fishpig_SmartTabs_Model_Tab::STATUS_ENABLED => $this->__('Enabled'),
				Fishpig_SmartTabs_Model_Tab::STATUS_DISABLED => $this->__('Disabled'),
			),
		));
	
		$this->addColumn('action',
			array(
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Edit'),
						'url'     => array(
						'base'=>'*/*/edit',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));

		return parent::_prepareColumns();
	}

	/**
	 * Prepare the massaction block for deleting multiple pages
	 *
	 * @return $this
	 */
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('tab_id');
		$this->getMassactionBlock()->setFormFieldName('tab');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
		
		return $this;
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function getStores()
	{
		$options = array(0 => $this->__('Global'));
		$stores = Mage::getResourceModel('core/store_collection')->load();
		
		foreach($stores as $store) {
			$options[$store->getId()] = $store->getWebsite()->getName() . ' &gt; ' . $store->getName();
		}

		return $options;
	}
}
