<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 03.08.15
 * Time: 14:01
 */


class Arcyro_Document_Block_Adminhtml_Catalog_Category_Roles extends Mage_Adminhtml_Block_Widget_Form
{


    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'attachment_form',
            array('legend' => Mage::helper('arcyro_document')->__('Roles'))
        );

        $fieldset->addField(
            'roles',
            'multiselect',
            array(
                'label' => Mage::helper('adminhtml')->__('Roles'),
                'name'  => 'roles[]',
                'values'   => Mage::helper('arcyro_document')->getRolesOptionValues(),
                'value' => Mage::helper('arcyro_document')->getSelectedCategoryRoles(Mage::registry('current_category')),
                'disabled' => false,
                'index' => 'roles',

            )
        );

        return parent::_prepareForm();
    }

}
