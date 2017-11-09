pop-dom
=======

[![Build Status](https://travis-ci.org/popphp/pop-dom.svg?branch=master)](https://travis-ci.org/popphp/pop-dom)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-dom)](http://cc.popphp.org/pop-dom/)

OVERVIEW
--------
`pop-dom` is a component for generating, rendering and parsing DOM documents and elements. With it,
you can easily create or parse document nodes and their children and have control over node content and
attributes.

`pop-dom`is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-dom` using Composer.

    composer require popphp/pop-dom

BASIC USAGE
-----------

### A simple DOM node fragment

```php
use Pop\Dom\Child;

$div = new Child('div');
$h1  = new Child('h1', 'This is a header');
$p   = new Child('p');
$p->setNodeValue('This is a paragraph.');

$div->addChildren([$h1, $p]);

echo $div;
```

```html
<div>
    <h1>This is a header</h1>
    <p class="paragraph">This is a paragraph.</p>
</div>
```

### Building a full DOM document

```php
// Title element
$title = new Child('title', 'This is the title');

// Meta tag
$meta = new Child('meta');
$meta->setAttributes([
    'http-equiv' => 'Content-Type',
    'content'    => 'text/html; charset=utf-8'
]);

// Head element
$head = new Child('head');
$head->addChildren([$title, $meta]);

// Some body elements
$h1 = new Child('h1', 'This is a header');
$p  = new Child('p', 'This is a paragraph.');

$div = new Child('div');
$div->setAttribute('id', 'content');
$div->addChildren([$h1, $p]);

// Body element
$body = new Child('body');
$body->addChild($div);

// Html element
$html = new Child('html');
$html->addChildren([$head, $body]);

// Create and render the DOM document with HTTP headers
$doc = new Document(Document::HTML, $html);
echo $doc;
```

```html
<!DOCTYPE html>
<html>
    <head>
        <title>This is the title</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <div id="content">
            <h1>This is a header</h1>
            <p>This is a paragraph.</p>
        </div>
    </body>
</html>
```

### Parsing a DOM Document

You can parse from a string of XML or HTML and it will return an object graph of Child elements
that you can further manipulate or edit to then output: 

```php
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
echo $doc;
```

And you can parse from a file as well:

```php
$children = Child::parseFile('index.html');
```
