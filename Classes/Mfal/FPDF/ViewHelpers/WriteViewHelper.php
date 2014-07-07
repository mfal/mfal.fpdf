<?php
namespace Mfal\FPDF\ViewHelpers;

class WriteViewHelper extends AbstractFPDFViewHelper {

	/**
	 * @param int $height
	 * @param string $link
	 */
	public function render($height, $link = '') {
		$text = $this->renderTextChildern();
		$this->fpdf()->Write($height, $text, $link);
	}
}