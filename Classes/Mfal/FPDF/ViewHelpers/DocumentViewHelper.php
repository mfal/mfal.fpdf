<?php
namespace Mfal\FPDF\ViewHelpers;

use TYPO3\Flow\Cli\Response as CliResponse;
use TYPO3\Flow\Http\Headers;
use TYPO3\Flow\Http\Response;

class DocumentViewHelper extends AbstractFPDFViewHelper {

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('defaultPageOrientation', 'string', '', FALSE, 'P');
		$this->registerArgument('unit', 'string', '', FALSE, 'mm');
		$this->registerArgument('defaultSize', 'string', '', FALSE, 'A4');
		$this->registerArgument('name', 'string', '', FALSE, 'document.pdf');
		$this->registerArgument('destination', 'string', '', FALSE, 'I');
	}

	protected function callRenderMethod() {
		$fpdf = new \fpdf\FPDF(
			$this->arguments['defaultPageOrientation'],
			$this->arguments['unit'],
			$this->arguments['defaultSize']
		);

		$this->templateVariableContainer->add($this->variableName, $fpdf);

		return parent::callRenderMethod();
	}

	/**
	 * @throws \TYPO3\Flow\Mvc\Exception\StopActionException
	 */
	public function render() {
		$this->setDefaultFont();

		$this->renderChildren();

		// Todo: Or better like this?
		//$this->fpdf()->Output(NULL, $destination);
		//exit();

		$response = $this->renderingContext->getControllerContext()->getResponse();
		if ($response instanceof Response) {
			$header = array(
				'Cache-Control' => 'private, max-age=0, must-revalidate',
				'Pragma' => 'public',
			);
			if ($this->arguments['destination'] === 'D') {
				$header['Content-Type'] = 'application/x-download';
				$header['Content-Disposition'] = 'attachment; filename="' . $this->arguments['name'] . '"';
			} else {
				$header['Content-Type'] = 'application/pdf';
				$header['Content-Disposition'] = 'inline; filename="' . $this->arguments['name'] . '"';
			}
			$response->setHeaders(new Headers($header));
			$response->setContent($this->fpdf()->Output(NULL, 'S'));
		} else if ($response instanceof CliResponse) {
			$this->fpdf()->Output($this->arguments['name'], 'S');
			$response->setContent('Saved file to ' . $this->arguments['name']);
		}
		throw new \TYPO3\Flow\Mvc\Exception\StopActionException();
	}
}
