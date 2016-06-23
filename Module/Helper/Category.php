<?php
/**
 * Arcyro_Document extension
 */
/**
 * Category helper
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Helper_Category extends Arcyro_Document_Helper_Data
{

    /**
     * get the selected attachment for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author
     */
    public function getSelectedAttachments(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedAttachments()) {
            $attachments = array();
            foreach ($this->getSelectedAttachmentsCollection($category) as $attachment) {
                $attachments[] = $attachment;
            }
            $category->setSelectedAttachments($attachments);
        }
        return $category->getData('selected_attachments');
    }

    /**
     * get attachment collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Arcyro_Document_Model_Resource_Attachment_Collection
     * @author
     */
    public function getSelectedAttachmentsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('arcyro_document/attachment_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
