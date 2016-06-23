<?php
/**
 * Arcyro_Document extension
 */

/**
 * Attachment admin controller
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Adminhtml_Document_ShowattachmentController extends Arcyro_Document_Controller_Adminhtml_Document
{
    /**
     * init the attachment
     * @access protected
     * @return Arcyro_Document_Model_Attachment
     */
    protected function _initCategory()
    {
        $session = Mage::getSingleton('core/session');
        $categoryId = (int)$this->getRequest()->getParam('id');
        $category = Mage::getModel('catalog/category');
        if ($categoryId) {
            $category->load($categoryId);
            $session->setData("categoryId", $categoryId);
        } elseif ($session->getData('categoryId')) {
            $category->load($session->getData('categoryId'));
        }
        Mage::register('current_category', $category);

    }

    /**
     * Download file by admin
     */
    public function downloadAttachmentAction()
    {
        $attachmentId = (int)$this->getRequest()->getParam('id');
        $attachment = Mage::getModel('arcyro_document/attachment');

        if ($attachmentId) {
            $attachment->load($attachmentId);
        }

        if ($attachmentId && !$attachment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('arcyro_document')->__('This attachment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }

        //check user can download
        if (!Mage::helper('arcyro_document/attachment')->isAllowed($attachment)) {
            $this->_getSession()->addError(
                Mage::helper('arcyro_document')->__('You do not have permission to download the file.')
            );
            $this->_redirect('*/*/');
            return;
        }

        $file = Mage::helper('arcyro_document/attachment')->getFileBaseDir() . DS . $attachment->getUploadedFile();
        $content = file_get_contents($file);
        $this->_prepareDownloadResponse(basename($file), $content);
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author
     */
    public function indexAction()
    {

        $this->loadLayout();
        $this->_title(Mage::helper('arcyro_document')->__('Document'))
            ->_title(Mage::helper('arcyro_document')->__('Attachment'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author
     */
    public function gridAction()
    {
        $this->_initCategory();
        $this->loadLayout()->renderLayout();
    }


    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author
     */
    public function categoriesAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author
     */
    public function categoriesJsonAction()
    {
        $this->_initCategory();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('arcyro_document/adminhtml_showattachment_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }


    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('arcyro_document/show_attachment');
    }
}
