<?php
namespace Mfal\FPDF\ViewHelpers;

class CellViewHelper extends AbstractFPDFViewHelper {

	/**
	 * @param int $width
	 * @param int $height
	 * @param string $border
	 * @param int $lineBreak
	 * @param string $align
	 * @param bool $fill
	 * @param string $link
	 */
	public function render($width = 0, $height = 0, $border = '0', $lineBreak = 0, $align = 'L', $fill = FALSE, $link = '') {
		$align = strtoupper($align);
		$text = $this->renderTextChildern();
		$this->fpdf()->Cell($width, $height, $text, $border, $lineBreak, $align, $fill, $link);
	}
}