<?php

namespace ContentGenerator\Domain\Template;

use Mustache_Engine;
use Mustache_Loader_StringLoader;

class Template {
    private string $templateName;
    private string $templateContent;

    public function __construct(string $templateName, string $templateContent) {
        $this->templateName = $templateName;
        $this->templateContent = $templateContent;
    }

    public function getName(): string {
        return $this->templateName;
    }

    public function getContent(): string {
        return $this->templateContent;
    }

    public function render(array $contexts, array $parameters = []): string {
        try {
            $mustache = new Mustache_Engine([
                'loader' => new Mustache_Loader_StringLoader(),
                'cache' => null
            ]);

            $contextData = [];
            foreach ($contexts as $key => $context) {
                $rendered = $context->render($parameters);
                if (is_string($rendered)) {
                    $rendered = $mustache->render($rendered, $contextData);
                }
                $contextData[$key] = $rendered;
            }

            $allVariables = TemplateParser::extractVariables($this->templateContent);

            foreach ($allVariables as $variable) {
                $keys = explode('.', $variable);
                $current = &$contextData;
                foreach ($keys as $key) {
                    if (!isset($current[$key])) {
                        $current[$key] = [];
                    }
                    $current = &$current[$key];
                }
                if (empty($current)) {
                    $current = '{{ ' . $variable . ' }}';
                }
            }

            return $mustache->render($this->templateContent, $contextData);
        } catch (\Throwable $e) {
            throw new \RuntimeException("Render error in template '{$this->templateName}': " . $e->getMessage());
        }
    }
}
