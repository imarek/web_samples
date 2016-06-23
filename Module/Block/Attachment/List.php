<?php
/**
 * Arcyro_Document extension
 */
/**
 * Attachment list block
 *
 * @category    Arcyro
 * @package     Arcyro_Document
 * @author
 */
class Arcyro_Document_Block_Attachment_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author
     */
    public function __construct()
    {
        parent::__construct();
        $attachments = Mage::getResourceModel('arcyro_document/attachment_collection')
                         ->addFieldToFilter('status', 1);
        $attachments->setOrder('title', 'asc');
        $this->setAttachments($attachments);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Arcyro_Document_Block_Attachment_List
     * @author
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'arcyro_document.attachment.html.pager'
        )
        ->setCollection($this->getAttachments());
        $this->setChild('pager', $pager);
        $this->getAttachments()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
