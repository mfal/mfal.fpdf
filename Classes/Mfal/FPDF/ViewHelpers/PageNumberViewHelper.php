<?php
namespace Mfal\FPDF\ViewHelpers;

class PageNumberViewHelper extends AbstractFPDFViewHelper {

	public function render() {
		return $this->fpdf()->PageNo();
	}
}