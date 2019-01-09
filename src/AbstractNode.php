<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

/**
 * Abstract node class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.2.6
 */
abstract class AbstractNode
{

    /**
     * Object child nodes
     * @var array
     */
    protected $childNodes = [];

    /**
     * Indentation for formatting purposes
     * @var string
     */
    protected $indent = null;

    /**
     * Child output
     * @var string
     */
    protected $output = null;

    /**
     * Parent node
     * @var AbstractNode
     */
    protected $parent = null;

    /**
     * Return the indent
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Set the indent
     *
     * @param  string $indent
     * @return mixed
     */
    public function setIndent($indent)
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Return the parent node
     *
     * @return AbstractNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent node
     *
     * @param  AbstractNode $parent
     * @return AbstractNode
     */
    public function setParent(AbstractNode $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Add a child to the object
     *
     * @param  mixed $c
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function addChild(Child $c)
    {
        $c->setParent($this);
        $this->childNodes[] = $c;
        return $this;
    }

    /**
     * Add children to the object
     *
     * @param  $children
     * @throws Exception
     * @return mixed
     */
    public function addChildren($children)
    {
        if (is_array($children)) {
            foreach ($children as $child) {
                $this->addChild($child);
            }
        } else if ($children instanceof Child) {
            $this->addChild($children);
        } else {
            throw new Exception('Error: The parameter passed must be an instance of Pop\Dom\Child or an array of Pop\Dom\Child instances.');
        }

        return $this;
    }

    /**
     * Get whether or not the child object has children
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return (count($this->childNodes) > 0);
    }

    /**
     * Get whether or not the child object has children (alias)
     *
     * @return boolean
     */
    public function hasChildNodes()
    {
        return (count($this->childNodes) > 0);
    }

    /**
     * Get the child nodes of the object
     *
     * @param int $i
     * @return Child
     */
    public function getChild($i)
    {
        return (isset($this->childNodes[(int)$i])) ? $this->childNodes[(int)$i] : null;
    }

    /**
     * Get the child nodes of the object
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->childNodes;
    }

    /**
     * Get the child nodes of the object (alias)
     *
     * @return array
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * Remove all child nodes from the object
     *
     * @param  int  $i
     * @return void
     */
    public function removeChild($i)
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
    public function removeChildren()
    {
        $this->childNodes = [];
    }

}
