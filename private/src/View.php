<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 10.08.18
 * Time: 09:37
 */

class View {
	/** @var int  */
	const INDEND_OFFSET = 4;

	/** @var string  */
	protected string $_html = '';

	/** @var string  */
	protected string $_selected_version = '';

	/** @var string  */
	protected string $_selected_dialect = '';

	/** @var string  */
	protected string $_pdf = '';

	/** @var array  */
	protected array $_parameter = [];

	/** @var array  */
	protected array $_examples = [];

	/**
	 * @return string
	 */
	public function getHtml(): string
    {
		return $this->_html;
	}

	/**
	 * @return string
	 */
	public function getSelectedVersion(): string
    {
		return $this->_selected_version;
	}

	/**
	 * @param string $selected_version
	 */
	public function setSelectedVersion(string $selected_version): void
    {
		$this->_selected_version = $selected_version;
	}

	/**
	 * @return string
	 */
	public function getSelectedDialect(): string
    {
		return $this->_selected_dialect;
	}

	/**
	 * @param string $selected_dialect
	 */
	public function setSelectedDialect(string $selected_dialect): void
    {
		$this->_selected_dialect = $selected_dialect;
	}

	/**
	 * @return string
	 */
	public function getPdf(): string
    {
		return $this->_pdf;
	}

    /**
     * View constructor.
     *
     * @param string $title
     * @param array $versions
     * @param string $selected_version
     * @param string $selected_dialect
     */
	public function __construct(
		string $title,
		array $versions,
		string $selected_version,
		string $selected_dialect
	)
	{
		$this->setSelectedVersion($selected_version);
		$this->setSelectedDialect($selected_dialect);
		if(!isset($versions[$this->getSelectedVersion()][$this->getSelectedDialect()])) {
			$this->setSelectedDialect(key($versions[$this->getSelectedVersion()]));
		}

		$this->addContent("<!DOCTYPE html>\n");
		$this->addContent("<html lang=\"en\">\n");

		$this->_initializeHedaer(
			$title
		);

		$this->_initializeBody(
			$title,
			$versions
		);

		$this->addContent("</html>\n");
	}

	/**
	 * @param string $content
	 * @param bool $print
	 */
	protected function addContent(
		string $content,
		bool $print = true
	): void
    {
		$this->_html .= $content;
		if($print) {
			$this->_pdf .= $content;
		}
	}

	/**
	 * @param string $title
	 */
	protected function _initializeHedaer(
		string $title
	): void
    {
		$this->addContent("\t<head>\n");
		//$this->addContent("\t\t<link rel=\"stylesheet\" type=\"text/css\" href=\"private/vendor/twbs/bootstrap/dist/css/bootstrap.min.css\">\n");
		$this->addContent("\t\t<link rel=\"stylesheet\" type=\"text/css\" href=\"index.css\">\n");
		$this->addContent("\t\t<meta charset=\"utf-8\"/>\n");
		$this->addContent("\t\t<title>" . $title . "</title>\n");
		$this->addContent("\t</head>\n");
	}

	/**
	 * @param string $title
	 * @param array $versions
	 */
	protected function _initializeBody(
		string $title,
		array $versions
	): void
    {
		$this->addContent("\t<body>\n");

		$this->addContent("\t\t<h1>" . $title . "</h1>\n");
		$this->addContent("\t\t<h2>Versions</h2>\n", false);
		$this->addContent("\t\t<form action='index.php'>\n", false);

		$this->addContent("\t\t\t<select name='version'>\n", false);
		$dialects_per_version = [];
		foreach($versions as $id => $dialects) {
			$selected = '';
			if($id == $this->getSelectedVersion()) {
				$selected = " selected='selected'";
				$dialects_per_version = array_keys($dialects);
			}
			$this->addContent("\t\t\t\t<option" . $selected . ">" . $id . "</option>\n", false);
		}
		$this->addContent("\t\t\t</select>\n", false);

		$this->addContent("\t\t\t<select name='dialect'>\n", false);
		foreach($dialects_per_version as $dialect) {
			$selected = '';
			if($dialect == $this->getSelectedDialect()) {
				$selected = " selected='selected'";
			}
			$this->addContent("\t\t\t\t<option" . $selected . ">" . $dialect . "</option>\n", false);
		}
		$this->addContent("\t\t\t</select>\n", false);

		$this->addContent("\t\t\t<input type='submit' name='select_version' value='Select Version'>\n", false);
		$this->addContent("\t\t</form><br>\n", false);
		$query_string = http_build_query($_GET);
		$action = 'index.php';
		if('' != $query_string) {
			$action .= '?' . $query_string;
		}
		$this->addContent("\t\t<form method='post' action=$action>\n", false);
		$this->addContent("\t\t\t<input type='submit' name='export' value='Export'>\n", false);
		$this->addContent("\t\t</form><br>\n", false);
		$this->addContent("\t\t<h2>Legend</h2>\n");
		$this->addContent("\t\t\t<p id='legend-question-mark'>? = optional</p><p id='legend-plus'>+ = one or more occurances</p><p id='legend-star'>* = zero or more occurances</p><p id='legend-attribute'>@ = Attribute</p><br>\n");

		$this->_initiliazeMethods($versions[$this->getSelectedVersion()][$this->getSelectedDialect()]);

		$this->addContent("\t</body>\n");
	}

