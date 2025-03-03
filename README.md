# ContentGenerator

ContentGenerator is a PHP package designed to manage and generate content using templates and contexts.

## Installation

To install the package, use Composer:

```bash
composer require your-namespace/content-generator
```

## Usage

Here's a basic example of how to use the ContentGenerator:

```php
use ContentGenerator\Application\ContentGenerator;
use ContentGenerator\Application\ContextManager;
use ContentGenerator\Application\TemplateManager;
use ContentGenerator\Domain\Context\DefaultContextDataProvider;

// Initialize managers
$contextManager = new ContextManager();
$templateManager = new TemplateManager();

// Create ContentGenerator instance
$contentGenerator = new ContentGenerator($contextManager, $templateManager);

// Register a context
$contentGenerator->registerContext('name', new DefaultContextDataProvider('name'));

// Register a template
$contentGenerator->registerTemplate('greeting', 'Hello, {{ name }}!');

// Generate content
$content = $contentGenerator->generateContent('greeting');
echo $content; // Outputs: Hello, {{ name }}!
```

## Testing

To run the tests, use PHPUnit:

```bash
php vendor/bin/phpunit
```

Ensure that Xdebug is properly configured if you encounter any issues related to debugging.
