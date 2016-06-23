<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment model
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Attachment extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'arcyro_document_attachment';
    const CACHE_TAG = 'arcyro_document_attachment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'arcyro_document_attachment';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'attachment';
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('arcyro_document/attachment');
    }

    /**
     * before save attachment
     *
     * @access protected
     * @return Arcyro_Document_Model_Attachment
     * @author
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save attachment relation
     *
     * @access public
     * @return Arcyro_Document_Model_Attachment
     * @author
     */
    protected function _afterSave()
    {
        $this->getCategoryInstance()->saveAttachmentRelation($this);
        return parent::_afterSave();
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Arcyro_Document_Model_Attachment_Category
     * @author
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('arcyro_document/attachment_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Arcyro_Document_Resource_Attachment_Category_Collection
     * @author
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
    
}
