<?php
/**
 * Arcyro_Document extension
 */
/**
 * attachment - categories relation edit block
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Showattachment_Categories extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_categoryIds = null;
    protected $_selectedNodes = null;

    /**
     * constructor
     *
     * Specify template to use
     * @access public
     * @author
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('arcyro_document/attachment/edit/tab/categories-show.phtml');
        $this->_withProductCount = false;
    }

    /**
     * Retrieve currently edited attachment
     *
     * @access public
     * @return Arcyro_Document_Model_Attachment
     * @author
     */
    public function getAttachment()
    {
        return Mage::registry('current_attachment');
    }

    /**
     * Returns root node and sets 'checked' flag (if necessary)
     *
     * @access public
     * @return Varien_Data_Tree_Node
     * @author
     */
    public function getRootNode()
    {
        $root = $this->getRoot();
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Arcyro_Document_Model_Category|null $parentNodeCategory
     * @param int  $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author
     */
    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }
        $root = Mage::registry('category_root');
        if (is_null($root)) {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            $ids = $this->getSelectedCategoryPathIds($rootId);
            $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->loadByIds($ids, false, false);
            if ($this->getCategory()) {
                $tree->loadEnsuredNodes($this->getCategory(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getCategoryCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('category_root', $root);
        }
        return $root;
    }

    /**
     * Get attachments categories connected with current logged admin user
     * @return Mage_Catalog_Model_Resource_Category_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection|mixed
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getCategoryCollection()
    {
        $user = Mage::getSingleton('admin/session');
        $roleId = $user->getUser()->getRole()->getId();

        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $collection->getSelect()->join(
                array('related' => $collection->getTable('arcyro_document/attachment_category_roles')),
                'related.category_id=e.entity_id AND related.role_id ='.$roleId, array()
            )->distinct(true);

            $this->setData('category_collection', $collection);
        }
        return $collection;
    }
    /**
     * Returns array with configuration of current node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @param int $level How deep is the node in the tree
     * @return array
     * @author
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);
        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @return bool
     * @author
     */
    protected function _isParentSelectedCategory($node)
    {
        $result = false;
        // Contains string with all category IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedCategoryIds = $this->getCategoryIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedCategoryIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedCategoryIds[$i];
                if (!$isSelf && in_array($selectedCategoryIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns array with nodes those are selected (contain current attachment)
     *
     * @return array
     */
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getCategoryIds() as $categoryId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($categoryId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of category children
     *
     * @access public
     * @param int $categoryId
     * @return string
     * @author
     */
    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }

    /**
     * Returns URL for loading tree
     *
     * @access public
     * @param null $expanded
     * @return string
     * @author
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/*/categoriesJson', array('_current' => true));
    }

    /**
     *  Return url to reload grid with attachments
     * @return string
     */
    public function getLoadGridUrl()
    {
        return $this->getUrl('adminhtml/document_showattachment/grid/', array('_current' => true));
    }

    /**
     * Return distinct path ids of selected category
     *
     * @access public
     * @param mixed $rootId Root category Id for context
     * @return array
     * @author
     */
    public function getSelectedCategoryPathIds($rootId = false)
    {
        $ids = array();
        $categoryIds = $this->getCategoryIds();
        if (empty($categoryIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('catalog/category_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', array('in'=>$categoryIds));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

}
