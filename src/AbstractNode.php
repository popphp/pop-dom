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

use InvalidArgumentException;

/**
 * Abstract node class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
abstract class AbstractNode implements NodeInterface
{

    /**
     * Object child nodes
     * @var array
     */
    protected array $childNodes = [];

    /**
     * Indentation for formatting purposes
     * @var ?string
     */
    protected ?string $indent = null;

    /**
     * Child output
     * @var ?string
     */
    protected ?string $output = null;

    /**
     * Parent node
     * @var ?AbstractNode
     */
    protected ?AbstractNode $parent = null;

    /**
     * Return the indent
     *
     * @return string
     */
    public function getIndent(): string
    {
        return $this->indent;
    }

    /**
     * Set the indent
     *
     * @param  string $indent
     * @return AbstractNode
     */
    public function setIndent(string $indent): AbstractNode
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Return the parent node
     *
     * @return AbstractNode|null
     */
    public function getParent(): AbstractNode|null
    {
        return $this->parent;
    }

    /**
     * Set the parent node
     *
     * @param  AbstractNode $parent
     * @return AbstractNode
     */
    public function setParent(NodeInterface $parent): AbstractNode
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Add a child to the object
     *
     * @param  Child $c
     * @throws InvalidArgumentException
     * @return AbstractNode
     */
    public function addChild(Child $c): AbstractNode
    {
        $c->setParent($this);
        $this->childNodes[] = $c;
        return $this;
    }

    /**
     * Add children to the object
     *
     * @param  mixed $children
     * @throws Exception
     * @return AbstractNode
     */
    public function addChildren(mixed $children): AbstractNode
    {
        if (is_array($children)) {
            foreach ($children as $child) {
                $this->addChild($child);
            }
        } else if ($children instanceof Child) {
            $this->addChild($children);
        } else {
            throw new Exception(
                'Error: The parameter passed must be an instance of Pop\Dom\Child or an array of Pop\Dom\Child instances.'
            );
        }

        return $this;
    }

    /**
     * Get whether or not the child object has children
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return (count($this->childNodes) > 0);
    }

    /**
     * Get whether or not the child object has children (alias)
     *
     * @return bool
     */
    public function hasChildNodes(): bool
    {
        return (count($this->childNodes) > 0);
    }

    /**
     * Get the child nodes of the object
     *
     * @param  int $i
     * @return Child|null
     */
    public function getChild(int $i): Child|null
    {
        return $this->childNodes[(int)$i] ?? null;
    }

    /**
     * Get the child nodes of the object
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->childNodes;
    }

    /**
     * Get the child nodes of the object (alias)
     *
     * @return array
     */
    public function getChildNodes(): array
    {
        return $this->childNodes;
    }

    /**
     * Remove all child nodes from the object
     *
     * @param  int  $i
     * @return void
     */
    public function removeChild(int $i): void
    {
        if (isset($this->childNodes[$i])) {
            unset($this->childNodes[$i]);
        }
    }

    /**
     * Remove all child nodes from the object
     *
     * @return void
     */
    public function removeChildren(): void
    {
        $this->childNodes = [];
    }

}
