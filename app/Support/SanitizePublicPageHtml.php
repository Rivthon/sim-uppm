<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SanitizePublicPageHtml
{
    /** @var array<string, list<string>> */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'], 'img' => ['src', 'alt', 'title'],
        'span' => ['class'], 'p' => ['class'], 'h2' => ['class'], 'h3' => ['class'],
        'h4' => ['class'], 'li' => ['class'],
    ];

    /** @var list<string> */
    private const ALLOWED_TAGS = [
        'p', 'h2', 'h3', 'h4', 'strong', 'b', 'em', 'i', 'u', 's', 'blockquote',
        'ol', 'ul', 'li', 'a', 'img', 'br', 'pre', 'code', 'span', 'sub', 'sup',
    ];

    public function __invoke(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        if (strip_tags($html) === $html) {
            return nl2br(e($html));
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrors = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div id="public-page-content">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrors);
        $root = $document->getElementById('public-page-content');

        if (! $root instanceof DOMElement) {
            return null;
        }

        $this->sanitizeChildren($root);
        $sanitized = '';
        foreach ($root->childNodes as $child) {
            $sanitized .= $document->saveHTML($child);
        }

        return trim($sanitized) ?: null;
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed'], true)) {
                    $node->parentNode?->removeChild($node);

                    continue;
                }
                $this->sanitizeChildren($node);
                while ($node->firstChild) {
                    $node->parentNode?->insertBefore($node->firstChild, $node);
                }
                $node->parentNode?->removeChild($node);

                continue;
            }

            $this->sanitizeAttributes($node, $tag);
            $this->sanitizeChildren($node);
        }
    }

    private function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = self::ALLOWED_ATTRIBUTES[$tag] ?? [];
        foreach (iterator_to_array($element->attributes) as $attribute) {
            if (! in_array($attribute->name, $allowed, true)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($element->hasAttribute('class')) {
            $classes = array_filter(
                preg_split('/\s+/', $element->getAttribute('class')) ?: [],
                fn (string $class): bool => preg_match('/^ql-(align-(center|right|justify)|indent-[1-8])$/', $class) === 1,
            );
            $classes === [] ? $element->removeAttribute('class') : $element->setAttribute('class', implode(' ', $classes));
        }

        if ($tag === 'a') {
            $href = $element->getAttribute('href');
            if (! $this->isSafeLink($href)) {
                $element->removeAttribute('href');
            }
            if ($element->getAttribute('target') === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            } else {
                $element->removeAttribute('target');
                $element->removeAttribute('rel');
            }
        }

        if ($tag === 'img' && ! $this->isUploadedImage($element->getAttribute('src'))) {
            $element->parentNode?->removeChild($element);
        }
    }

    private function isSafeLink(string $url): bool
    {
        return $url !== '' && (str_starts_with($url, '/') || str_starts_with($url, '#') || preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1);
    }

    private function isUploadedImage(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && str_contains($path, '/storage/public-pages/content/');
    }
}
