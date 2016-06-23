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
class Arcyro_Document_Adminhtml_Document_AttachmentController extends Arcyro_Document_Controller_Adminhtml_Document
{
    /**
     * init the attachment
     *
     * @access protected
     * @return Arcyro_Document_Model_Attachment
     */
    protected function _initAttachment()
    {
        $attachmentId  = (int) $this->getRequest()->getParam('id');
        $attachment    = Mage::getModel('arcyro_document/attachment');
        if ($attachmentId) {
            $attachment->load($attachmentId);
        }
        Mage::register('current_attachment', $attachment);
        return $attachment;
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
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit attachment - action
     *
     * @access public
     * @return void
     * @author
     */
    public function editAction()
    {
        $attachmentId    = $this->getRequest()->getParam('id');
        $attachment      = $this->_initAttachment();
        if ($attachmentId && !$attachment->getId()) {
            $this->_getSession()->addError(
                Mage::helper('arcyro_document')->__('This attachment no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getAttachmentData(true);
        if (!empty($data)) {
            $attachment->setData($data);
        }
        Mage::register('attachment_data', $attachment);
        $this->loadLayout();
        $this->_title(Mage::helper('arcyro_document')->__('Document'))
             ->_title(Mage::helper('arcyro_document')->__('Attachment'));
        if ($attachment->getId()) {
            $this->_title($attachment->getTitle());
        } else {
            $this->_title(Mage::helper('arcyro_document')->__('Add attachment'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new attachment action
     *
     * @access public
     * @return void
     * @author
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save attachment - action
     *
     * @access public
     * @return void
     * @author
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('attachment')) {
            try {
                $attachment = $this->_initAttachment();
                $attachment->addData($data);
                $uploadedFileName = $this->_uploadAndGetName(
                    'uploaded_file',
                    Mage::helper('arcyro_document/attachment')->getFileBaseDir(),
                    $data
                );
                $attachment->setData('uploaded_file', $uploadedFileName);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $attachment->setCategoriesData($categories);
                }
                $attachment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('arcyro_document')->__('Attachment was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $attachment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                if (isset($data['uploaded_file']['value'])) {
                    $data['uploaded_file'] = $data['uploaded_file']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAttachmentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                if (isset($data['uploaded_file']['value'])) {
                    $data['uploaded_file'] = $data['uploaded_file']['value'];
                }
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('arcyro_document')->__('There was a problem saving the attachment.')
                );
                Mage::getSingleton('adminhtml/session')->setAttachmentData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('arcyro_document')->__('Unable to find attachment to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete attachment - action
     *
     * @access public
     * @return void
     * @author
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $attachment = Mage::getModel('arcyro_document/attachment');
                $attachment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('arcyro_document')->__('Attachment was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('arcyro_document')->__('There was an error deleting attachment.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('arcyro_document')->__('Could not find attachment to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete attachment - action
     *
     * @access public
     * @return void
     * @author
     */
    public function massDeleteAction()
    {
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('arcyro_document')->__('Please select attachment to delete.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                    $attachment = Mage::getModel('arcyro_document/attachment');
                    $attachment->setId($attachmentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('arcyro_document')->__('Total of %d attachment were successfully deleted.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('arcyro_document')->__('There was an error deleting attachment.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author
     */
    public function massStatusAction()
    {
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('arcyro_document')->__('Please select attachment.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                $attachment = Mage::getSingleton('arcyro_document/attachment')->load($attachmentId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attachment were successfully updated.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('arcyro_document')->__('There was an error updating attachment.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass enabled change - action
     *
     * @access public
     * @return void
     * @author
     */
    public function massEnabledAction()
    {
        $attachmentIds = $this->getRequest()->getParam('attachment');
        if (!is_array($attachmentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('arcyro_document')->__('Please select attachment.')
            );
        } else {
            try {
                foreach ($attachmentIds as $attachmentId) {
                $attachment = Mage::getSingleton('arcyro_document/attachment')->load($attachmentId)
                    ->setEnabled($this->getRequest()->getParam('flag_enabled'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attachment were successfully updated.', count($attachmentIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('arcyro_document')->__('There was an error updating attachment.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
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
        $this->_initAttachment();
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
        $this->_initAttachment();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('arcyro_document/adminhtml_attachment_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author
     */
    public function exportCsvAction()
    {
        $fileName   = 'attachment.csv';
        $content    = $this->getLayout()->createBlock('arcyro_document/adminhtml_attachment_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author
     */
    public function exportExcelAction()
    {
        $fileName   = 'attachment.xls';
        $content    = $this->getLayout()->createBlock('arcyro_document/adminhtml_attachment_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author
     */
    public function exportXmlAction()
    {
        $fileName   = 'attachment.xml';
        $content    = $this->getLayout()->createBlock('arcyro_document/adminhtml_attachment_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
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
        return Mage::getSingleton('admin/session')->isAllowed('arcyro_document/attachment');
    }
}
