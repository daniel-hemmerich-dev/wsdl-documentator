<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 27.07.18
 * Time: 11:41
 */

require_once 'Base.php';
require_once 'Schema.php';

class XSD extends Base {
    const NAMESPACE = '';

	/** @var DOMDocument  */
	protected DOMDocument $_dom;

	/** @var Schema  */
	protected Schema $_schema;

	/**
	 * @return DOMDocument
	 */
	public function getDom(): DOMDocument
    {
		return $this->_dom;
	}

	/**
	 * @param DOMDocument $dom
	 */
	public function setDom(DOMDocument $dom) {
		$this->_dom = $dom;
	}

	/**
	 * @return Schema
	 */
	public function getSchema(): Schema {
		return $this->_schema;
	}

	/**
	 * @param Schema $schema
	 */
	public function setSchema(Schema $schema) {
		$this->_schema = $schema;
	}

    /**
     * @param $xsd
     * @throws Exception
     */
	public function __construct($xsd) {
		$doc = new DOMDocument();
		$doc->loadXML(
			mb_convert_encoding(
				$xsd,
				'utf-8'
			)
		);
		$xpath = new DOMXPath(
			$doc
		);
		$xpath->registerNamespace(
			'xsd',
			'http://www.w3.org/2001/XMLSchema'
		);

		$this->setDom($doc);
		parent::__construct($xpath);

		$schema = $this->getXpath()->evaluate(
			'/' . 'xsd' . ':schema'
		);
		$this->setSchema(
			new Schema(
				$schema[0],
				$this->getXpath()
			)
		);
	}
}