<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @package    Pop_Dom
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
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
     * Add a child to the object
     *
     * @param  mixed $c
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function addChild(Child $c)
    {
        $this->childNodes[] = $c;
        return $this;
    }

    /**
     * Add children to the object
     *
     * @param  array $c
     * @throws Exception
     * @return mixed
     */
    public function addChildren(array $c)
    {
        foreach ($c as $child) {
            $this->addChild($child);
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
