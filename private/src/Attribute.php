<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 27.07.18
 * Time: 11:37
 */

require_once 'Element.php';

class Attribute extends Element {
	/** @var string  */
	protected string $_name = '';

	/** @var string  */
	protected string $_type = '';

	/** @var string  */
	protected string $_use = '';

	/** @var null  */
	protected $_restriction = null;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name) {
		$this->_name = $name;
	}

	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->_type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type) {
		$this->_type = str_ireplace(XSD::NAMESPACE . ':', '', $type);
	}

	/**
	 * @return string
	 */
	public function getUse(): string {
		return $this->_use;
	}

	/**
	 * @param string $use
	 */
	public function setUse(string $use) {
		$this->_use = $use;
	}

	/**
	 * @return null
	 */
	public function getRestriction()
	{
		return $this->_restriction;
	}

	/**
	 * @param null $restriction
	 */
	public function setRestriction($restriction)
	{
		$this->_restriction = $restriction;
	}

    /**
     * @param DOMNode $DOMNode
     * @param DOMXPath $DOMXPath
     * @throws Exception
     */
	public function __construct(
		DOMNode $DOMNode,
		DOMXPath $DOMXPath
	) {
		parent::__construct($DOMXPath);

		$this->setName($DOMNode->getAttribute('name'));
		$this->setType($DOMNode->getAttribute('Node'));
		$this->setUse($DOMNode->getAttribute('use'));

		if($DOMNode->getAttribute('type')) {
			$this->setType($DOMNode->getAttribute('type'));
		}

		$documentations = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':annotation/' . XSD::NAMESPACE . ':documentation',
			$DOMNode
		);
		if($documentations->length > 0) {
			$example = $this->_parseExample($documentations[0]);
			$this->setExample($example);
			$this->setDocumentation($documentations[0]->nodeValue);
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':simpleType/' . XSD::NAMESPACE . ':restriction',
			$DOMNode
		);

		if($restrictions->length > 0) {
			$this->setRestriction(
				new Restriction(
					$restrictions[0],
					$this->getXpath()
				)
			);
			if('' == $this->getType()) {
				$this->setType($this->getRestriction()->getBase());
			}
		}
	}
}