	/**
	 * @param array $version
	 */
	protected function _initiliazeMethods(array $version): void
    {
		$this->addContent("\t\t<h2>Methods</h2>\n");

		foreach($version['methods'] as $name => $method) {
			$this->addContent("\t\t<h3><a href='" . "#" . "$name'>$name</a></h3>\n");
		}

		$this->addContent("\t\t<br><br>\n");
		foreach($version['methods'] as $name => $method) {
			$this->addContent("\t\t<h3 id='$name'>$name</h3>\n");

			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Description'>Description</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Request-Tree'>Request-Tree</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Request-Examples'>Request-Examples</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Request-Elements'>Request-Elements</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Response-Tree'>Response-Tree</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Response-Examples'>Response-Examples</a></h4>\n");
			$this->addContent("\t\t<h4><a href='" . "#" . "$name-Response-Elements'>Response-Elements</a></h4><br>\n");

			$this->addContent("\t\t<h4 id='" . $name . "-Description" . "'>$name-Description</h4>\n");
			$this->addContent("\t\t<p>" . $method['request']->getSchema()->getDocumentation() . "</p><br>\n");

			if(isset($method['request'])) {
				$this->addContent("\t\t<h4 id='" . $name . "-Request-Tree" . "'>$name-Request-Tree</h4>\n");
				$this->addContent("\t\t<div>\n");
				$this->_initializeSchema(
					$name,
					'request',
					$method['request']
				);
				$this->addContent("\t\t</div><br>\n");

				$this->addContent("\t\t<h4 id='" . $name . "-Request-Examples" . "'>$name-Request-Examples</h4>\n");
				$this->_initializeExample($method['request']);

				$this->addContent("\t\t<h4 id='" . $name . "-Request-Elements" . "'>$name-Request-Elements</h4>\n");
				$this->_initializeParameterOverview(
					$name,
					'request'
				);
				$this->_parameter = [];
			}

			if(isset($method['response'])) {
				$this->addContent("\t\t<h4 id='" . $name . "-Response-Tree" . "'>$name-Response-Tree</h4>\n");
				$this->addContent("\t\t<div>\n");
				$this->_initializeSchema(
					$name,
					'response',
					$method['response']
				);
				$this->addContent("\t\t</div><br>\n");

				$this->addContent("\t\t<h4 id='" . $name . "-Response-Examples" . "'>$name-Response-Examples</h4>\n");
				$this->_initializeExample($method['response']);

				$this->addContent("\t\t<h4 id='" . $name . "-Response-Elements" . "'>$name-Response-Elements</h4>\n");
				$this->_initializeParameterOverview(
					$name,
					'response'
				);
				$this->_parameter = [];
			}

			$this->addContent("\t\t<br><br>\n");
		}
	}

	/**
	 * @param string $method
	 * @param string $suffix
	 * @param XSD $xsd
	 */
	protected function _initializeSchema(
	    string $method,
		string $suffix,
		XSD    $xsd
	): void
    {
		$schema = $xsd->getSchema();
		$nodes = $schema->getNodes();

		foreach($nodes as $node) {
			$this->_initializeNodes(
				$method,
				$suffix,
				$node,
				0
			);
		}
	}

