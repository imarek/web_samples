<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment - category controller
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Arcyro_Document_Adminhtml_Document_Attachment_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Arcyro_Document');
    }

    /**
     * attachments grid in the catalog page
     *
     * @access public
     * @return void
     * @author
     */
    public function attachmentsgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.attachment')
            ->setCategoryAttachments($this->getRequest()->getPost('category_attachments', null));
        $this->renderLayout();
    }
}
