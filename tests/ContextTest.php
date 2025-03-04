<?php

namespace ContentGenerator\Tests;

use PHPUnit\Framework\TestCase;
use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Context\DefaultContextDataProvider;

class ContextTest extends TestCase {
    public function testContextName() {
        $context = new Context('testContext', new DefaultContextDataProvider('testContext'));
        $this->assertEquals('testContext', $context->getName());
    }

    public function testRenderDefaultContext() {
        $context = new Context('testContext', new DefaultContextDataProvider('testContext'));
        $this->assertEquals('testContext', $context->render());
    }
}
