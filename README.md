pop-dom
=======

[![Build Status](https://github.com/popphp/pop-dom/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-dom/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-dom)](http://cc.popphp.org/pop-dom/)

[![Join the chat at https://discord.gg/TZjgT74U7E](https://media.popphp.org/img/discord.svg)](https://discord.gg/TZjgT74U7E)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Working with Nodes](#working-with-nodes)
* [Rendering Options](#rendering-options)
* [Document Types](#document-types)
* [Parsing](#parsing)
* [Exceptions](#exceptions)

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
        "popphp/pop-dom" : "^4.1.0"
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
$p->setAttribute('class', 'paragraph');

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

// Create and render the DOM document
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

Working with Nodes
-------------------

`Child::create()` is a static-factory alternative to `new Child(...)`, useful for fluent chaining:

```php
use Pop\Dom\Child;

$p = Child::create('p', 'Some text', ['attributes' => ['class' => 'lead']]);
echo $p;
```
```html
<p class="lead">Some text</p>
```

The constructor's `$options` array is shorthand for calling several setters at once. Supported keys:
`attributes` (array, same as `setAttributes()`), `cData` (bool, same as `setAsCData()`), `childrenFirst`
(bool, same as `setChildrenFirst()`), `indent` (string, same as `setIndent()`), and `whitespace` (bool, same
as `preserveWhiteSpace()`):

```php
$note = new Child('note', 'Hello & welcome', [
    'cData'      => true,
    'attributes' => ['id' => 'greeting']
]);
echo $note;
```
```html
<note id="greeting"><![CDATA[Hello & welcome]]></note>
```

### Node name and value

```php
$h = new Child('h2', 'Original');
$h->setNodeName('h3');
$h->setNodeValue('Replaced');
echo $h->getNodeName();  // h3
echo $h->getNodeValue(); // Replaced

$note = new Child('note', 'Hello');
$note->addNodeValue(' World'); // appends to the existing value
echo $note->getNodeValue(); // Hello World
```

### Attributes

```php
$el = new Child('div');
$el->setAttribute('id', 'main');
$el->setAttributes(['data-role' => 'content', 'class' => 'box']);

$el->hasAttribute('id');    // true
$el->getAttribute('class'); // 'box'
$el->getAttributes();       // ['id' => 'main', 'data-role' => 'content', 'class' => 'box']
$el->hasAttributes();       // true

$el->removeAttribute('class');
$el->hasAttribute('class'); // false
```

Attribute values are HTML-escaped automatically when rendered, so it's safe to pass values containing
quotes or other markup characters.

### Tree navigation and mutation

```php
$ul = new Child('ul');
$ul->addChildren([new Child('li', 'One'), new Child('li', 'Two'), new Child('li', 'Three')]);

$ul->hasChildren();          // true (hasChildNodes() is an alias)
count($ul->getChildren());   // 3   (getChildNodes() is an alias)
$ul->getChild(1)->getNodeValue(); // 'Two'

$ul->removeChild(0);         // removes 'One'; remaining children are reindexed from 0
$ul->getChild(0)->getNodeValue(); // 'Two'

$ul->getChild(0)->getParent(); // the $ul Child instance

$ul->removeChildren();       // clears all children
$ul->hasChildren();          // false
```

[Top](#pop-dom)

Rendering Options
------------------

### Value-first vs. children-first

By default, a node's own value renders before its children. `setChildrenFirst()` reverses that:

```php
$p = new Child('p', 'Value last:');
$p->setChildrenFirst(true);
$p->addChild(new Child('strong', 'child'));
echo $p;
```
```html
<p>
    <strong>child</strong>
    Value last:
</p>
```

### Whitespace and indentation

Output is pretty-printed (indented, one node per line) by default. `preserveWhiteSpace(false)` collapses a
node's own markup onto a single line instead:

```php
$div = new Child('div');
$div->preserveWhiteSpace(false);
$div->addChild(new Child('span', 'inline'));
echo $div;
```
```html
<div>    <span>inline</span>
</div>
```

`setIndent()` overrides the indentation string used for that specific node instead of the default
4-space-per-depth-level convention:

```php
$div = new Child('div');
$div->setIndent('  ');
$div->addChild(new Child('span', 'two-space indent'));
echo $div;
```
```html
  <div>
      <span>two-space indent</span>
  </div>
```

### CDATA

`setAsCData()` wraps a node's value in `<![CDATA[ ... ]]>`, useful for XML/RSS/Atom content that may contain
markup characters that shouldn't be escaped:

```php
$note = new Child('note', 'Value with <special> & characters');
$note->setAsCData();
echo $note;
```
```html
<note><![CDATA[Value with <special> & characters]]></note>
```

`isCData()` reports whether the flag is set.

### Extracting content

`getNodeContent()` returns a node's inner markup (its children, rendered, without the node's own opening/
closing tag); `getTextContent()` does the same but strips all tags, leaving plain text. Both accept an
`$ignoreWhiteSpace` flag that collapses runs of whitespace to single spaces and normalizes spacing around
sentence punctuation (`. ? ! , : ;`) — handy for pulling readable text out of parsed or hand-built markup:

```php
$note = new Child('note', '   Extra   whitespace   here.   ');
echo $note->getNodeContent();       // '   Extra   whitespace   here.   '
echo $note->getNodeContent(true);   // 'Extra whitespace here. '

$p = new Child('p');
$p->addChild(new Child('#text', 'Some ', ['whitespace' => true]));
$p->addChild(new Child('strong', 'bold', ['whitespace' => true]));
$p->addChild(new Child('#text', ' text', ['whitespace' => true]));

echo $p->getNodeContent(true); // 'Some <strong>bold</strong> text'
echo $p->getTextContent(true); // 'Some bold text'
```

[Top](#pop-dom)

Document Types
----------------

`Document` supports four doctype constants. `XML`, `RSS` and `ATOM` all emit the same XML declaration and
differ only in the `Content-Type` they report; `HTML` emits an HTML5 doctype:

```php
use Pop\Dom\Document;

$doc = new Document();               // Document::XML is the default
$doc->getDoctype();                  // <?xml version="1.0" encoding="utf-8"?>
$doc->getContentType();              // application/xml

$rss = new Document(Document::RSS);
$rss->getContentType();              // application/rss+xml

$atom = new Document(Document::ATOM);
$atom->getContentType();             // application/atom+xml
```

`setCharset()`/`getCharset()` control the encoding declared in the XML doctype; `setContentType()` overrides
the reported content type directly. Note that `Document` only builds the markup string — sending it with the
right `Content-Type` header (e.g. via `$response->addHeaders(['Content-Type' => $doc->getContentType()])` in
a full Pop application) is up to the caller.

A minimal RSS feed, built the same way as any other document:

```php
$item = new Child('item');
$item->addChildren([
    new Child('title', 'First Post'),
    new Child('link', 'https://example.com/first-post'),
]);

$channel = new Child('channel');
$channel->addChildren([
    new Child('title', 'My Feed'),
    new Child('link', 'https://example.com'),
    $item
]);

$rssRoot = new Child('rss');
$rssRoot->setAttribute('version', '2.0');
$rssRoot->addChild($channel);

$doc = new Document(Document::RSS, $rssRoot);
echo $doc;
```
```xml
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title>My Feed</title>
        <link>https://example.com</link>
        <item>
            <title>First Post</title>
            <link>https://example.com/first-post</link>
        </item>
    </channel>
</rss>
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

### Return shape

Both `parseString()` and `parseFile()` share the same `Child|array|null` return type, which depends on the
shape of the input:

- A full document (containing `<html>` and `<body>`) returns a single `Child` — the `<html>` root — as shown
  above.
- A fragment with multiple top-level nodes (e.g. `"<div>one</div><div>two</div>"`) returns an `array` of
  top-level `Child` instances instead, since there's no single root to return.
- Input with no element nodes at all (e.g. an empty or whitespace-only string) returns `null`.

Because of this, prefer `addChildren()` over `addChild()` when the input shape isn't guaranteed to be a
single root — `addChildren()` accepts either a `Child` or an `array` of `Child`:

```php
$doc = new Document(Document::HTML);
$doc->addChildren(Child::parseString('<div>one</div><div>two</div>'));
echo $doc;
```
```html
<!DOCTYPE html>
<div>one</div>
<div>two</div>
```

### Mixed text and element content

The parser represents interleaved text and elements (e.g. `<p>How are <em>YOU</em> doing?</p>`) as a mix of
regular `Child` nodes and `Child` nodes named `#text` — a `#text` node renders just its value, with no
surrounding tag. This is a public pattern you can also use when building mixed content by hand instead of
parsing it:

```php
$p = new Child('p');
$p->addChild(new Child('#text', 'How are ', ['whitespace' => true]));
$p->addChild(new Child('em', 'YOU', ['whitespace' => true]));
$p->addChild(new Child('#text', ' doing?', ['whitespace' => true]));
echo $p;
```
```html
<p>
    How are 
    <em>YOU</em>
     doing?
</p>
```

[Top](#pop-dom)

Exceptions
----------

`Pop\Dom\Exception` is the one exception type this component throws:

```php
use Pop\Dom\Document;
use Pop\Dom\Child;
use Pop\Dom\Exception;

try {
    (new Document())->setDoctype('BOGUS');           // invalid doctype
} catch (Exception $e) { /* ... */ }

try {
    (new Child('div'))->addChildren('not-a-child');  // addChildren() given something other than a Child/array
} catch (Exception $e) { /* ... */ }

try {
    Child::parseFile('/no/such/file.html');          // parseFile() given a nonexistent path
} catch (Exception $e) { /* ... */ }
```

[Top](#pop-dom)
