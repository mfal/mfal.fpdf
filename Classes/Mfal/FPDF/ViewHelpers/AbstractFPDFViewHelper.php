<?php
namespace Mfal\FPDF\ViewHelpers;

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class AbstractFPDFViewHelper extends AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $variableName = 'FPDFObject';

	protected $argumentMappings = array(
		'fontFamily' => array('FontFamily', 'SetFont'),
		'fontSize' => array('FontSize', 'SetFontSize'),
		'fontStyle' => array('FontStyle', 'SetFont'),
		'marginLeft' => array('lMargin', 'SetLeftMargin'),
		'marginTop' => array('tMargin', 'SetTopMargin'),
		'marginRight' => array('rMargin', 'SetRightMargin'),
		'positionX' => array('x', 'SetX'),
		'positionY' => array('y', 'SetY')
	);

	public function initializeArguments() {
		parent::initializeArguments();
		foreach (array_keys($this->argumentMappings) as $argumentName) {
			$this->registerArgument($argumentName, 'string', '');
		}
	}

	/**
	 * @return \fpdf\FPDF
	 */
	public function fpdf() {
		return $this->templateVariableContainer->get($this->variableName);
	}

	/**
	 * @return string
	 */
	protected function renderTextChildern() {
		return iconv('UTF-8', 'windows-1252', str_replace(PHP_EOL, "\n", $this->renderChildren()));
	}

	protected function setDefaultFont() {
		$this->fpdf()->SetFont('Arial', '', 12);
	}

	protected function callRenderMethod() {
		$previousValues = array();
		foreach ($this->argumentMappings as $argumentName => $fpdf) {
			if ($this->hasArgument($argumentName) === FALSE) {
				continue;
			}

			$propertyName = $fpdf[0];
			$setterFunctionName = $fpdf[1];

			$previousValues[$argumentName] = $this->fpdf()->$propertyName;

			if ($argumentName === 'fontSize') {
				$previousValues[$argumentName] = $previousValues[$argumentName] * $this->fpdf()->k;
			}

			if ($argumentName === 'fontStyle') {
				$this->fpdf()->$setterFunctionName('', $this->arguments[$argumentName]);
			} else {
				$this->fpdf()->$setterFunctionName($this->arguments[$argumentName]);
			}
		}

		$result = parent::callRenderMethod();

		foreach ($this->argumentMappings as $argumentName => $fpdf) {
			if ($this->hasArgument($argumentName)) {
				$setterFunctionName = $fpdf[1];

				if ($argumentName === 'fontStyle') {
					$this->fpdf()->$setterFunctionName('', $previousValues[$argumentName]);
				} else {
					$this->fpdf()->$setterFunctionName($previousValues[$argumentName]);
				}
			}
		}

		return $result;
	}
}
