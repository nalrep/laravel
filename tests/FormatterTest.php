<?php

namespace Narlrep\Tests;

use Narlrep\Output\Formatter;

class FormatterTest extends TestCase
{
    public function test_it_formats_to_json()
    {
        $formatter = new Formatter();
        $data = [['name' => 'John', 'email' => 'john@example.com']];
        
        $json = $formatter->format($data, 'json');
        
        $this->assertJson($json);
        $this->assertStringContainsString('John', $json);
    }

    public function test_it_formats_to_html_table()
    {
        $formatter = new Formatter();
        $data = [['name' => 'John', 'email' => 'john@example.com']];
        
        $html = $formatter->format($data, 'html');
        
        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('John', $html);
    }

    public function test_it_formats_to_html_list()
    {
        $formatter = new Formatter();
        $data = ['Apple', 'Banana', 'Cherry'];
        
        $html = $formatter->format($data, 'html');
        
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<li>Apple</li>', $html);
        $this->assertStringNotContainsString('<table', $html);
    }

    public function test_it_formats_to_html_simple()
    {
        $formatter = new Formatter();
        $data = "Total Sales: $500";
        
        $html = $formatter->format($data, 'html');
        
        $this->assertStringContainsString('<p', $html);
        $this->assertStringContainsString('Total Sales: $500', $html);
        $this->assertStringNotContainsString('<table', $html);
    }
}
