pop-dom
=======

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