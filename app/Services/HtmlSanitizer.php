<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class HtmlSanitizer
{
    /**
     * Allowed HTML tag names.
     */
    protected static array $allowedTags = [
        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'a', 'img',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'hr', 'br',
        'span', 'div',
    ];

    /**
     * Tags whose contents must be removed entirely.
     */
    protected static array $dangerousTags = [
        'script', 'style', 'iframe', 'frame', 'frameset',
        'object', 'embed', 'applet', 'form', 'input',
        'button', 'textarea', 'select', 'meta', 'link', 'base',
    ];

    /**
     * Allowed attribute names.
     */
    protected static array $allowedAttributes = [
        'href', 'title', 'target', 'rel',
        'src', 'alt', 'width', 'height',
        'class', 'id',
    ];

    /**
     * Sanitize an HTML string.
     */
    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Fast check: if no html tags, return as is
        if (!str_contains($html, '<')) {
            return e($html);
        }

        $dom = new DOMDocument();
        $libxmlState = libxml_use_internal_errors(true);

        // Wrap fragment with UTF-8 meta and container
        $wrapped = '<?xml encoding="UTF-8"><div id="html-sanitizer-root">' . $html . '</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();
        libxml_use_internal_errors($libxmlState);

        $root = $dom->getElementById('html-sanitizer-root');
        if (!$root) {
            return '';
        }

        static::sanitizeNode($root);

        // Serialize inner HTML of the container
        $cleanHtml = '';
        foreach ($root->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        return trim($cleanHtml);
    }

    /**
     * Recursively sanitize DOM nodes.
     */
    protected static function sanitizeNode(DOMNode $node): void
    {
        $toRemove = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                /** @var DOMElement $child */
                $tagName = strtolower($child->nodeName);

                // Completely remove dangerous tags and their content
                if (in_array($tagName, static::$dangerousTags, true)) {
                    $toRemove[] = $child;
                    continue;
                }

                // If tag is not allowed, unwrap its children into parent
                if (!in_array($tagName, static::$allowedTags, true)) {
                    // Recursively clean children first
                    static::sanitizeNode($child);
                    // Move child nodes before current node, then delete node
                    while ($child->hasChildNodes()) {
                        $childNode = $child->firstChild;
                        $node->insertBefore($childNode, $child);
                    }
                    $toRemove[] = $child;
                    continue;
                }

                // Clean attributes
                static::sanitizeAttributes($child);

                // Recursively clean inner nodes
                static::sanitizeNode($child);
            }
        }

        foreach ($toRemove as $rem) {
            if ($rem->parentNode) {
                $rem->parentNode->removeChild($rem);
            }
        }
    }

    /**
     * Sanitize attributes on an allowed element.
     */
    protected static function sanitizeAttributes(DOMElement $element): void
    {
        $attrsToRemove = [];
        $tagName = strtolower($element->nodeName);

        foreach ($element->attributes as $attr) {
            $attrName = strtolower($attr->nodeName);
            $attrValue = trim($attr->nodeValue);

            // Disallow all on* attributes (event handlers)
            if (str_starts_with($attrName, 'on')) {
                $attrsToRemove[] = $attr->nodeName;
                continue;
            }

            // Check if attribute is in allowed list
            if (!in_array($attrName, static::$allowedAttributes, true)) {
                $attrsToRemove[] = $attr->nodeName;
                continue;
            }

            // Validate href values
            if ($attrName === 'href') {
                $lower = strtolower($attrValue);
                if (str_starts_with($lower, 'javascript:') ||
                    str_starts_with($lower, 'data:') ||
                    str_starts_with($lower, 'vbscript:')) {
                    $attrsToRemove[] = $attr->nodeName;
                    continue;
                }
            }

            // Validate src values (only https://, http://, relative URLs; never allow data: base64 or script schemes)
            if ($attrName === 'src') {
                $lower = strtolower($attrValue);
                if (str_starts_with($lower, 'javascript:') ||
                    str_starts_with($lower, 'vbscript:') ||
                    str_starts_with($lower, 'data:')) {
                    $attrsToRemove[] = $attr->nodeName;
                    continue;
                }
            }
        }

        foreach ($attrsToRemove as $remove) {
            $element->removeAttribute($remove);
        }

        // Enforce safe rel for external links
        if ($tagName === 'a' && $element->hasAttribute('href')) {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }
}

