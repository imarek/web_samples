<?php
/**
 * Arcyro_Document extension
 */

/**
 * Attachment helper
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Helper_Attachment extends Mage_Core_Helper_Abstract
{

    /**
     * get base files dir
     *
     * @access public
     * @return string
     * @author
     */
    public function getFileBaseDir()
    {
        return Mage::getBaseDir('media') . DS . 'attachment' . DS . 'file';
    }

    /**
     * get base file url
     *
     * @access public
     * @return string
     * @author
     */
    public function getFileBaseUrl()
    {
        return Mage::getBaseUrl('media') . 'attachment' . '/' . 'file';
    }


    /**
     * Check admin can download attachment
     * @return boolean
     * @param $attachment
     */
    public function isAllowed($attachment)
    {
        $categories = Mage::getModel('arcyro_document/attachment_category')->getCategoryCollection($attachment);
        $categoriesIds = array();
        foreach ($categories as $category) {
            $categoriesIds[] = $category->getId();
        }

        $user = Mage::getSingleton('admin/session');
        $roleId = $user->getUser()->getRole()->getId();
        $categoryRoles = Mage::getModel('arcyro_document/attachment_category_roles')
            ->getCollection()
            ->addFieldToFilter('role_id', $roleId)
            ->addFieldToFilter('category_id', array('in' => $categoriesIds))
            ->load();
        if ($categoryRoles->count() > 0) {
            return true;
        }
        return false;
    }
}
