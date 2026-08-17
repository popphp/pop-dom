<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
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
    public static function create(string $name, ?string $value = null, array $options = []): Child
    {
        return new self($name, $value, $options);
    }

    /**
     * Static method to parse an XML/HTML string
     *
     * @param  string $string
     * @return Child|array|null
     */
    public static function parseString(string $string): Child|array|null
    {
        if (trim($string) === '') {
            return null;
        }

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
                $attribs = self::extractAttributes($node);

                if ($parent === null) {
                    $parent = new Child($node->nodeName);
                } else {
                    if (($node->nodeType == XML_TEXT_NODE) && ($child !== null)) {
                        $nodeValue = trim($node->nodeValue);
                        if (!empty($nodeValue)) {
                            if (($endElement) && ($child->getParent() !== null) && ($node->previousSibling !== null)) {
                                self::appendSiblingText($child, $node, $nodeValue);
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
                            $parent = self::climbToNamedParent($parent, $node->parentNode->nodeName);
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
        if ($parent === null) {
            return null;
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
     * @return Child|array|null
     */
    public static function parseFile(string $file): Child|array|null
    {
        if (!file_exists($file)) {
            throw new Exception('Error: That file does not exist.');
        }
        return self::parseString(file_get_contents($file));
    }

    /**
     * Extract a DOM node's attributes as a name/value array
     *
     * @param  \DOMNode $node
     * @return array
     */
    private static function extractAttributes(\DOMNode $node): array
    {
        $attribs = [];
        if ($node instanceof \DOMElement) {
            for ($i = 0; $i < $node->attributes->length; $i++) {
                $name = $node->attributes->item($i)->name;
                $attribs[$name] = $node->getAttribute($name);
            }
        }
        return $attribs;
    }

    /**
     * Climb the parent chain from $start until a Child node named $targetName is found
     *
     * @param  AbstractNode|null $start
     * @param  string            $targetName
     * @return AbstractNode|null
     */
    private static function climbToNamedParent(AbstractNode|null $start, string $targetName): AbstractNode|null
    {
        $node = $start;
        while (($node instanceof Child) && ($node->getNodeName() != $targetName)) {
            $node = $node->getParent();
        }
        return $node;
    }

    /**
     * Reattach a stray text node to the appropriate ancestor of $child, based on the DOM node
     * that precedes it as a sibling
     *
     * @param  Child    $child
     * @param  \DOMNode $node
     * @param  string   $nodeValue
     * @return void
     */
    private static function appendSiblingText(Child $child, \DOMNode $node, string $nodeValue): void
    {
        $prev = $node->previousSibling->nodeName;
        $par  = self::climbToNamedParent($child->getParent(), $prev);
        $par  = ($par === null) ? $child->getParent() : $par->getParent();
        $par->addChild(new Child('#text', $nodeValue));
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
            $content = self::normalizeWhiteSpace($content);
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
            $content = self::normalizeWhiteSpace($content);
        }
        return $content;
    }

    /**
     * Collapse whitespace and normalize spacing around sentence punctuation
     *
     * @param  string $content
     * @return string
     */
    private static function normalizeWhiteSpace(string $content): string
    {
        $content = preg_replace('/\s+/', ' ', str_replace(["\n", "\r", "\t"], ["", "", ""], trim($content)));
        $content = preg_replace('/\s*\.\s*/', '. ', $content);
        $content = preg_replace('/\s*\?\s*/', '? ', $content);
        $content = preg_replace('/\s*\!\s*/', '! ', $content);
        $content = preg_replace('/\s*,\s*/', ', ', $content);
        $content = preg_replace('/\s*\:\s*/', ': ', $content);
        $content = preg_replace('/\s*\;\s*/', '; ', $content);
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
     * @param  mixed $value
     * @return Child
     */
    public function setNodeValue(mixed $value = null): Child
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
     * @param  string $name
     * @param  mixed  $value
     * @return Child
     */
    public function setAttribute(string $name, mixed $value = null): Child
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Set an attribute or attributes for the child element object
     *
     * @param  array $attributes
     * @return Child
     */
    public function setAttributes(array $attributes): Child
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
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
        return isset($this->attributes[$name]);
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
     * @return ?string
     */
    public function getAttribute(string $name): ?string
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
     * @param  string $name
     * @return Child
     */
    public function removeAttribute(string $name): Child
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
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
        $ownIndent    = $this->indent ?? str_repeat('    ', $depth);
        $attribs      = '';
        $attribAry    = [];
        $nl           = $this->preserveWhiteSpace ? "\n" : '';
        $leadIndent   = $this->preserveWhiteSpace ? "{$indent}{$ownIndent}" : '';

        $nodeValue = $this->cData ? '<![CDATA[' . $this->nodeValue . ']]>' : $this->nodeValue;

        // Format child attributes, if applicable.
        if ($this->hasAttributes()) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $key => $value) {
                $attribAry[] = $key . "=\"" . htmlspecialchars((string)$value, ENT_QUOTES) . "\"";
            }
            $attribs = ' ' . implode(' ', $attribAry);
        }

        // Initialize the node.
        if ($this->nodeName == '#text') {
            $this->output .= $leadIndent . $nodeValue . $nl;
        } else {
            if (!$inner) {
                $this->output .= $leadIndent . "<{$this->nodeName}{$attribs}";
            }

            if ($indent === null) {
                $indent     = $ownIndent;
                $origIndent = $ownIndent;
            } else {
                $origIndent = $indent . $ownIndent;
            }

            $closeIndent = $this->preserveWhiteSpace ? $origIndent : '';

            // If current child element has child nodes, format and render.
            if (count($this->childNodes) > 0) {
                if (!$inner) {
                    $this->output .= ">" . $nl;
                }
                $newDepth    = $depth + 1;
                $valueIndent = $this->preserveWhiteSpace ? str_repeat('    ', $newDepth) . "{$indent}" : '';

                // Render node value before the child nodes.
                if (!$this->childrenFirst) {
                    if ($nodeValue !== null) {
                        $this->output .= $valueIndent . "{$nodeValue}\n";
                    }
                    foreach ($this->childNodes as $child) {
                        $this->output .= $child->render($newDepth, $indent);
                    }
                    if (!$inner) {
                        $this->output .= $closeIndent . "</{$this->nodeName}>" . $nl;
                    }
                // Else, render child nodes first, then node value.
                } else {
                    foreach ($this->childNodes as $child) {
                        $this->output .= $child->render($newDepth, $indent);
                    }
                    if (!$inner) {
                        if ($nodeValue !== null) {
                            $this->output .= $valueIndent . "{$nodeValue}" . $nl . $closeIndent .
                                "</{$this->nodeName}>" . $nl;
                        } else {
                            $this->output .= $closeIndent . "</{$this->nodeName}>" . $nl;
                        }
                    }
                }
            // Else, render the child node.
            } else {
                if (!$inner) {
                    if (($nodeValue !== null) || ($this->nodeName == 'textarea')) {
                        $this->output .= ">";
                        $this->output .= "{$nodeValue}</{$this->nodeName}>" . $nl;
                    } else {
                        $this->output .= " />" . $nl;
                    }
                } else if (!empty($nodeValue)) {
                    $this->output .= $nodeValue;
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
