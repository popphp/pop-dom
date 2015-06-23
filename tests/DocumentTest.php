<?php

namespace Pop\Dom\Test;

use Pop\Dom\Document;
use Pop\Dom\Child;

class DomTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $doc = new Document(Document::HTML5, new Child('title', 'Hello World'), '    ');
        $this->assertInstanceOf('Pop\Dom\Document', $doc);
        $this->assertEquals('    ', $doc->getIndent());
        $this->assertEquals(1, count($doc->getChildren()));
        $this->assertContains('<!DOCTYPE html>', $doc->getDoctype());
    }

    public function testSetDoctype()
    {
        $doc = new Document();
        $doc->setDoctype(Document::ATOM);
        $this->assertContains('<?xml version=', $doc->getDoctype());
        $doc->setDoctype(Document::RSS);
        $this->assertContains('<?xml version=', $doc->getDoctype());
        $doc->setDoctype(Document::XML);
        $this->assertContains('<?xml version=', $doc->getDoctype());
    }

    public function testSetCharset()
    {
        $doc = new Document();
        $doc->setCharset('utf-8');
        $this->assertEquals('utf-8', $doc->getCharset());
    }

    public function testSetContentType()
    {
        $doc = new Document();
        $doc->setContentType('application/xml');
        $this->assertEquals('application/xml', $doc->getContentType());
    }

    public function testAddChild()
    {
        $doc = new Document();
        $doc->addChild(new Child('title', 'Hello World'));
        $this->assertTrue($doc->hasChildren());
        $this->assertEquals(1, count($doc->getChildren()));
        $this->assertInstanceOf('Pop\Dom\Child', $doc->getChild(0));
        $doc->removeChild(0);
        $this->assertNull($doc->getChild(0));
        $this->assertEquals(0, count($doc->getChildren()));
    }

    public function testAddChildFromConfigArray()
    {
        $doc = new Document();
        $doc->addChild([
            'nodeName'      => 'title',
            'nodeValue'     => 'Hello World',
            'childrenFirst' => true,
            'indent'        => '    '
        ]);
        $this->assertEquals(1, count($doc->getChildren()));
        $doc->removeChildren();
        $this->assertEquals(0, count($doc->getChildren()));
    }

    public function testAddChildException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $doc = new Document();
        $doc->addChild('bad');
    }

    public function testAddChildren()
    {
        $doc = new Document();
        $doc->addChildren([
            new Child('title', 'Hello World'),
            new Child('h1', 'Header')
        ]);
        $this->assertEquals(2, count($doc->getChildren()));
    }

    public function testRender()
    {
        $doc = new Document();
        $doc->addChildren([
            new Child('title', 'Hello World'),
            new Child('h1', 'Header')
        ]);
        $this->assertContains('<h1>Header</h1>', (string)$doc);
    }

    /**
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $doc = new Document(Document::HTML5, new Child('title', 'Hello World'));
        $doc->addChild(new Child('h1', 'Header'));

        ob_start();
        $doc->render();
        $result = ob_get_clean();

        $this->assertContains('<h1>Header</h1>', $result);
    }

}
