<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment edit form tab
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Attachment_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Arcyro_Document_Block_Adminhtml_Attachment_Edit_Tab_Form
     * @author
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('attachment_');
        $form->setFieldNameSuffix('attachment');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'attachment_form',
            array('legend' => Mage::helper('arcyro_document')->__('Attachment'))
        );
        $fieldset->addType(
            'file',
            Mage::getConfig()->getBlockClassName('arcyro_document/adminhtml_attachment_helper_file')
        );

        $fieldset->addField(
            'title',
            'text',
            array(
                'label' => Mage::helper('arcyro_document')->__('Title'),
                'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'comment',
            'textarea',
            array(
                'label' => Mage::helper('arcyro_document')->__('Comment'),
                'name'  => 'comment',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'uploaded_file',
            'file',
            array(
                'label' => Mage::helper('arcyro_document')->__('Uploaded_file'),
                'name'  => 'uploaded_file',

           )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('arcyro_document')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('arcyro_document')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('arcyro_document')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_attachment')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getAttachmentData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getAttachmentData());
            Mage::getSingleton('adminhtml/session')->setAttachmentData(null);
        } elseif (Mage::registry('current_attachment')) {
            $formValues = array_merge($formValues, Mage::registry('current_attachment')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
