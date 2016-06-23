<?php
/**
 * Arcyro_Document extension
 */
/**
 * Adminhtml observer
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the attachment tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Arcyro_Document_Model_Adminhtml_Observer
     * @author
     */
    public function addCategoryAttachmentBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'arcyro_document/adminhtml_catalog_category_tab_attachment',
            'category.attachment.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.attachment.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.attachment.grid',
            'getSelectedAttachments',
            'attachments',
            'category_attachments'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'attachment',
            array(
                'label' => Mage::helper('arcyro_document')->__('Attachment'),
                'content' => $content,
            )
        );

        $tabs->addTab('category_roles', array(
            'label' => Mage::helper('catalog')->__('Roles'),
            'content' => $tabs->getLayout()->createBlock('arcyro_document/adminhtml_catalog_category_roles')->toHtml()
        ));
        return $this;
    }

    /**
     * save attachment - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Arcyro_Document_Model_Adminhtml_Observer
     * @author
     */
    public function saveCategoryAttachmentData($observer)
    {

        $post = Mage::app()->getRequest()->getPost('attachments', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);

            $category = Mage::registry('category');
            $attachmentCategory = Mage::getResourceSingleton('arcyro_document/attachment_category')
                ->saveCategoryRelation($category, $post);

        }
        $roles = Mage::app()->getRequest()->getPost('roles', -1);
        //saving roles to category
        if ($roles != '-1') {
            Mage::getResourceSingleton('arcyro_document/attachment_category')->saveCategoryRoles($category, $roles);
            return $this;
        }


    }
}