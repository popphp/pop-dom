<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

use DOMNodeList;
use DOMNode;

/**
 * Dom iterator class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2026 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.0.7
 */
class DomIterator implements \RecursiveIterator
{

    /**
     * Current position
     * @var int
     */
    protected int $position = 0;

    /**
     * Node List
     * @var ?DOMNodeList
     */
    protected ?DOMNodeList $nodeList = null;

    /**
     * Constructor
     *
     * Instantiate the DOM iterator object
     *
     * @param DOMNode $domNode
     */
    public function __construct(DOMNode $domNode)
    {
        $this->nodeList = $domNode->childNodes;
    }

    /**
     * Get current method
     * @return DOMNode
     */
    public function current(): DOMNode
    {
        return $this->nodeList->item($this->position);
    }

    /**
     * Get children method
     * @return DomIterator
     */
    public function getChildren(): DomIterator
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
     * @return void
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * Rewind method
     * @return void
     */
    public function rewind(): void
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
