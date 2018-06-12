<?php
/**
 * Cart_Pdf
 *
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Cart Pdf helper
 *
 * @package Omeka\Plugins\Cart
 */

class Pdf_Items
{
	// The PDF object
	private $_pdf;


	/**
	 * Class constructor :
	 *  - Initialize FPDF class
	 *  - Generate pages for each item
	 *  - Render PDF in browser
	 */
	public function __construct($items, $download)
	{
		$this->_pdf = new tcpdf('P', 'mm', 'A4');

		$title = metadata($items[0], array('Dublin Core', 'Title'));
		$this->_pdf->SetTitle($title .' - '. get_option('site_title'));

		foreach ($items as $item) {
			$this->_addPage($item);	
		}
		
		
		if ($download == 1) {
			$this->_render($title);
		} else {
			$this->_render();
		}
	}


	/**
	 * Add a page to PDF document
	 * @param Item $item The item object
	 */
	protected function _addPage($item) {

		$pdf = $this->_pdf;

		// Create page and display header
		$pdf->AddPage();
		$this->_setHeader();
		$pdf->SetMargins(10,10,10);

		// Retrieve elements texts
		$elements = all_element_texts($item, array('return_type' => 'array'));

		// Add identifiers to all_element_texts() results
		$identifiers = metadata($item, array("Dublin Core", "Identifier"), array("all" => true));
		$elements['Dublin Core']['Identifier'] = $identifiers;

		// Display title
		$title = $elements['Dublin Core']['Title'];
		$title = implode(' - ', $title);
		$title = $this->_getValue($title);
		$pdf->SetFont('dejavusans','B',16);
		$pdf->MultiCell(0, 7, $title, 0, 'L');

		unset($elements['Dublin Core']['Title']);
		$pdf->Ln(8);

		// Display AdditionalResource specific values
		if ($item->item_type_id != AdditionalResourcesPlugin::BIBLIOGRAPHIC_ITEM) {

			$url = absolute_url('items/values/'.$item->id);
			$json = file_get_contents($url);
			$data = json_decode($json, TRUE);

			
			foreach ($data['values'] as $section => $values) {

				$pdf->SetFont('dejavusans','B',18);
				$pdf->MultiCell(0, 10, $section, 0, 'L');

				foreach($values as $vals) {

					$title = key($vals);
					$datas = array_shift($vals);

					$pdf->SetFont('dejavusans','B',11);
					$pdf->MultiCell(0, 10, $title, 0, 'L');

					foreach ($datas as $val) {
						
						$pdf->SetFont('dejavusans','',11);
						$pdf->MultiCell(0, 6, $this->_getValue($val), 0, 1);
						$pdf->ln(3);
					}
				}
			}

		} else { // Display raw metadata

			foreach ($elements as $elementSetName => $elementTexts) {
				foreach ($elementTexts as $elementName => $elementsText) {
					$pdf->SetFont('dejavusans','B',12);
					$pdf->MultiCell(0, 10, str_replace('PDF:', '', __('PDF:'.$elementName)), 0, 2); // Prefix label in PDF by "PDF:", a way to override default translations with plugin translations
					foreach ($elementsText as $element) {
						$pdf->SetFont('dejavusans','',12);
						$pdf->MultiCell(0, 6, $this->_getValue($element), 0, 1);
						$pdf->ln(3);
					}
				}
			}
		}

		// Display footer
		$this->_setFooter($item);
	}


	/**
	 * Add a page to PDF document
	 */
	protected function _render($title = null) {

		if (strlen(trim($title))) {
			$this->_pdf->Output($title.'.pdf', "D");
		} else {
			header('Content-type: application/pdf');
			$this->_pdf->Output();
		}
		exit;
	}


	/**
	 * Add the header
	 */
	protected function _setHeader()
	{
		$pdf = $this->_pdf;
		$headerText = get_option('site_title');
	    $pdf->SetFont('dejavusans','',10);
	    $pdf->Ln(0);
	    $pdf->MultiCell(0, 7, $headerText, 0, 'C');
	    $pdf->Ln(10);
	}


	/**
	 * Add the footer
	 */
	protected function _setFooter($item = null)
	{
		$pdf = $this->_pdf;
		$footerText = isset($item) ? $item->getProperty('permalink') : WEB_DIR;
		$pdf->SetY(-28);
	    $pdf->SetFont('dejavusans','',10);
	    $pdf->MultiCell(0, 7, $footerText, 0, 'C');
	}


	/**
	 * Prevent encoding issues with FPDF library
	 */
	protected function _getValue($value)
	{
		if (strlen(trim($value))) {
			$value = ucfirst(strip_tags($value));
			///$value = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $value); // Prevent MS-Word copy/paste
		}
		return $value;
	}
}