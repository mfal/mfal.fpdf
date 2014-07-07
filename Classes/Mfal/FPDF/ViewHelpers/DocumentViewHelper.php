<?php
namespace Mfal\FPDF\ViewHelpers;

use TYPO3\Flow\Cli\Response as CliResponse;
use TYPO3\Flow\Http\Headers;
use TYPO3\Flow\Http\Response;

class DocumentViewHelper extends AbstractFPDFViewHelper {

	protected $scaleFactor = array();

	public function initialize() {
		parent::initialize();

		$this->scaleFactor = array('pt' => 1,
			'mm' => (72 / 25.4),
			'cm' => (72 / 2.54),
			'in' => 72
		);

		$fpdf = new \fpdf\FPDF();
		$this->templateVariableContainer->add($this->variableName, $fpdf);
	}

	/**
	 * @param string $defaultPageOrientation
	 * @param string $unit
	 * @param string $defaultSize
	 * @param string $name
	 * @param string $destination
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 */
	public function render($defaultPageOrientation = 'P', $unit = 'mm', $defaultSize = 'A4', $name = 'document.pdf', $destination = 'I') {
		$this->fpdf()->k = $this->scaleFactor[$unit];
		$this->fpdf()->DefOrientation = $defaultPageOrientation;
		$this->fpdf()->DefPageSize = $defaultSize;
		$this->setDefaultFont();

		$this->renderChildren();

		// Todo: Or like this?
		//$this->fpdf()->Output(NULL, $destination);
		//exit();

		$response = $this->renderingContext->getControllerContext()->getResponse();
		if ($response instanceof Response) {
			$header = array(
				'Cache-Control' => 'private, max-age=0, must-revalidate',
				'Pragma' => 'public',
			);
			if ($destination === 'D') {
				$header['Content-Type'] = 'application/x-download';
				$header['Content-Disposition'] = 'attachment; filename="' . $name . '"';
			} else {
				$header['Content-Type'] = 'application/pdf';
				$header['Content-Disposition'] = 'inline; filename="' . $name . '"';
			}
			$response->setHeaders(new Headers($header));
			$response->setContent($this->fpdf()->Output(NULL, 'S'));
		} else if ($response instanceof CliResponse) {
			$this->fpdf()->Output($name, 'S');
			$response->setContent('Saved file to ' . $name);
		}
		throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
	}
}
