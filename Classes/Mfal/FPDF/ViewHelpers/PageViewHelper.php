<?php
namespace Mfal\FPDF\ViewHelpers;

class PageViewHelper extends AbstractFPDFViewHelper {

	/**
	 * @param string $orientation
	 * @param string $size
	 */
	public function render($orientation = '', $size = '') {
		$this->fpdf()->AddPage($orientation, $size);
		$this->renderChildren();
	}
}