	/**
	 * @param string $method
	 * @param string $suffix
	 * @param Node $node
	 * @param int $intend
	 */
	protected function _initializeNodes (
		string $method,
		string $suffix,
		Node   $node,
		int $intend
	): void
    {
		$this->addContent($this->_noBackspaces($intend) . '<a href="#' . $method . '-' . $suffix . '-parameter-element-' . $node->getName() . '">' . $node->getName() . '</a>');

		if(0 == $node->getMinOccurances()) {
			if(99999 != $node->getMaxOccurances() && 1 != $node->getMaxOccurances()) {
				$this->addContent('<span>' . $this->_noBackspaces(1) . '[0, '. $node->getMaxOccurances() . ']</span>');
			} else {
				$this->addContent('<a href="#legend-star">' . $this->_noBackspaces(1) . '*</a>');
			}
		} else if(1 == $node->getMinOccurances()) {
			if(99999 != $node->getMaxOccurances() && 1 != $node->getMaxOccurances()) {
				$this->addContent('<span>' . $this->_noBackspaces(1) . '[1, '. $node->getMaxOccurances() . ']</span>');
			} else if (1 != $node->getMaxOccurances()) {
				$this->addContent('<a href="#legend-plus">' . $this->_noBackspaces(1) . '+</a>');
			}
		} else {
			$this->addContent('<span>' . $this->_noBackspaces(1) . '[' . $node->getMinOccurances() . ', ' . $node->getMaxOccurances() . ']</span>');
		}

		$this->_parameter[$node->getName()] = [
			'type'        =>	$node->getType(),
			'restriction' =>	$node->getRestriction(),
			'example'     =>	$node->getExample(),
			'description' =>	$node->getDocumentation()
		];

		$attributes = $node->getAttributes();
		$attribute_count = count($attributes);
		if($attribute_count > 0) {
			$this->addContent('<span>' . $this->_noBackspaces(2) . '(</span>');
		}

		foreach($attributes as $attribute_number => $attribute) {
			$this->addContent('<a href="#' . $method . '-' . $suffix . '-parameter-attribute-' . $attribute->getName() . '">' . $attribute->getName() . '</a>');
			if('' == $attribute->getUse()) {
				$this->addContent('<a href="#legend-question-mark">' . $this->_noBackspaces(1) . '?</a>');
			}

			if(($attribute_count - 1) != $attribute_number) {
				$this->addContent('<span>, </span>');
			}

			$this->_parameter['@' . $attribute->getName()] = [
				'type'        =>	$attribute->getType(),
				'restriction' =>	$attribute->getRestriction(),
				'example'     =>	$attribute->getExample(),
				'description' =>	$attribute->getDocumentation()
			];
		}

		if($attribute_count > 0) {
			$this->addContent('<span>)</span>');
		}

		$childNodes = $node->getNodes();
		foreach($childNodes as $childNode) {
			$this->addContent("<br>");
			$this->_initializeNodes(
				$method,
				$suffix,
				$childNode,
				$intend + self::INDEND_OFFSET
			);
		}
	}

    /**
     * @param DOMNode $DOMNode
     */
	protected function _checkNodeExamples(DOMNode $DOMNode): void
    {
		$examples = [];
		foreach($DOMNode->childNodes as $index => $child) {
			if($this->_parseNodeExamples(
				$child,
                $index
			)) {
				$examples[] = $DOMNode->nodeName;
			}
		}

		if(count($examples) > 0) {
			exit(print_r($examples, true));
		}
	}

	/**
	 * @param XSD $xsd
	 */
	protected function _initializeExample(XSD $xsd): void
    {
		$schema = $xsd->getSchema();
		$nodes = $schema->getNodes();

		$example_index = 0;
		while(true) {
			$examples = [];
			foreach($nodes as $node) {
				if($this->_parseNodeExamples(
					$node,
					$example_index
				)) {
					$examples[] = $node;
				}
			}
			if(0 == count($examples)) {
				break;
			}
			
			$this->addContent("\t\t<div class='example'>\n");
			$this->addContent("\t\t" . htmlspecialchars('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>') . "<br>\n");
			foreach($examples as $node) {
				$this->_initializeNodeExamples(
					$node,
					$example_index,
					0
				);
			}
			$this->addContent("\t\t</div><br>\n");
			if(count($examples) > 0) {
				$example_index++;
			}
		}
	}

