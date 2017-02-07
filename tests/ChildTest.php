<?php

namespace Pop\Dom\Test;

use Pop\Dom\Child;

class ChildTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $child = new Child('h1', 'Hello World', [
            'indent'        => '    ',
            'attributes'    => ['style' => 'width: 100px;'],
            'childrenFirst' => true
        ]);
        $this->assertInstanceOf('Pop\Dom\Child', $child);
        $child->setNodeName('title');
        $child->setNodeValue('Hello World!');
        $this->assertEquals('title', $child->getNodeName());
        $this->assertEquals('Hello World!', $child->getNodeValue());
    }

    public function testChildrenFirst()
    {
        $child = new Child('h1', 'Hello World', [
            'indent'        => '    ',
            'attributes'    => ['style' => 'width: 100px;'],
            'childrenFirst' => true
        ]);
        $this->assertTrue($child->isChildrenFirst());
        $child->setChildrenFirst(false);
        $this->assertFalse($child->isChildrenFirst());
    }

    public function testCreate()
    {
        $child = Child::create('h1', 'Hello World', [
            'attributes' => [
                'style' => 'color: #f00;'
            ]
        ]);
        $this->assertInstanceOf('Pop\Dom\Child', $child);
    }

    public function testRemove()
    {
        $child = new Child('h1', 'Hello World');
        $child->addChild(new Child('p', 'Paragraph'));
        $this->assertEquals(1, count($child->getChildren()));
        $child->removeChildren();
        $this->assertEquals(0, count($child->getChildren()));
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
        $child->addChild(new Child('p', 'Paragraph'));
        $this->assertContains('<h1 id="header" style="display: block;">', (string)$child);
        $this->assertContains('<p>Paragraph</p>', (string)$child);
    }

    public function testRenderChildrenFirst()
    {
        $child = new Child('h1', 'Header');
        $child->setChildrenFirst(true);
        $child->setIndent('    ');
        $child->setAttributes([
            'id'    => 'header',
            'style' => 'display: block;'
        ]);
        $child->addChild(new Child('p', 'Paragraph'));
        $this->assertContains('<h1 id="header" style="display: block;">', (string)$child);
        $this->assertContains('<p>Paragraph</p>', (string)$child);
    }

    public function testRenderNoNodeValue()
    {
        $child = new Child('h1');
        $this->assertEquals("<h1 />\n", (string)$child);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $child = new Child('h1', 'Header');
        ob_start();
        $child->render();
        $result = ob_get_clean();
        $this->assertContains('<h1>Header</h1>', $result);
    }

}