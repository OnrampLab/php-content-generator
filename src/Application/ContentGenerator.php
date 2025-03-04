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
    }

    public function registerTemplate(string $templateName, string $templateContent): void
    {
        $template = new Template($templateName, $templateContent);
        $this->templateRepository->addTemplate($template);

        $this->checkAndRegisterContexts($templateContent);
    }

    private function checkAndRegisterContexts(string $templateContent): void
    {
        $variables = TemplateParser::extractVariables($templateContent);

        foreach ($variables as $var) {
            if (is_null($this->contextRepository->getContext($var))) {
                $this->contextRepository->addMissingContext($var);
                continue;
            }
            // Check nested templates
            $nestedTemplate = $this->contextRepository->getContext($var)->render();
            if (is_string($nestedTemplate)) {
                $this->checkAndRegisterContexts($nestedTemplate);
            }
        }
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
}
