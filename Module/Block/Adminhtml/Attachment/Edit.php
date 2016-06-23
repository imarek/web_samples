<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment admin edit form
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Attachment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'arcyro_document';
        $this->_controller = 'adminhtml_attachment';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('arcyro_document')->__('Save Attachment')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('arcyro_document')->__('Delete Attachment')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('arcyro_document')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_attachment') && Mage::registry('current_attachment')->getId()) {
            return Mage::helper('arcyro_document')->__(
                "Edit Attachment '%s'",
                $this->escapeHtml(Mage::registry('current_attachment')->getTitle())
            );
        } else {
            return Mage::helper('arcyro_document')->__('Add Attachment');
        }
    }
}
