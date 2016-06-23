<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment admin edit tabs
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Attachment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('attachment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('arcyro_document')->__('Attachment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Edit_Tabs
     * @author
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_attachment',
            array(
                'label'   => Mage::helper('arcyro_document')->__('Attachment'),
                'title'   => Mage::helper('arcyro_document')->__('Attachment'),
                'content' => $this->getLayout()->createBlock(
                    'arcyro_document/adminhtml_attachment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'categories',
            array(
                'label' => Mage::helper('arcyro_document')->__('Associated categories'),
                'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
                'class' => 'ajax'
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve attachment entity
     *
     * @access public
     * @return Arcyro_Document_Model_Attachment
     * @author
     */
    public function getAttachment()
    {
        return Mage::registry('current_attachment');
    }
}
