<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment - Category relation resource model collection
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Resource_Attachment_Category_Collection extends Mage_Catalog_Model_Resource_Category_Collection
{
    /**
     * remember if fields have been joined
     *
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Arcyro_Document_Model_Resource_Attachment_Category_Collection
     * @author
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('arcyro_document/attachment_category')),
                'related.category_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add attachment filter
     *
     * @access public
     * @param Arcyro_Document_Model_Attachment | int $attachment
     * @return Arcyro_Document_Model_Resource_Attachment_Category_Collection
     * @author
     */
    public function addAttachmentFilter($attachment)
    {
        if ($attachment instanceof Arcyro_Document_Model_Attachment) {
            $attachment = $attachment->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.attachment_id = ?', $attachment);
        return $this;
    }
}
