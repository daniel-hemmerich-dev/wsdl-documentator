<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 08.09.18
 * Time: 22:52
 */

require_once 'Base.php';

class Element extends Base {
	/** @var string  */
	const EXAMPLE_REGEX = '/\[examples:\s*(.+)\]/mi';

	/** @var string  */
	const EXAMPLE_DELIMITER = ',';

	/** @var string  */
	const EXAMPLE_EMPTY = '';

	/** @var string  */
	protected string $_example = '';

	/** @var array  */
	protected array $_examples = [];

	/** @var string  */
	protected string $_documentation = '';

	/**
	 * @return string
	 */
	public function getExample(): string {
		return $this->_example;
	}

	/**
	 * @param string $example
	 */
	public function setExample(string $example) {
		$examples = explode(
			self::EXAMPLE_DELIMITER,
			$example
		);
		if(isset($examples[0]) && (self::EXAMPLE_EMPTY != $examples[0])) {
			$this->setExamples($examples);
			$this->_example = $example;
		}
	}

	/**
	 * @param int $index
	 *
	 * @return bool
	 */
	public function hasExample(int $index): bool {
		return isset($this->_examples[$index]) && (self::EXAMPLE_EMPTY != $this->_examples[$index]);
	}

	/**
	 * @return array
	 */
	public function getExamples(): array {
		return $this->_examples;
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public function getExampleByIndex(int $index): string {
		return $this->_examples[$index] ?: '';
 	}

	/**
	 * @param array $examples
	 */
	public function setExamples(array $examples) {
		$this->_examples = $examples;
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
		$this->_documentation = preg_replace(
			self::EXAMPLE_REGEX,
			'',
			$documentation
		);
	}

	/**
	 * @param DOMNode $DOMNode
	 *
	 * @return string
	 */
	protected function _parseExample(DOMNode $DOMNode): string  {
		$example = [];
		preg_match(
			self::EXAMPLE_REGEX,
			$DOMNode->nodeValue,
			$example
		);

        return $example[1] ?? '';
	}
}