<?php

namespace Pop\Dom\Test;

use Pop\Dom\DomIterator;
use PHPUnit\Framework\TestCase;

class DomIteratorTest extends TestCase
{

    public function testKey()
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

        $doc = new \DOMDocument();
        $doc->loadHTML($html);

        $dit = new DomIterator($doc);

        $this->assertEquals(0, $dit->key());
    }

}