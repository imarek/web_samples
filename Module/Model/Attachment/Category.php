<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment category model
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Attachment_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author
     */
    protected function _construct()
    {
        $this->_init('arcyro_document/attachment_category');
    }

    /**
     * Save data for attachment-category relation
     *
     * @access public
     * @param  Arcyro_Document_Model_Attachment $attachment
     * @return Arcyro_Document_Model_Attachment_Category
     * @author
     */
    public function saveAttachmentRelation($attachment)
    {
        $data = $attachment->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveAttachmentRelation($attachment, $data);
        }
        return $this;
    }

    /**
     * get categories for attachment
     *
     * @access public
     * @param Arcyro_Document_Model_Attachment $attachment
     * @return Arcyro_Document_Model_Resource_Attachment_Category_Collection
     * @author
     */
    public function getCategoryCollection($attachment)
    {
        $collection = Mage::getResourceModel('arcyro_document/attachment_category_collection')
            ->addAttachmentFilter($attachment);
        return $collection;
    }
}
