pop-dom
=======

[![Build Status](https://travis-ci.org/popphp/pop-dom.svg?branch=master)](https://travis-ci.org/popphp/pop-dom)
[![Coverage Status](http://www.popphp.org/cc/coverage.php?comp=pop-dom)](http://www.popphp.org/cc/pop-dom/)

OVERVIEW
--------
`pop-dom` is a component for generating and rendering DOM documents and elements. With it, you
can easily create document nodes and their children and have control over node content and
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

### Use a config array to construct a complex DOM node fragment

```php
$children = [
    [
        'nodeName'      => 'h1',
        'nodeValue'     => 'This is a header',
        'attributes'    => ['class' => 'header', 'style' => 'font-size: 3.0em;'],
        'childrenFirst' => false,
        'childNodes'    => null
    ],
    [
        'nodeName'      => 'div',
        'nodeValue'     => 'This is a div element',
        'attributes'    => ['id' => 'content'],
        'childrenFirst' => false,
        'childNodes'    => [
            [
                'nodeName'      => 'p',
                'nodeValue'     => 'This is a paragraph1',
                'attributes'    => ['style' => 'font-size: 0.9em;'],
                'childrenFirst' => false,
                'childNodes'    => [
                    [
                        'nodeName'   => 'strong',
                        'nodeValue'  => 'This is bold!',
                        'attributes' => ['style' => 'font-size: 1.2em;']
                    ]
                ]
            ],
            [
                'nodeName'   => 'p',
                'nodeValue'  => 'This is another paragraph!',
                'attributes' => ['style' => 'font-size: 0.9em;']
            ]
        ],
    ]
];

$parent = new Child('div');
$parent->setIndent('    ')
       ->addChildren($children);
$parent->render();
```

```html
    <div>
        <h1 class="header" style="font-size: 3.0em;">This is a header</h1>
        <div id="content">
            This is a div element
            <p style="font-size: 0.9em;">
                This is a paragraph1
                <strong style="font-size: 1.2em;">This is bold!</strong>
            </p>
            <p style="font-size: 0.9em;">This is another paragraph!</p>
        </div>
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
$doc = new Document(Document::HTML5, $html);
$doc->render();
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