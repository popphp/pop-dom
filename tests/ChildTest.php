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
            'childrenFirst' => true,
            'whitespace'    => true
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

    public function testHasChildNodes()
    {
        $child = new Child('h1', 'Hello World');
        $child->addChild(new Child('p', 'Paragraph'));
        $child->addChildren([new Child('h1', 'Hello World'), new Child('h3', 'Hey!')]);
        $this->assertTrue($child->hasChildNodes());
    }

    public function testGetChildNodes()
    {
        $child = new Child('h1', 'Hello World');
        $child->addChild(new Child('p', 'Paragraph'));
        $child->addChildren([new Child('h1', 'Hello World'), new Child('h3', 'Hey!')]);
        $this->assertEquals(3, count($child->getChildNodes()));
    }

    public function testAddChildrenException()
    {
        $this->expectException('Pop\Dom\Exception');
        $child = new Child('h1', 'Hello');
        $child->addChildren('h4');
    }

    public function testRemove()
    {
        $child = new Child('h1', 'Hello World');
        $child->addChild(new Child('p', 'Paragraph'));
        $child->addChildren([new Child('h1', 'Hello World'), new Child('h3', 'Hey!')]);
        $child->addChildren(new Child('h4', 'Bye!'));
        $this->assertEquals(4, count($child->getChildren()));
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
        $this->assertTrue($child->hasAttribute('id'));
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

    public function testGetNodeContent()
    {
        $parent = new Child('html');

        $child = new Child('head');
        $parent->addChild($child);

        $parent = $child;
        $child = new Child('title', 'Hello World');
        $parent->addChild($child);

        $parent = $parent->getParent();
        $child = new Child('body');
        $parent->addChild($child);

        $parent = $child;
        $child = new Child('h1', 'hello world');
        $parent->addChild($child);

        // next (sibling)
        $child = new Child('p', 'some text');
        $parent->addChild($child);

        // next (sibling)
        $child = new Child('p');
        $child->addChild(new Child('#text', 'some ', ['whitespace' => true]));
        $child->addChild(new Child('strong', 'more', ['whitespace' => true]));
        $child->addChild(new Child('#text', ' text ', ['whitespace' => true]));
        $parent->addChild($child);

        while (null !== $parent->getParent()) {
            $parent = $parent->getParent();
        }

        $body = $parent->getChild(1);
        $content = $body->getNodeContent();
        $this->assertContains('<strong>more</strong>', $content);
        $this->assertNotContains('<body', $content);
    }

    public function testGetTextContent()
    {
        $parent = new Child('html');

        $child = new Child('head');
        $parent->addChild($child);

        $parent = $child;
        $child = new Child('title', 'Hello World');
        $parent->addChild($child);

        $parent = $parent->getParent();
        $child = new Child('body');
        $parent->addChild($child);

        $parent = $child;
        $child = new Child('h1', 'hello world');
        $parent->addChild($child);

        // next (sibling)
        $child = new Child('p', 'some text');
        $parent->addChild($child);

        // next (sibling)
        $child = new Child('p');
        $child->addChild(new Child('#text', 'some ', ['whitespace' => true]));
        $child->addChild(new Child('strong', 'more', ['whitespace' => true]));
        $child->addChild(new Child('#text', ' text ', ['whitespace' => true]));
        $parent->addChild($child);

        while (null !== $parent->getParent()) {
            $parent = $parent->getParent();
        }

        $body = $parent->getChild(1);
        $content = $body->getTextContent();
        $this->assertContains('more', $content);
        $this->assertNotContains('<h1>', $content);
    }

    public function testNodeWhiteSpace()
    {
        $child = new Child('note', '   Hello   ');
        $this->assertEquals('Hello', $child->getNodeContent(true));
    }

    public function testTextWhiteSpace()
    {
        $child = new Child('note', '   Hello   ');
        $this->assertEquals('Hello', $child->getTextContent(true));
    }

    public function testAddNodeValue()
    {
        $child = new Child('note', 'Hello');
        $child->addNodeValue(' World');
        $this->assertEquals('Hello World', $child->getNodeValue());
    }

    public function testCData()
    {
        $child = new Child('note', "Here's some crazy TEXT!<br />");
        $child->setAsCData();

        $content = $child->render();
        $this->assertTrue($child->isCData());
        $this->assertContains('<![CDATA[', $content);
        $this->assertContains(']]>', $content);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $child = new Child('h1', 'Header');
        ob_start();
        echo $child;
        $result = ob_get_clean();
        $this->assertContains('<h1>Header</h1>', $result);
    }

}