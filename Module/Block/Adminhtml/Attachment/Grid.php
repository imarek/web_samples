<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment admin grid block
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Attachment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('attachmentGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Grid
     * @author
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('arcyro_document/attachment')
            ->getCollection();
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare grid collection
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Grid
     * @author
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('arcyro_document')->__('Id'),
                'index'  => 'entity_id',
                'type'   => 'number'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('arcyro_document')->__('title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );
        
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('arcyro_document')->__('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => array(
                    '1' => Mage::helper('arcyro_document')->__('Enabled'),
                    '0' => Mage::helper('arcyro_document')->__('Disabled'),
                )
            )
        );
        $this->addColumn(
            'comment',
            array(
                'header' => Mage::helper('arcyro_document')->__('comment'),
                'index'  => 'comment',
                'type'=> 'text',

            )
        );
        $this->addColumn(
            'enabled',
            array(
                'header' => Mage::helper('arcyro_document')->__('enabled'),
                'index'  => 'enabled',
                'type'    => 'options',
                    'options'    => array(
                    '1' => Mage::helper('arcyro_document')->__('Yes'),
                    '0' => Mage::helper('arcyro_document')->__('No'),
                )

            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('arcyro_document')->__('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('arcyro_document')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'  =>  Mage::helper('arcyro_document')->__('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('arcyro_document')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit'),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'is_system' => true,
                'sortable'  => false,
            )
        );
        $this->addExportType('*/*/exportCsv', Mage::helper('arcyro_document')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('arcyro_document')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('arcyro_document')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * prepare mass action
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Grid
     * @author
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('attachment');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'=> Mage::helper('arcyro_document')->__('Delete'),
                'url'  => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('arcyro_document')->__('Are you sure?')
            )
        );
        $this->getMassactionBlock()->addItem(
            'status',
            array(
                'label'      => Mage::helper('arcyro_document')->__('Change status'),
                'url'        => $this->getUrl('*/*/massStatus', array('_current'=>true)),
                'additional' => array(
                    'status' => array(
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('arcyro_document')->__('Status'),
                        'values' => array(
                            '1' => Mage::helper('arcyro_document')->__('Enabled'),
                            '0' => Mage::helper('arcyro_document')->__('Disabled'),
                        )
                    )
                )
            )
        );
        $this->getMassactionBlock()->addItem(
            'enabled',
            array(
                'label'      => Mage::helper('arcyro_document')->__('Change enabled'),
                'url'        => $this->getUrl('*/*/massEnabled', array('_current'=>true)),
                'additional' => array(
                    'flag_enabled' => array(
                        'name'   => 'flag_enabled',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => Mage::helper('arcyro_document')->__('enabled'),
                        'values' => array(
                                '1' => Mage::helper('arcyro_document')->__('Yes'),
                                '0' => Mage::helper('arcyro_document')->__('No'),
                            )

                    )
                )
            )
        );
        return $this;
    }

    /**
     * get the row url
     *
     * @access public
     * @param Arcyro_Document_Model_Attachment
     * @return string
     * @author
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Grid
     * @author
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}
