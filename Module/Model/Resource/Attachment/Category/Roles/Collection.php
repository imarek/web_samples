<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 05.08.15
 * Time: 12:20
 */ 
class Arcyro_Document_Model_Resource_Attachment_Category_Roles_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('arcyro_document/attachment_category_roles');
    }

}