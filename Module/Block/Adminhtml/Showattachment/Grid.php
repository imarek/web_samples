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
class Arcyro_Document_Block_Adminhtml_Showattachment_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
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

        $collection = Mage::getResourceModel('arcyro_document/attachment_collection');
        if ($this->getCategory()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=2';
        }
        
        $collection->getSelect()->join(
            array('related' => $collection->getTable('arcyro_document/attachment_category')),
            'related.attachment_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
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
                'header'    => Mage::helper('arcyro_document')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
            )
        );

        $this->addColumn(
            'comment',
            array(
                'header' => Mage::helper('arcyro_document')->__('Comment'),
                'index'  => 'comment',
                'type'=> 'text',

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

        $this->addColumn('action',
            array(
                'header' => Mage::helper('arcyro_document')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('arcyro_document')->__('Download'),
                        'url' => array('base'=> '*/*/downloadAttachment'),
                        'field' => 'id'
                    )),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }



    /**
     * get the row url
     * @access public
     * @param Arcyro_Document_Model_Attachment
     * @return string
     * @author
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/downloadAttachment', array('id' => $row->getId()));
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
