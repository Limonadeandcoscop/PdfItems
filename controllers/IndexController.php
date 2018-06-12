<?php

class PdfItems_IndexController extends Omeka_Controller_AbstractActionController {

    
    public function init() {
    }

    /**
     * Display item in PDF
     *
     * @return PDF file
     */
    public function pdfAction() {

        $this->_helper->viewRenderer->setNoRender(true);

        $item_id = $this->getParam('item-id');
        if (!$item_id) {
            throw new Exception("Invalid item ID");
        }

        $item = get_record_by_id('Item', $item_id);
        if (!$item) {
            throw new Exception("Invalid item");
        }

        $download = $this->getParam('download');

        new Pdf_Items($item, $download);
    }


}

