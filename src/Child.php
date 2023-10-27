<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

use RecursiveIteratorIterator;

/**
 * Dom child class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class Child extends AbstractNode
{

    /**
     * Child element node name
     * @var ?string
     */
    protected ?string $nodeName = null;

    /**
     * Child element node value
     * @var ?string
     */
    protected ?string $nodeValue = null;

    /**
     * Child element node value CDATA flag
     * @var bool
     */
    protected bool $cData = false;

    /**
     * Flag to render children before node value or not
     * @var bool
     */
    protected bool $childrenFirst = false;

    /**
     * Child element attributes
     * @var array
     */
    protected array $attributes = [];

    /**
     * Flag to preserve whitespace
     * @var bool
     */
    protected bool $preserveWhiteSpace = true;

    /**
     * Constructor
     *
     * Instantiate the DOM element object
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  array   $options
     */
    public function __construct(string $name, ?string $value = null, array $options = [])
    {
        $this->nodeName  = $name;
        $this->nodeValue = $value;

        if (isset($options['cData'])) {
            $this->cData = (bool)$options['cData'];
        }
        if (isset($options['childrenFirst'])) {
            $this->childrenFirst = (bool)$options['childrenFirst'];
        }
        if (isset($options['indent'])) {
            $this->indent = (string)$options['indent'];
        }
        if (isset($options['attributes'])) {
            $this->setAttributes($options['attributes']);
        }
        if (isset($options['whitespace'])) {
            $this->preserveWhiteSpace($options['whitespace']);
        }
    }

    /**
     * Static factory method to create a child object
     *
     * @param  string  $name
     * @param  ?string $value
     * @param  array   $options
     * @return Child
     */
    public static function create(string $name, ?string $value = null, array $options = [])
    {
        return new self($name, $value, $options);
    }

    /**
     * Static method to parse an XML/HTML string
     *
     * @param  string $string
     * @return Child|array
     */
    public static function parseString(string $string): Child|array
    {
        $doc = new \DOMDocument();
        $doc->loadHTML($string);

        $dit = new RecursiveIteratorIterator(
            new DomIterator($doc),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $parent     = null;
        $child      = null;
        $lastDepth  = 0;
        $endElement = null;
        $partial    = ((stripos($string, '<html') === false) || (stripos($string, '<body') === false));

        foreach($dit as $node) {
            if (($node->nodeType == XML_ELEMENT_NODE) || ($node->nodeType == XML_TEXT_NODE)) {
                $attribs = [];
                if ($node->attributes !== null) {
                    for ($i = 0; $i < $node->attributes->length; $i++) {
                        $name = $node->attributes->item($i)->name;
                        $attribs[$name] = $node->getAttribute($name);
                    }
                }
                if ($parent === null) {
                    $parent = new Child($node->nodeName);
                } else {
                    if (($node->nodeType == XML_TEXT_NODE) && ($child !== null)) {
                        $nodeValue = trim($node->nodeValue);
                        if (!empty($nodeValue)) {
                            if (($endElement) && ($child->getParent() !== null) && ($node->previousSibling !== null)) {
                                $prev = $node->previousSibling->nodeName;
                                $par  = $child->getParent();
                                while (($par !== null) && ($prev != $par->getNodeName())) {
                                    $par = $par->getParent();
                                }
                                if ($par === null) {
                                    $par = $child->getParent();
                                } else {
                                    $par = $par->getParent();
                                }
                                $par->addChild(new Child('#text', $nodeValue));
                            } else {
                                $child->setNodeValue($nodeValue);
                                $endElement = true;
                            }
                        }
                    } else {
                        // down
                        if ($dit->getDepth() > $lastDepth) {
                            if ($child !== null) {
                                $parent = $child;
                            }
                            $child  = new Child($node->nodeName);
                            $parent->addChild($child);
                            $endElement = false;
                        // up
                        } else if ($dit->getDepth() < $lastDepth) {
                            while ($parent->getNodeName() != $node->parentNode->nodeName) {
                                $parent = $parent->getParent();
                            }
                            //$parent = $parent->getParent();
                            $child  = new Child($node->nodeName);
                            $parent->addChild($child);
                            $endElement = false;
                            // next (sibling)
                        } else if ($dit->getDepth() == $lastDepth) {
                            $child  = new Child($node->nodeName);
                            $parent->addChild($child);
                            $endElement = false;
                        }
                        if (!empty($attribs)) {
                            $child->setAttributes($attribs);
                        }
                        $lastDepth = $dit->getDepth();
                    }
                }
            }
        }
        while ($parent->getParent() !== null) {
            $parent = $parent->getParent();
        }

        if ($partial) {
            $parent = $parent->getChild(0);
            if (strtolower($parent->getNodeName()) == 'body') {
                $parent = $parent->getChildNodes();
            }
        }

        return $parent;
    }

    /**
     * Static method to parse an XML/HTML string from a file
     *
     * @param  string $file
     * @throws Exception
     * @return Child
     */
    public static function parseFile(string $file): Child
    {
        if (!file_exists($file)) {
            throw new Exception('Error: That file does not exist.');
        }
        return self::parseString(file_get_contents($file));
    }

    /**
     * Return the child node name
     *
     * @return string|null
     */
    public function getNodeName(): string|null
    {
        return $this->nodeName;
    }

    /**
     * Return the child node value
     *
     * @return string|null
     */
    public function getNodeValue(): string|null
    {
        return $this->nodeValue;
    }

    /**
     * Return the child node content, including tags, etc
     *
     * @param  bool $ignoreWhiteSpace
     * @return string
     */
    public function getNodeContent(bool $ignoreWhiteSpace = false): string
    {
        $content = $this->render(0, null, true);
        if ($ignoreWhiteSpace) {
            $content = preg_replace('/\s+/', ' ', str_replace(["\n", "\r", "\t"], ["", "", ""], trim($content)));
            $content = preg_replace('/\s*\.\s*/', '. ', $content);
            $content = preg_replace('/\s*\?\s*/', '? ', $content);
            $content = preg_replace('/\s*\!\s*/', '! ', $content);
            $content = preg_replace('/\s*,\s*/', ', ', $content);
            $content = preg_replace('/\s*\:\s*/', ': ', $content);
            $content = preg_replace('/\s*\;\s*/', '; ', $content);
        }
        return $content;
    }

    /**
     * Return the child node content, including tags, etc
     *
     * @param  bool $ignoreWhiteSpace
     * @return string
     */
    public function getTextContent(bool $ignoreWhiteSpace = false): string
    {
        $content = strip_tags($this->render(0, null, true));

        if ($ignoreWhiteSpace) {
            $content = preg_replace('/\s+/', ' ', str_replace(["\n", "\r", "\t"], ["", "", ""], trim($content)));
            $content = preg_replace('/\s*\.\s*/', '. ', $content);
            $content = preg_replace('/\s*\?\s*/', '? ', $content);
            $content = preg_replace('/\s*\!\s*/', '! ', $content);
            $content = preg_replace('/\s*,\s*/', ', ', $content);
            $content = preg_replace('/\s*\:\s*/', ': ', $content);
            $content = preg_replace('/\s*\;\s*/', '; ', $content);
        }
        return $content;
    }

    /**
     * Set the child node name
     *
     * @param  string $name
     * @return Child
     */
    public function setNodeName(string $name): Child
    {
        $this->nodeName = $name;
        return $this;
    }

    /**
     * Set the child node value
     *
     * @param  string $value
     * @return Child
     */
    public function setNodeValue(string $value): Child
    {
        $this->nodeValue = $value;
        return $this;
    }

    /**
     * Add to the child node value
     *
     * @param  string $value
     * @return Child
     */
    public function addNodeValue(string $value): Child
    {
        $this->nodeValue .= $value;
        return $this;
    }

    /**
     * Set the child node value as CDATA
     *
     * @param  bool $cData
     * @return Child
     */
    public function setAsCData(bool $cData = true): Child
    {
        $this->cData = $cData;
        return $this;
    }

    /**
     * Determine if the child node value is CDATA
     *
     * @return bool
     */
    public function isCData(): bool
    {
        return $this->cData;
    }

    /**
     * Set an attribute for the child element object
     *
     * @param  string $a
     * @param  string $v
     * @return Child
     */
    public function setAttribute(string $a, string $v): Child
    {
        $this->attributes[$a] = $v;
        return $this;
    }

    /**
     * Set an attribute or attributes for the child element object
     *
     * @param  array $a
     * @return Child
     */
    public function setAttributes(array $a): Child
    {
        foreach ($a as $name => $value) {
            $this->attributes[$name] = $value;
        }
        return $this;
    }

    /**
     * Determine if the child object has an attribute
     *
     * @param  string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return (isset($this->attributes[$name]));
    }

    /**
     * Determine if the child object has attributes
     *
     * @return bool
     */
    public function hasAttributes(): bool
    {
        return (count($this->attributes) > 0);
    }

    /**
     * Get the attribute of the child object
     *
     * @param  string $name
     * @return string|null
     */
    public function getAttribute(string $name): string|null
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Get the attributes of the child object
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Remove an attribute from the child element object
     *
     * @param  string $a
     * @return Child
     */
    public function removeAttribute(string $a): Child
    {
        if (isset($this->attributes[$a])) {
            unset($this->attributes[$a]);
        }
        return $this;
    }

    /**
     * Determine if child nodes render first, before the node value
     *
     * @return bool
     */
    public function isChildrenFirst(): bool
    {
        return $this->childrenFirst;
    }

    /**
     * Set whether child nodes render first, before the node value
     *
     * @param  bool $first
     * @return Child
     */
    public function setChildrenFirst(bool $first = true): Child
    {
        $this->childrenFirst = $first;
        return $this;
    }

    /**
     * Set whether to preserve whitespace
     *
     * @param  bool $preserve
     * @return Child
     */
    public function preserveWhiteSpace(bool $preserve = true): Child
    {
        $this->preserveWhiteSpace = $preserve;
        return $this;
    }

    /**
     * Render the child and its child nodes.
     *
     * @param  int     $depth
     * @param  ?string $indent
     * @param  bool    $inner
     * @return string|null
     */
    public function render(int $depth = 0, ?string $indent = null, bool $inner = false): string|null
    {
        // Initialize child object properties and variables.
        $this->output = '';
        $this->indent = ($this->indent === null) ? str_repeat('    ', $depth) : $this->indent;
        $attribs      = '';
        $attribAry    = [];

        if ($this->cData) {
            $this->nodeValue = '<![CDATA[' . $this->nodeValue . ']]>';
        }

        // Format child attributes, if applicable.
        if ($this->hasAttributes()) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $key => $value) {
                $attribAry[] = $key . "=\"" . $value . "\"";
            }
            $attribs = ' ' . implode(' ', $attribAry);
        }

        // Initialize the node.
        if ($this->nodeName == '#text') {
            $this->output .= ((!$this->preserveWhiteSpace) ?
                    '' : "{$indent}{$this->indent}") . $this->nodeValue . ((!$this->preserveWhiteSpace) ? '' : "\n");
        } else {
            if (!$inner) {
                $this->output .= ((!$this->preserveWhiteSpace) ?
                        '' : "{$indent}{$this->indent}") . "<{$this->nodeName}{$attribs}";
            }

            if (($indent === null) && ($this->indent !== null)) {
                $indent     = $this->indent;
                $origIndent = $this->indent;
            } else {
                $origIndent = $indent . $this->indent;
            }

            // If current child element has child nodes, format and render.
            if (count($this->childNodes) > 0) {
                if (!$inner) {
                    $this->output .= ">";
                    if ($this->preserveWhiteSpace) {
                        $this->output .= "\n";
                    }
                }
                $newDepth = $depth + 1;

                // Render node value before the child nodes.
                if (!$this->childrenFirst) {
                    if ($this->nodeValue !== null) {
                        $this->output .= ((!$this->preserveWhiteSpace) ?
                                '' : str_repeat('    ', $newDepth) . "{$indent}") . "{$this->nodeValue}\n";
                    }
                    foreach ($this->childNodes as $child) {
                        $this->output .= $child->render($newDepth, $indent);
                    }
                    if (!$inner) {
                        if (!$this->preserveWhiteSpace) {
                            $this->output .= "</{$this->nodeName}>";
                        } else {
                            $this->output .= "{$origIndent}</{$this->nodeName}>\n";
                        }
                    }
                // Else, render child nodes first, then node value.
                } else {
                    foreach ($this->childNodes as $child) {
                        $this->output .= $child->render($newDepth, $indent);
                    }
                    if (!$inner) {
                        if ($this->nodeValue !== null) {
                            $this->output .= ((!$this->preserveWhiteSpace) ?
                                    '' : str_repeat('    ', $newDepth) . "{$indent}") .
                                "{$this->nodeValue}" . ((!$this->preserveWhiteSpace) ?
                                    '' : "\n{$origIndent}") . "</{$this->nodeName}>" . (($this->preserveWhiteSpace) ? '' : "\n");
                        } else {
                            $this->output .= ((!$this->preserveWhiteSpace) ?
                                    '' : "{$origIndent}") . "</{$this->nodeName}>" . ((!$this->preserveWhiteSpace) ? '' : "\n");
                        }
                    }
                }
            // Else, render the child node.
            } else {
                if (!$inner) {
                    if (($this->nodeValue !== null) || ($this->nodeName == 'textarea')) {
                        $this->output .= ">";
                        $this->output .= "{$this->nodeValue}</{$this->nodeName}>" . ((!$this->preserveWhiteSpace) ? '' : "\n");
                    } else {
                        $this->output .= " />";
                        if ($this->preserveWhiteSpace) {
                            $this->output .= "\n";
                        }
                    }
                } else if (!empty($this->nodeValue)) {
                    $this->output .= $this->nodeValue;
                }
            }
        }

        return $this->output;
    }

    /**
     * Render Dom child object to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
