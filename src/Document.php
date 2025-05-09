<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Dom;

/**
 * Dom class
 *
 * @category   Pop
 * @package    Pop\Dom
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.0.5
 */
class Document extends AbstractNode
{

    /**
     * Constant to use the XML doctype
     * @var string
     */
    const XML = 'XML';

    /**
     * Constant to use the HTML doctype
     * @var string
     */
    const HTML = 'HTML';

    /**
     * Constant to use the XML doctype, RSS content-type
     * @var string
     */
    const RSS = 'RSS';

    /**
     * Constant to use the XML doctype, Atom content-type
     * @var string
     */
    const ATOM = 'ATOM';

    /**
     * Document type
     * @var string
     */
    protected string $doctype = 'XML';

    /**
     * Document content type
     * @var string
     */
    protected string $contentType = 'application/xml';

    /**
     * Document charset
     * @var string
     */
    protected string $charset = 'utf-8';

    /**
     * Document doctypes
     * @var array
     */
    protected static array $doctypes = [
        'XML'  => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n",
        'HTML' =>"<!DOCTYPE html>\n"
    ];

    /**
     * Constructor
     *
     * Instantiate the document object
     *
     * @param  string  $doctype
     * @param  ?Child  $childNode
     * @param  ?string $indent
     * @throws Exception
     */
    public function __construct(string $doctype = 'XML', ?Child $childNode = null, ?string $indent = null)
    {
        $this->setDoctype($doctype);

        if ($childNode !== null) {
            $this->addChild($childNode);
        }
        if ($indent !== null) {
            $this->setIndent($indent);
        }
    }

    /**
     * Return the document type.
     *
     * @return string
     */
    public function getDoctype(): string
    {
        return str_replace('[{charset}]', $this->charset, Document::$doctypes[$this->doctype]);
    }

    /**
     * Return the document charset
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * Return the document charset
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Set the document type
     *
     * @param  string $doctype
     * @throws Exception
     * @return Document
     */
    public function setDoctype(string $doctype): Document
    {
        $doctype = strtoupper($doctype);

        if (($doctype != self::XML) && ($doctype != self::HTML) && ($doctype != self::RSS) && ($doctype != self::ATOM)) {
            throw new Exception('Error: Incorrect document type');
        }

        switch ($doctype) {
            case 'XML':
                $this->doctype     = self::XML;
                $this->contentType = 'application/xml';
                break;
            case 'HTML':
                $this->doctype     = self::HTML;
                $this->contentType = 'text/html';
                break;
            case 'RSS':
                $this->doctype     = self::XML;
                $this->contentType = 'application/rss+xml';
                break;
            case 'ATOM':
                $this->doctype     = self::XML;
                $this->contentType = 'application/atom+xml';
                break;
        }

        return $this;
    }

    /**
     * Set the document charset
     *
     * @param  string $char
     * @return Document
     */
    public function setCharset(string $char): Document
    {
        $this->charset = $char;
        return $this;
    }

    /**
     * Set the document charset
     *
     * @param  string $content
     * @return Document
     */
    public function setContentType(string $content): Document
    {
        $this->contentType = $content;
        return $this;
    }

    /**
     * Render the document and its child elements
     *
     * @return string
     */
    public function render(): string
    {
        $this->output = null;

        if ($this->doctype !== null) {
            $this->output .= str_replace('[{charset}]', $this->charset, Document::$doctypes[$this->doctype]);
        }

        foreach ($this->childNodes as $child) {
            $this->output .= $child->render(0, $this->indent);
        }

        return $this->output;
    }

    /**
     * Render Dom object to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
