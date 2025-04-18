<?php

namespace Pop\Dom\Test;

use Pop\Dom\Document;
use Pop\Dom\Child;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{

    public function testConstructor()
    {
        $doc = new Document(Document::HTML, new Child('title', 'Hello World'), '    ');
        $this->assertInstanceOf('Pop\Dom\Document', $doc);
        $this->assertEquals('    ', $doc->getIndent());
        $this->assertEquals(1, count($doc->getChildren()));
        $this->assertStringContainsString('<!DOCTYPE html>', $doc->getDoctype());
    }

    public function testSetDoctype()
    {
        $doc = new Document();
        $doc->setDoctype(Document::ATOM);
        $this->assertStringContainsString('<?xml version=', $doc->getDoctype());
        $doc->setDoctype(Document::RSS);
        $this->assertStringContainsString('<?xml version=', $doc->getDoctype());
        $doc->setDoctype(Document::XML);
        $this->assertStringContainsString('<?xml version=', $doc->getDoctype());
    }

    public function testSetDoctypeException()
    {
        $this->expectException('Pop\Dom\Exception');
        $doc = new Document();
        $doc->setDoctype('BAD');
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
        $this->assertStringContainsString('<h1>Header</h1>', (string)$doc);
    }

    #[runInSeparateProcess]
    public function testOutput()
    {
        $doc = new Document(Document::HTML, new Child('title', 'Hello World'));
        $doc->addChild(new Child('h1', 'Header'));

        ob_start();
        echo $doc;
        $result = ob_get_clean();

        $this->assertStringContainsString('<h1>Header</h1>', $result);
    }

}
