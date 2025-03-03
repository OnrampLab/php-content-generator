<?php

namespace ContentGenerator\Tests;

use PHPUnit\Framework\TestCase;
use ContentGenerator\Domain\Template\Template;
use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Context\DefaultContextDataProvider;

class TemplateTest extends TestCase {
    public function testTemplateName() {
        $template = new Template('testTemplate', 'Hello, {{ name }}!');
        $this->assertEquals('testTemplate', $template->getName());
    }

    public function testRenderTemplate() {
        $template = new Template('testTemplate', 'Hello, {{ name }}!');
        $context = new Context('name', new DefaultContextDataProvider('name'));
        $this->assertEquals('Hello, {{ name }}!', $template->render(['name' => $context]));
    }
}
