<?php

namespace ContentGenerator\Domain\Template;

interface TemplateRepositoryInterface {
    public function addTemplate(Template $template): void;
    public function getTemplate(string $templateName): ?Template;
}
