<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

use InvalidArgumentException;

/**
 * Node interface
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.0.5
 */
interface NodeInterface
{

    /**
     * Return the indent
     *
     * @return string
     */
    public function getIndent(): string;

    /**
     * Set the indent
     *
     * @param  string $indent
     * @return NodeInterface
     */
    public function setIndent(string $indent): NodeInterface;

    /**
     * Return the parent node
     *
     * @return NodeInterface|null
     */
    public function getParent(): NodeInterface|null;

    /**
     * Set the parent node
     *
     * @param  NodeInterface $parent
     * @return NodeInterface
     */
    public function setParent(NodeInterface $parent): NodeInterface;

    /**
     * Add a child to the object
     *
     * @param  Child $c
     * @throws InvalidArgumentException
     * @return NodeInterface
     */
    public function addChild(Child $c): NodeInterface;

    /**
     * Add children to the object
     *
     * @param  mixed $children
     * @throws Exception
     * @return NodeInterface
     */
    public function addChildren(mixed $children): NodeInterface;

    /**
     * Get whether or not the child object has children
     *
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * Get whether or not the child object has children (alias)
     *
     * @return bool
     */
    public function hasChildNodes(): bool;

    /**
     * Get the child nodes of the object
     *
     * @param  int $i
     * @return Child|null
     */
    public function getChild(int $i): Child|null;

    /**
     * Get the child nodes of the object
     *
     * @return array
     */
    public function getChildren(): array;

    /**
     * Get the child nodes of the object (alias)
     *
     * @return array
     */
    public function getChildNodes(): array;

    /**
     * Remove all child nodes from the object
     *
     * @param  int  $i
     * @return void
     */
    public function removeChild(int $i): void;

    /**
     * Remove all child nodes from the object
     *
     * @return void
     */
    public function removeChildren(): void;

}
