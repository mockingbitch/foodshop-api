<?php

namespace App\Support;

/**
 * Sanitize HTML for WYSIWYG display. Allows safe tags only to prevent XSS.
 * Use for news content and excerpt (multilingual HTML).
 */
class HtmlSanitizer
{
    /** Allowed tags for WYSIWYG (headings, paragraphs, lists, links, images, formatting). */
    protected static string $allowedTags = '<p><br><br/><strong><b><em><i><u><s><strike>'
        . '<h1><h2><h3><h4><h5><h6>'
        . '<ul><ol><li>'
        . '<a><img>'
        . '<blockquote><pre><code>'
        . '<span><div>'
        . '<table><thead><tbody><tr><th><td>';

    /**
     * Sanitize HTML string for safe WYSIWYG display.
     */
    public static function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $sanitized = strip_tags($html, self::$allowedTags);

        // Optional: strip javascript: and data: from href/src to prevent XSS
        $sanitized = preg_replace_callback(
            '/(<(?:a|img)\s[^>]*)(href|src)=(["\']?)([^"\'>\s]+)\3/',
            function (array $m) {
                $url = $m[4];
                if (preg_match('#^\s*(javascript|data):#i', $url)) {
                    return $m[1];
                }
                return $m[0];
            },
            $sanitized
        );

        return trim($sanitized);
    }

    /**
     * Sanitize multilingual array (e.g. content.en, content.vn). Keys preserved, values sanitized.
     *
     * @param array<string, string>|null $items
     * @return array<string, string>
     */
    public static function sanitizeArray(?array $items): array
    {
        if ($items === null || $items === []) {
            return [];
        }

        $result = [];
        foreach ($items as $key => $value) {
            $result[$key] = is_string($value) ? self::sanitize($value) : '';
        }

        return $result;
    }
}
