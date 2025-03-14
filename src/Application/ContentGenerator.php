<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Context\ContextDataProvider;
use ContentGenerator\Domain\Template\TemplateParser;
use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Template\Template;

class ContentGenerator
{
    private ContextManager $contextManager;
    private TemplateManager $templateManager;

    public function __construct(
        ContextManager $contextManager,
        TemplateManager $templateManager
    ) {
        $this->contextManager = $contextManager;
        $this->templateManager = $templateManager;
    }

    public function registerContext(string $contextName, ContextDataProvider $provider): void
    {
        $this->contextManager->addContext(new Context($contextName, $provider));
        $this->contextManager->removeMissingContext($contextName);
    }

    /**
     * @param array<mixed> $parameters
     */
    public function registerTemplate(string $templateName, string $templateContent, array $parameters = []): void
    {
        $template = new Template($templateName, $templateContent);
        $this->templateManager->addTemplate($template);

        $this->checkAndRegisterNestedContexts(templateContent: $templateContent, parameters: $parameters);
    }

    /**
     * @param array<string> $visited
     * @param array<mixed> $parameters
     */
    public function checkAndRegisterNestedContexts(
        string $templateContent,
        array &$visited = [],
        array $parameters = []
    ): void {
        $variables = TemplateParser::extractVariables($templateContent);

        foreach ($variables as $var) {
            if (in_array($var, $visited)) {
                throw new \RuntimeException("Detected recursive context: $var");
            }

            if (is_null($this->contextManager->getContext($var))) {
                $this->contextManager->addMissingContext($var);
                continue;
            }

            $visited[] = $var;

            // Check nested templates
            $nestedTemplate = $this->contextManager->getContext($var)->render($parameters);
            if (is_string($nestedTemplate) && $this->containsTemplateVariables($nestedTemplate)) {
                $this->checkAndRegisterNestedContexts($nestedTemplate, $visited, $parameters);
            }

            array_pop($visited); // Remove the current variable from the visited list after checking nested contexts
        }
    }

    private function containsTemplateVariables(string $template): bool
    {
        return preg_match('/{{.*}}/', $template) === 1;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function generateContent(string $templateName, array $parameters = []): string
    {
        $template = $this->templateManager->getTemplate($templateName);
        if (!$template) {
            throw new \RuntimeException("Template '$templateName' not found.");
        }

        $contexts = $this->contextManager->getAllContexts();
        $renderedContent = $template->render($contexts, $parameters);

        return $renderedContent;
    }

    /**
     * @return array<string>
     */
    public function getMissingContexts(): array
    {
        return $this->contextManager->getMissingContexts();
    }

    public function removeContext(string $contextName): void
    {
        $this->contextManager->removeContext($contextName);
    }
}
