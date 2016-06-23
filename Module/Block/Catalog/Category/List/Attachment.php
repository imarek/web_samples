<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment list on category page block
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Catalog_Category_List_Attachment extends Mage_Core_Block_Template
{
    /**
     * get the list of attachment
     *
     * @access protected
     * @return Arcyro_Document_Model_Resource_Attachment_Collection
     * @author
     */
    public function getAttachmentCollection()
    {
        if (!$this->hasData('attachment_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('arcyro_document/attachment_collection')
                ->addFieldToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('attachment_collection', $collection);
        }
        return $this->getData('attachment_collection');
    }
}
