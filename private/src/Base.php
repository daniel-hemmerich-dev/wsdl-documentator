<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 10.08.18
 * Time: 08:59
 */

class Base {
	/** @var DOMXPath  */
	protected DOMXPath $_xpath;

	/**
	 * @return DOMXPath
	 */
	public function getXpath(): DOMXPath
    {
		return $this->_xpath;
	}

	/**
	 * @param DOMXPath $xpath
	 */
	public function setXpath(DOMXPath $xpath): void
    {
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