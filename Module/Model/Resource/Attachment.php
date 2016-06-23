<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment resource model
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Model_Resource_Attachment extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author
     */
    public function _construct()
    {
        $this->_init('arcyro_document/attachment', 'entity_id');
    }
}
