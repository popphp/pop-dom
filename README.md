pop-dom
=======

[![Build Status](https://github.com/popphp/pop-dom/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-dom/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-dom)](http://cc.popphp.org/pop-dom/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Parsing](#parsing)

Overview
--------
`pop-dom` is a component for generating, rendering and parsing DOM documents and elements. With it,
you can easily create or parse document nodes and their children and have control over node content and
attributes.

`pop-dom` is a component of the [Pop PHP Framework](https://www.popphp.org/).

[Top](#pop-dom)

Install
-------

Install `pop-dom` using Composer.

    composer require popphp/pop-dom

Or, require it in your composer.json file

    "require": {
        "popphp/pop-dom" : "^4.0.7"
    }

[Top](#pop-dom)

Quickstart
----------

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
use Pop\Dom\Document;
use Pop\Dom\Child;

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

[Top](#pop-dom)

Parsing
-------

You can parse from a string of XML or HTML and it will return an object of Child elements
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

[Top](#pop-dom)
