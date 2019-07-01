<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 10.08.18
 * Time: 08:59
 */

class Base {
	/** @var null  */
	protected $_xpath = null;

	/**
	 * @return null
	 */
	public function getXpath() : DOMXPath {
		return $this->_xpath;
	}

	/**
	 * @param null $xpath
	 */
	public function setXpath(DOMXPath $xpath) {
		$this->_xpath = $xpath;
	}

	/**
	 * Base constructor.
	 *
	 * @param DOMXPath $DOMXPath
	 */
	public function __construct (DOMXPath $DOMXPath) {
		$this->setXpath($DOMXPath);
	}
}