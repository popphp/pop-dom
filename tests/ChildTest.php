<?php

namespace Pop\Dom\Test;

use Pop\Dom\Child;

class ChildTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $child = new Child('h1', 'Hello World', new Child('h2', 'Test'));
        $this->assertInstanceOf('Pop\Dom\Child', $child);
        $child->setNodeName('title');
        $child->setNodeValue('Hello World!');
        $this->assertEquals('title', $child->getNodeName());
        $this->assertEquals('Hello World!', $child->getNodeValue());
    }

    public function testFactory()
    {
        $child = Child::factory([
            'nodeName'   => 'h1',
            'nodeValue'  => 'Hello World',
            'attributes' => [
                'style' => 'color: #f00;'
            ],
            'childNodes' => [[
                'nodeName'  => 'h2',
                'nodeValue' => 'Test',
            ]]
        ]);
        $this->assertInstanceOf('Pop\Dom\Child', $child);
    }

    public function testFactoryException()
    {
        $this->setExpectedException('Pop\Dom\Exception');
        $child = Child::factory([
            'nodeValue'  => 'Hello World'
        ]);
    }

    public function testAttributes()
    {
        $child = new Child('h1', 'Hello World');
        $child->setAttribute('class', 'header');
        $child->setAttributes([
            'id'    => 'header',
            'style' => 'display: block;'
        ]);
        $this->assertEquals('header', $child->getAttribute('id'));
        $this->assertEquals(3, count($child->getAttributes()));
        $child->removeAttribute('id');
        $this->assertNull($child->getAttribute('id'));
    }

    public function testRender()
    {
        $child = new Child('h1', 'Header');
        $child->setIndent('    ');
        $child->setAttributes([
            'id'    => 'header',
            'style' => 'display: block;'
        ]);
        $this->assertContains('<h1 id="header" style="display: block;">Header</h1>', (string)$child);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $child = Child::factory([
            'nodeName'   => 'h1',
            'nodeValue'  => 'Hello World',
            'attributes' => [
                'style' => 'color: #f00;'
            ],
            'childNodes' => [[
                'nodeName'  => 'h2',
                'nodeValue' => 'Test',
            ]]
        ]);

        ob_start();
        $child->render();
        $result = ob_get_clean();

        $this->assertContains('<h1 style="color: #f00;">', $result);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutputChildrenFirst()
    {
        $child = Child::factory([
            'nodeName'   => 'h1',
            'nodeValue'  => 'Hello World',
            'attributes' => [
                'style' => 'color: #f00;'
            ],
            'childrenFirst' => true,
            'childNodes' => [[
                'nodeName'  => 'h2',
                'nodeValue' => 'Test',
            ]]
        ]);

        ob_start();
        $child->render();
        $result = ob_get_clean();

        $this->assertContains('<h1 style="color: #f00;">', $result);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutputNoNodeValue()
    {
        $child = Child::factory([
            'nodeName'   => 'img',
            'attributes' => [
                'src' => 'img/image.jpg'
            ]
        ]);

        ob_start();
        $child->render();
        $result = ob_get_clean();

        $this->assertContains('<img src="img/image.jpg" />', $result);
    }

}