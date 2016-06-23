<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment - Categories relation model
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Resource_Attachment_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author
     */
    protected function  _construct()
    {
        $this->_init('arcyro_document/attachment_category', 'rel_id');
    }

    /**
     * Save attachment - category relations
     *
     * @access public
     * @param Arcyro_Document_Model_Attachment $attachment
     * @param array $data
     * @return Arcyro_Document_Model_Resource_Attachment_Category
     * @author
     */
    public function saveAttachmentRelation($attachment, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('attachment_id=?', $attachment->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'attachment_id' => $attachment->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - attachment relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Arcyro_Document_Model_Resource_Attachment_Category
     * @author
     */
    public function saveCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('category_id=?', $category->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $attachmentId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'attachment_id' => $attachmentId,
                    'category_id'   => $category->getId(),
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save category admin roles
     * @param $category
     * @param $data
     * @return $this
     */
    public function saveCategoryRoles($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('category_id=?', $category->getId());
        $this->_getWriteAdapter()->delete($this->getTable('arcyro_document/attachment_category_roles'), $deleteCondition);

        foreach ($data as $roleId ) {
            $this->_getWriteAdapter()->insert(
                $this->getTable('arcyro_document/attachment_category_roles'),
                array(
                    'role_id' => $roleId,
                    'category_id'   => $category->getId()
                )
            );
        }
        return $this;
    }
}
