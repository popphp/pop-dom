<?php

namespace Pop\Dom\Test;

use Pop\Dom\Document;
use Pop\Dom\Child;
use Pop\Dom\DomIterator;

class ParseTest extends \PHPUnit_Framework_TestCase
{

    public function testParseString()
    {
        $html = <<<HTML
<html>
    <head>
        <title>Hello World Title</title>
    </head>
    <body>
        <h1 class="top-header" id="header">Hello World Header</h1>
        <p>How are <em>YOU</em> doing <strong><em>today</em></strong>???</p>
        <p class="special-p">Some <strong class="bold">more</strong> text.</p>
    </body>
</html>
HTML;
        $doc = new Document(Document::HTML);
        $doc->addChild(Child::parseString($html));
        $render = (string)$doc;
        $this->assertContains('<title>Hello World Title</title>', $render);
        $this->assertContains('class="top-header"', $render);
        $this->assertContains('<p class="special-p">', $render);
    }

    public function testParseFile()
    {
        $doc = new Document(Document::HTML);
        $doc->addChild(Child::parseFile(__DIR__ . '/tmp/test.html'));
        $render = (string)$doc;
        $this->assertContains('<title>Hello World Title</title>', $render);
        $this->assertContains('class="top-header"', $render);
        $this->assertContains('<p class="special-p">', $render);
    }

    public function testParseFileException()
    {
        $this->expectException('Pop\Dom\Exception');
        $doc = new Document(Document::HTML);
        $doc->addChild(Child::parseFile(__DIR__ . '/tmp/bad.html'));
    }


    public function testParsePartial()
    {
        $html = <<<HTML
        <h1 class="top-header" id="header">Hello World Header</h1>
        <p>How are <em>YOU</em> doing <strong><em>today</em></strong>???</p>
        <p class="special-p">Some <strong class="bold">more</strong> text.</p>
HTML;
        $doc = new Document(Document::HTML);
        $doc->addChildren(Child::parseString($html));
        $render = (string)$doc;
        $this->assertContains('class="top-header"', $render);
        $this->assertContains('<p class="special-p">', $render);
    }

}