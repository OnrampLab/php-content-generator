<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Template\Template;
use ContentGenerator\Domain\Template\TemplateRepositoryInterface;

class TemplateManager implements TemplateRepositoryInterface
{
    /**
     * @var array<string, Template> $templates
     */
    private array $templates = [];

    public function addTemplate(Template $template): void
    {
        $this->templates[$template->getName()] = $template;
    }

    public function getTemplate(string $templateName): ?Template
    {
        return $this->templates[$templateName] ?? null;
    }
}