	/**
	 * @param Node $node
	 * @param int $example
	 *
	 * @return bool
	 */
	protected function _parseNodeExamples (
		Node $node,
        int $example
	): bool
    {
		if($node->isSimple()) {
			return $node->hasExample($example);
		} else {
			foreach($node->getNodes() as $childNode) {
				if($this->_parseNodeExamples(
					$childNode,
					$example
				)) {
					return true;
				}
			}
			return false;
		}
	}

	/**
	 * @param Node $node
	 * @param int $example_index
	 * @param int $intend
	 *
	 * @return bool
	 */
	protected function _initializeNodeExamples (
		Node $node,
		int $example_index,
		int $intend
	): bool
    {
		if($node->isSimple()) {
			if(!$node->hasExample($example_index)) {
				return false;
			}
		} else if(!$this->_parseNodeExamples(
			$node,
			$example_index
		)) {
			return false;
		}

		$this->addContent($this->_noBackspaces($intend) . htmlspecialchars('<' . $node->getName()));

		$attributes = $node->getAttributes();
		foreach($attributes as $attribute) {
			if(!$attribute->hasExample($example_index)) {
				continue;
			}
			$this->addContent(htmlspecialchars(' ' . $attribute->getName() . '="' . $attribute->getExampleByIndex($example_index) . '"'));
		}

		$this->addContent(htmlspecialchars('>'));

		if($node->isComplex()) {
			$this->addContent("<br>");
		}

		$childNodes = $node->getNodes();
		foreach($childNodes as $childNode) {
			$examples_available = $this->_initializeNodeExamples(
				$childNode,
				$example_index,
				$intend + self::INDEND_OFFSET
			);
			if($examples_available) {
				$this->addContent("<br>");
			}
		}

		if(count($childNodes) > 0) {
			$this->addContent($this->_noBackspaces($intend) . htmlspecialchars('</' . $node->getName() . '>') . "\n");
		} else {
			$this->addContent(htmlspecialchars($node->getExampleByIndex($example_index) . '</' . $node->getName() . '>') . "\n");
		}
		//$this->addContent("<br>");

		return true;
	}

	/**
	 * @param string $methodname
	 * @param string $suffix
	 */
	protected function _initializeParameterOverview(
		string $methodname,
		string $suffix
	): void
    {
		ksort($this->_parameter);

		$this->addContent("\t\t<table>\n");
		$this->addContent("\t\t\t<tr>\n");
		$this->addContent("\t\t\t<th>Name</th>\n");
		$this->addContent("\t\t\t<th>Type</th>\n");
		$this->addContent("\t\t\t<th>Restriction</th>\n");
		$this->addContent("\t\t\t<th>Examples</th>\n");
		$this->addContent("\t\t\t<th>Description</th>\n");
		$this->addContent("\t\t\t</tr>\n");

		foreach($this->_parameter as $name => $parameter) {
			$id = $methodname . '-' . $suffix . '-parameter-' . ('@' == $name[0] ? 'attribute' : 'element') . '-' . ('@' == $name[0] ? substr($name, 1) : $name);
			$this->addContent("\t\t\t<tr>\n");
			$this->addContent("\t\t\t<td id='$id'>$name</td>\n");
			$this->addContent("\t\t\t<td>{$parameter['type']}</td>\n");
			$this->addContent("\t\t\t<td>{$parameter['restriction']}</td>\n");
			$this->addContent("\t\t\t<td>{$parameter['example']}</td>\n");
			$this->addContent("\t\t\t<td>{$parameter['description']}</td>\n");
			$this->addContent("\t\t\t</tr>\n");
		}

		$this->addContent("\t\t</table><br>\n");
	}

	/**
	 * @param int $indent
	 *
	 * @return string
	 */
	protected function _noBackspaces (int $indent): string
    {
		return implode(
			'',
			array_fill(
				0,
				$indent,
				'&nbsp;'
			)
        );
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->_html;
	}

    /**
     * @param array $parameter
     * @return string
     */
	public function createURL(array $parameter): string
    {
        if(false !== stripos(
                "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
                'https://tcxml.de-dc.thomascook.com'
            )) {
            return 'https://tcxml.de-dc.thomascook.com/index.php?' . http_build_query(
                    $parameter
                );
        } else {
            return 'http://0.0.0.0:8080/index.php?' . http_build_query(
                    $parameter
                );
        }
    }
}