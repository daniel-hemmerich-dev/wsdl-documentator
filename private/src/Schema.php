<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 27.07.18
 * Time: 12:23
 */

require_once 'Base.php';
require_once 'Node.php';

class Schema extends Base {
	/** @var string  */
	protected string $_xmlns = '';

	/** @var array  */
	protected array $nodes = [];

	/** @var string  */
	protected string $_documentation = '';

	/**
	 * @return string
	 */
	public function getXmlns(): string {
		return $this->_xmlns;
	}

	/**
	 * @param string $xmlns
	 */
	public function setXmlns(string $xmlns) {
		$this->_xmlns = $xmlns;
	}

	/**
	 * @return array
	 */
	public function getNodes(): array {
		return $this->nodes;
	}

	/**
	 * @return string
	 */
	public function getDocumentation(): string {
		return $this->_documentation;
	}

	/**
	 * @param string $documentation
	 */
	public function setDocumentation(string $documentation) {
		$this->_documentation = $documentation;
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
		$this->setXmlns($DOMNode->getAttribute('xlmns'));
		$this->_initialize($DOMNode);
	}

    /**
     * @param DOMNode $DOMNode
     * @return void
     * @throws Exception
     */
	protected function _initialize(DOMNode $DOMNode): void {
		$documentations = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':annotation/' . XSD::NAMESPACE . ':documentation',
			$DOMNode
		);
		if($documentations->length > 0) {
			$this->setDocumentation($documentations[0]->nodeValue);
		}

		$this->_initializeElements($DOMNode);
	}

    /**
     * @param DOMNode $DOMNode
     * @return void
     * @throws Exception
     */
	protected function _initializeElements(DOMNode $DOMNode): void {
		$nodes = $this->getXpath()->evaluate(
			XSD::NAMESPACE . ':element',
			$DOMNode
		);
		foreach($nodes as $node) {
			$this->nodes[] = new Node(
				$node,
				$this->getXpath()
			);
		}
	}
}