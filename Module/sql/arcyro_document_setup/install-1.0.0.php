<?php
/**
 * Arcyro_Document extension
 */
/**
 * Document module install script
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
$this->startSetup();
$this->run("DROP TABLE IF EXISTS {$this->getTable('arcyro_document/attachment')};");
$this->run("DROP TABLE IF EXISTS {$this->getTable('arcyro_document/attachment_category')};");
$this->run("DROP TABLE IF EXISTS {$this->getTable('arcyro_document/attachment_category_roles')};");
$table = $this->getConnection()
    ->newTable($this->getTable('arcyro_document/attachment'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Attachment ID'
    )
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array('nullable' => false,), 'title')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('nullable' => true), 'Notes')
    ->addColumn('uploaded_file', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'uploaded_file')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(), 'Enabled')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Attachment Modification Time')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Attachment Creation Time')
    ->setComment('Attachment Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
    ->newTable($this->getTable('arcyro_document/attachment_category'))
    ->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Relation ID')
    ->addColumn('attachment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false, 'default' => '0',),
        'Attachment ID'
    )
    ->addColumn(
        'category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Category ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('nullable' => false, 'default' => '0',), 'Position')
    ->addIndex($this->getIdxName('arcyro_document/attachment_category', array('category_id')), array('category_id'))
    ->addForeignKey($this->getFkName(
        'arcyro_document/attachment_category',
        'attachment_id',
        'arcyro_document/attachment',
        'entity_id'
    ),
        'attachment_id',
        $this->getTable('arcyro_document/attachment'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'arcyro_document/attachment_category',
            'category_id',
            'catalog/category',
            'entity_id'
        ),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'arcyro_document/attachment_category',
            array('attachment_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('attachment_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Attachment to Category Linkage Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
    ->newTable($this->getTable('arcyro_document/attachment_category_roles'))
    ->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Relation ID')
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('unsigned' => true, 'nullable' => false, 'default' => '0',),
        'Role ID'
    )
    ->addColumn(
        'category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Category ID')
    ->addIndex($this->getIdxName('arcyro_document/attachment_category_roles', array('category_id')), array('category_id'))
    ->addForeignKey($this->getFkName(
        'arcyro_document/attachment_category_roles',
        'role_id',
        'admin/role',
        'role_id'
    ),
        'role_id',
        $this->getTable('admin/role'),
        'role_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'arcyro_document/attachment_category_roles',
            'category_id',
            'catalog/category',
            'entity_id'
        ),
        'category_id',
        $this->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addIndex(
        $this->getIdxName(
            'arcyro_document/attachment_category_roles',
            array('role_id', 'category_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('role_id', 'category_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Category to Roles Linkage Table');
$this->getConnection()->createTable($table);

$this->endSetup();
