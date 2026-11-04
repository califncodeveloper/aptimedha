<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_scripts_and_their_contents(): void
    {
        $dirty = '<p>Safe text</p><script>alert("xss")</script>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringContainsString('<p>Safe text</p>', $clean);
        $this->assertStringNotContainsString('script', $clean);
        $this->assertStringNotContainsString('alert', $clean);
    }

    public function test_it_removes_inline_event_handlers(): void
    {
        $dirty = '<a href="https://example.com" onclick="alert(1)" onmouseover="steal()">Click me</a>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onmouseover', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_it_removes_javascript_pseudo_protocol(): void
    {
        $dirty = '<a href="javascript:alert(1)">Malicious link</a>';
        $clean = HtmlSanitizer::clean($dirty);

        $this->assertStringNotContainsString('javascript:', $clean);
    }

    public function test_it_allows_safe_tags(): void
    {
        $html = '<h2>Heading</h2><p>Paragraph with <strong>bold</strong>, <em>italic</em>, and a <a href="https://aptimedha.com">link</a>.</p><ul><li>Item 1</li><li>Item 2</li></ul>';
        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('<h2>Heading</h2>', $clean);
        $this->assertStringContainsString('<strong>bold</strong>', $clean);
        $this->assertStringContainsString('<em>italic</em>', $clean);
        $this->assertStringContainsString('<ul>', $clean);
        $this->assertStringContainsString('<li>Item 1</li>', $clean);
    }
}

