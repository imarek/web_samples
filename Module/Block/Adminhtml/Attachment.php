<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment admin block
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Adminhtml_Attachment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_attachment';
        $this->_blockGroup         = 'arcyro_document';
        parent::__construct();
        $this->_headerText         = Mage::helper('arcyro_document')->__('Attachment');
        $this->_updateButton('add', 'label', Mage::helper('arcyro_document')->__('Add Attachment'));

    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }
}
