<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 27.07.18
 * Time: 11:35
 */

require_once 'Element.php';
require_once 'Restriction.php';
require_once 'Attribute.php';

class Node extends Element {
	/** @var array */
	protected array $_nodes = [];

	/** @var string  */
	protected string $_name = '';

	/** @var string  */
	protected string $_type = '';

	/** @var int  */
	protected int $_min_occurances = 1;

	/** @var int  */
	protected int $_max_occurances = 1;

	/** @var null  */
	protected $_restriction = null;

	/** @var array  */
	protected array $_attributes = [];

	/**
	 * @return null
	 */
	public function getRestriction() {
		return $this->_restriction;
	}

	/**
	 * @param null $restriction
	 */
	public function setRestriction($restriction) {
		$this->_restriction = $restriction;
	}

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
	 * @return int
	 */
	public function getMinOccurances(): int {
		return $this->_min_occurances;
	}

	/**
	 * @param int $min_occurances
	 */
	public function setMinOccurances(int $min_occurances) {
		$this->_min_occurances = $min_occurances;
	}

	/**
	 * @return int
	 */
	public function getMaxOccurances(): int {
		return $this->_max_occurances;
	}

	/**
	 * @param int $max_occurances
	 */
	public function setMaxOccurances(int $max_occurances) {
		$this->_max_occurances = $max_occurances;
	}

    /**
     * Type constructor.
     *
     * @param DOMNode $DOMNode
     * @param DOMXPath $DOMXPath
     * @throws Exception
     */
	public function __construct (
		DOMNode $DOMNode,
		DOMXPath $DOMXPath
	) {
		parent::__construct($DOMXPath);
		$this->setName($DOMNode->getAttribute('name'));

		if('' != $DOMNode->getAttribute('minOccurs')) {
			$this->setMinOccurances($DOMNode->getAttribute('minOccurs'));
		}

		if('' != $DOMNode->getAttribute('maxOccurs')) {
			$this->setMaxOccurances(
				'unbounded' == $DOMNode->getAttribute('maxOccurs') ? 99999 : $DOMNode->getAttribute('maxOccurs')
			);
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

		$typeAddon = '';
		if($DOMNode->getAttribute('type')) {
			$this->setType($DOMNode->getAttribute('type'));
			$typeAddon = "[@name='" . $this->getType() . "']";
			$types = $this->getXpath()->evaluate(
				XSD::NAMESPACE . ':complexType' . $typeAddon
			);
		} else {
			$types = $this->getXpath()->evaluate(
				XSD::NAMESPACE . ':complexType' . $typeAddon,
				$DOMNode
			);
		}

		if($types->length > 0) {
			$this->_initialize($types[0]);
		}

		$types = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':simpleType' . $typeAddon,
			$DOMNode
		);

		if($types->length > 0) {
			$this->_initialize($types[0]);
		}
	}

	/**
	 * @return array
	 */
	public function getNodes (): array {
		return $this->_nodes;
	}

	/**
	 * @return array
	 */
	public function getAttributes (): array {
		return $this->_attributes;
	}

	/**
	 * @return bool
	 */
	public function isComplex (): bool {
		return (count($this->getNodes()) > 0 || count($this->getAttributes()) > 0);
	}

	/**
	 * @return bool
	 */
	public function isSimple (): bool {
		return (0 == count($this->getNodes()));
	}

    /**
     * @param $DOMNode
     * @return void
     * @throws Exception
     */
	protected function _initialize($DOMNode): void {
		$documentations = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':annotation/' . XSD::NAMESPACE . ':documentation',
			$DOMNode
		);
		if($documentations->length > 0) {
			$this->setDocumentation($documentations[0]->nodeValue);
		}

		$restrictions = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':restriction',
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

		$nodes = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':sequence/' . XSD::NAMESPACE . ':element',
			$DOMNode
		);

		foreach($nodes as $node) {
			$this->_nodes[] = new Node(
				$node,
				$this->getXpath()
			);
		}

		$attributes = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':attribute',
			$DOMNode
		);

		foreach($attributes as $attribute) {
			$this->_attributes[] = new Attribute(
				$attribute,
				$this->getXpath()
			);
		}
	}
}