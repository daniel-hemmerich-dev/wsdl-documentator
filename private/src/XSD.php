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
	const NAMESPACE = 'xsd';

	/** @var null  */
	protected $_dom = null;

	/** @var null  */
	protected $_schema = null;

	/**
	 * @return DOMDocument
	 */
	public function getDom() : DOMDocument {
		return $this->_dom;
	}

	/**
	 * @param null $dom
	 */
	public function setDom(DOMDocument $dom) {
		$this->_dom = $dom;
	}

	/**
	 * @return null
	 */
	public function getSchema() {
		return $this->_schema;
	}

	/**
	 * @param null $schema
	 */
	public function setSchema($schema) {
		$this->_schema = $schema;
	}

	/**
	 * XSD constructor.
	 *
	 * @param string $xsd
	 */
	public function __construct(string $xsd) {
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
			self::NAMESPACE,
			'http://www.w3.org/2001/XMLSchema'
		);

		$this->setDom($doc);
		parent::__construct($xpath);

		$schema = $this->getXpath()->evaluate(
			'/' . self::NAMESPACE . ':schema'
		);
		$this->setSchema(
			new Schema(
				$schema[0],
				$this->getXpath()
			)
		);
	}
}