<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

use ReturnTypeWillChange;

/**
 * Dom iterator class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2023 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.3.0
 */
class DomIterator implements \RecursiveIterator
{

    /**
     * Current position
     * @var int
     */
    protected $position;

    /**
     * Node List
     * @var \DOMNodeList
     */
    protected $nodeList;

    /**
     * Constructor
     *
     * Instantiate the DOM iterator object
     *
     * @param \DOMNode $domNode
     */
    public function __construct(\DOMNode $domNode)
    {
        $this->position = 0;
        $this->nodeList = $domNode->childNodes;
    }

    /**
     * Get current method
     * @return \DOMNode
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->nodeList->item($this->position);
    }

    /**
     * Get children method
     * @return DomIterator
     */
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        return new self($this->current());
    }

    /**
     * Has children method
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->current()->hasChildNodes();
    }

    /**
     * Key method
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Next method
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    /**
     * Rewind method
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Is valid method
     * @return bool
     */
    public function valid(): bool
    {
        return $this->position < $this->nodeList->length;
    }

}