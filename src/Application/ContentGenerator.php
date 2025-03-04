<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Context\ContextDataProvider;
use ContentGenerator\Domain\Context\ContextRepositoryInterface;
use ContentGenerator\Domain\Template\TemplateRepositoryInterface;
use ContentGenerator\Domain\Template\TemplateParser;
use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Template\Template;

class ContentGenerator
{
    private ContextRepositoryInterface $contextRepository;
    private TemplateRepositoryInterface $templateRepository;

    public function __construct(ContextRepositoryInterface $contextRepository, TemplateRepositoryInterface $templateRepository)
    {
        $this->contextRepository = $contextRepository;
        $this->templateRepository = $templateRepository;
    }

    public function registerContext(string $contextName, ContextDataProvider $provider): void
    {
        $this->contextRepository->addContext(new Context($contextName, $provider));
        $this->contextRepository->removeMissingContext($contextName);
    }

    public function registerTemplate(string $templateName, string $templateContent): void
    {
        $template = new Template($templateName, $templateContent);
        $this->templateRepository->addTemplate($template);

        $this->checkAndRegisterNestedContexts($templateContent);
    }

    public function checkAndRegisterNestedContexts(string $templateContent, array &$visited = []): void
    {
        $variables = TemplateParser::extractVariables($templateContent);

        foreach ($variables as $var) {
            if (in_array($var, $visited)) {
                throw new \RuntimeException("Detected recursive context: $var");
            }

            if (is_null($this->contextRepository->getContext($var))) {
                $this->contextRepository->addMissingContext($var);
                continue;
            }

            $visited[] = $var;

            // Check nested templates
            $nestedTemplate = $this->contextRepository->getContext($var)->render();
            if (is_string($nestedTemplate) && $this->containsTemplateVariables($nestedTemplate)) {
                $this->checkAndRegisterNestedContexts($nestedTemplate, $visited);
            }

            array_pop($visited); // Remove the current variable from the visited list after checking nested contexts
        }
    }

    private function containsTemplateVariables(string $template): bool
    {
        return preg_match('/{{.*}}/', $template) === 1;
    }

    public function generateContent(string $templateName, array $parameters = []): string
    {
        $template = $this->templateRepository->getTemplate($templateName);
        if (!$template) {
            throw new \RuntimeException("Template '$templateName' not found.");
        }

        $contexts = $this->contextRepository->getAllContexts();
        $renderedContent = $template->render($contexts, $parameters);

        return $renderedContent;
    }


    public function getMissingContexts(): array
    {
        return $this->contextRepository->getMissingContexts();
    }

    public function removeContext(string $contextName): void
    {
        $this->contextRepository->removeContext($contextName);
    }
}
