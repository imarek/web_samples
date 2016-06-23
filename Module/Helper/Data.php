<?php
/**
 * Arcyro_Document extension
 */

/**
 * Document default helper
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])
            ) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    /**
     * Get admin roles
     * @param bool $inlcudeNone
     * @return array
     */
    public function getRolesOptionValues($inlcudeNone = false)
    {

        $collection = Mage::getModel('admin/roles')->getCollection();
        $values = array();
        if ($inlcudeNone) {
            $values[] = array('label' => "--None--", 'value' => 0);
        }
        foreach ($collection as $category) {
            $values[] = array('label' => $category->getRoleName(), 'value' => $category->getId());
        }
        return $values;
    }

    /**
     *  Get selected roles for category
     * @param $category
     * @return array
     */
    public function getSelectedCategoryRoles($category)
    {
        $values = array();
        if (!$category->getId()) {
            return array();
        }
        $collection = Mage::getModel('arcyro_document/attachment_category_roles')->getCollection();
        /** @var $category Mage_Catalog_Model_Category */
        $collection->addFilter('category_id', $category->getId());
        foreach ($collection as $category) {
            $values[] = $category->getRoleId();
        }
        return array_values($values);
    }
}
