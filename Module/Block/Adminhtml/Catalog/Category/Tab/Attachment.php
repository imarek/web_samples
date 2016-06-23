<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment tab on category edit form
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Catalog_Category_Tab_Attachment extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setId('catalog_category_attachment');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_attachments'=>1));
        }
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
     * prepare the collection
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Catalog_Category_Tab_Attachment
     * @author
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('arcyro_document/attachment_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('arcyro_document/attachment_category')),
            'related.attachment_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Catalog_Category_Tab_Attachment
     * @author
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_attachments',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_attachments',
                'values' => $this->_getSelectedAttachments(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('arcyro_document')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'title',
            array(
                'header' => Mage::helper('arcyro_document')->__('title'),
                'align'  => 'left',
                'index'  => 'title',
                'renderer' => 'arcyro_document/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/document_attachment/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('arcyro_document')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected attachments
     *
     * @access protected
     * @return array
     * @author
     */
    protected function _getSelectedAttachments()
    {
        $attachments = $this->getCategoryAttachments();
        if (!is_array($attachments)) {
            $attachments = array_keys($this->getSelectedAttachments());
        }
        return $attachments;
    }

    /**
     * Retrieve selected attachments
     *
     * @access protected
     * @return array
     * @author
     */
    public function getSelectedAttachments()
    {
        $attachments = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('arcyro_document/category')->getSelectedAttachments(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $attachment) {
            $attachments[$attachment->getId()] = array('position' => $attachment->getPosition());
        }
        return $attachments;
    }

    /**
     * get row url
     *
     * @access public
     * @param Arcyro_Document_Model_Attachment
     * @return string
     * @author
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/document_attachment_catalog_category/attachmentsgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Arcyro_Document_Block_Adminhtml_Catalog_Category_Tab_Attachment
     * @author
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_attachments') {
            $attachmentIds = $this->_getSelectedAttachments();
            if (empty($attachmentIds)) {
                $attachmentIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$attachmentIds));
            } else {
                if ($attachmentIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$attachmentIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
