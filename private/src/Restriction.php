<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 27.07.18
 * Time: 11:38
 */

require_once 'Base.php';

class Restriction extends Base {
	/** @var string  */
	protected string $_base = '';

	/** @var array  */
	protected array $_values = [];

	/**
	 * @return string
	 */
	public function getBase(): string
	{
		return $this->_base;
	}

	/**
	 * @param string $base
	 */
	public function setBase(string $base)
	{
		$this->_base = $base;
	}

	/**
	 * @return array
	 */
	public function getValues(): array
	{
		return $this->_values;
	}

	/**
	 * @param array $values
	 */
	public function setValues(array $values)
	{
		$this->_values = $values;
	}

    /**
     * @param $DOMNode
     * @param $DOMXPath
     * @throws Exception
     */
	public function __construct(
		$DOMNode,
		$DOMXPath
	) {
		parent::__construct($DOMXPath);
		$this->setBase(strtolower($DOMNode->getAttribute('base')));

		switch($this->getBase()) {
			case XSD::NAMESPACE . ':integer': $this->_initializeInteger($DOMNode); break;
			case XSD::NAMESPACE . ':string': $this->_initializeString($DOMNode); break;
			default: throw new Exception('unknown base: ' . $this->getBase());
		}
	}

	/**
	 * @param DOMNode $DOMNode
	 */
	protected function _initializeInteger(DOMNode $DOMNode) {
		$values = [];

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':minInclusive',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['minInclusive'] = $restrictions[0]->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':maxInclusive',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['maxInclusive'] = $restrictions[0]->getAttribute('value');
		}

		$this->setValues($values);
	}

	/**
	 * @param DOMNode $DOMNode
	 */
	protected function _initializeString (DOMNode $DOMNode) {
		$values = [];

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':enumeration',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['enumerations'] = [];
		}

		foreach($restrictions as $restriction) {
			$values['enumerations'][] = $restriction->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':pattern',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['pattern'] = $restrictions[0]->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':whitespace',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['whitespace'] = $restrictions[0]->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':length',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['length'] = $restrictions[0]->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':minLength',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['minLength'] = $restrictions[0]->getAttribute('value');
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':maxLength',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$values['maxLength'] = $restrictions[0]->getAttribute('value');
		}

		$this->setValues($values);
	}

	/**
	 *
	 */
	public function __toString() {
		$result = '';

		foreach ($this->getValues() as $key => $value) {
			$result .= $key . ': ' . $value . '<br>';
		}

		if(count($this->getValues()) > 0) {
			$result = substr(
				$result,
				0,
				-4
			);
		}

		return $result;
	}
}