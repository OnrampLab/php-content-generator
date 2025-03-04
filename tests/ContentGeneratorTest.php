<?php

namespace ContentGenerator\Tests;

use PHPUnit\Framework\TestCase;
use ContentGenerator\Application\ContextManager;
use ContentGenerator\Application\TemplateManager;
use ContentGenerator\Application\ContentGenerator;
use ContentGenerator\Domain\Context\ContextDataProvider;

class ContentGeneratorTest extends TestCase
{
    private ContentGenerator $contentGenerator;

    protected function setUp(): void
    {
        $contextManager = new ContextManager();
        $templateManager = new TemplateManager();
        $this->contentGenerator = new ContentGenerator($contextManager, $templateManager);
    }

    public function testMissingNestedContext()
    {
        $this->contentGenerator->registerContext('user', new class implements ContextDataProvider {
            public function getData(array $parameters = []): array
            {
                return ['name' => 'Alice'];
            }
        });

        $this->contentGenerator->registerTemplate('nested_missing', 'Hello, {{ user.name }}, your role is {{ user.role }}.');

        $missingContexts = $this->contentGenerator->getMissingContexts();
        $this->assertContains('user.role', $missingContexts);

        $content = $this->contentGenerator->generateContent('nested_missing');
        $this->assertEquals('Hello, Alice, your role is .', $content);
    }

    public function testNestedContextVariables()
    {
        $this->contentGenerator->registerContext('user', new class implements ContextDataProvider {
            public function getData(array $parameters = []): array
            {
                return ['name' => 'Alice'];
            }
        });

        $this->contentGenerator->registerContext('content', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return 'Hi {{user.name}}, welcome!';
            }
        });

        $this->contentGenerator->registerTemplate('welcome_template', '{{content}}');

        $content = $this->contentGenerator->generateContent('welcome_template');
        $this->assertEquals('Hi Alice, welcome!', $content);
    }

    public function testComplexTemplateStructure()
    {
        $this->contentGenerator->registerContext('user', new class implements ContextDataProvider {
            public function getData(array $parameters = []): array
            {
                return ['name' => 'Alice', 'role' => 'admin'];
            }
        });
        $this->contentGenerator->registerTemplate('complex', 'User: {{ user.name }}, Role: {{ user.role }}');

        $content = $this->contentGenerator->generateContent('complex');
        $this->assertEquals('User: Alice, Role: admin', $content);
    }

    public function testRegisterAndGenerateContent()
    {
        $this->contentGenerator->registerContext('name', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return 'John Doe';
            }
        });
        $this->contentGenerator->registerTemplate('greeting', 'Hello, {{ name }}!');

        $content = $this->contentGenerator->generateContent('greeting');
        $this->assertEquals('Hello, John Doe!', $content);
    }

    public function testWithMultipleContexts()
    {
        $this->contentGenerator->registerContext('name', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return 'John Doe';
            }
        });
        $this->contentGenerator->registerContext('isAdmin', new class implements ContextDataProvider {
            public function getData(array $parameters = []): bool
            {
                return true;
            }
        });
        $this->contentGenerator->registerTemplate('greeting', 'Hello, {{#isAdmin}}{{name}}{{/isAdmin}}!');

        $content = $this->contentGenerator->generateContent('greeting');
        $this->assertEquals('Hello, John Doe!', $content);
    }

    public function testGetMissingContexts()
    {
        $this->contentGenerator->registerTemplate('greeting', 'Hello, {{ test }}!');
        $missingContexts = $this->contentGenerator->getMissingContexts();
        $content = $this->contentGenerator->generateContent('greeting');
        $this->assertContains('test', $missingContexts);
        $this->assertEquals('Hello, !', $content);
    }

    public function testGetNestedMissingContexts()
    {
        $this->contentGenerator->registerContext('content', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return 'Hi {{user.name}}, welcome!';
            }
        });

        $this->contentGenerator->registerTemplate('welcome_template', '{{content}}');

        $content = $this->contentGenerator->generateContent('welcome_template');
        $this->assertEquals('Hi , welcome!', $content);
        $missingContexts = $this->contentGenerator->getMissingContexts();
        $this->assertContains('user.name', $missingContexts);
    }

    public function testNestedComplexTemplateStructure()
    {
        $this->contentGenerator->registerContext('user', new class implements ContextDataProvider {
            public function getData(array $parameters = []): array
            {
                return ['name' => 'Alice', 'role' => 'admin'];
            }
        });
        $this->contentGenerator->registerContext('content1', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return 'Hi {{user.name}}, welcome!';
            }
        });
        $this->contentGenerator->registerContext('content2', new class implements ContextDataProvider {
            public function getData(array $parameters = []): string
            {
                return '{{ user.role }}';
            }
        });

        $this->contentGenerator->registerTemplate('complex', '{{ content1 }}, Role: {{ content2 }}');

        $content = $this->contentGenerator->generateContent('complex');
        $this->assertEquals('Hi Alice, welcome!, Role: admin', $content);
    }
}
