<?php
namespace Mfal\FPDF\ViewHelpers;

class LnViewHelper extends AbstractFPDFViewHelper {

	/**
	 * @param string $height
	 */
	public function render($height = NULL) {
		$this->fpdf()->Ln($height);
	}
}