<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Context\ContextRepositoryInterface;
use ContentGenerator\Domain\Context\DefaultContextDataProvider;

class ContextManager implements ContextRepositoryInterface {
    private array $contexts = [];

    public function addContext(Context $context): void {
        $this->contexts[$context->getName()] = $context;
    }

    public function getContext(string $contextName): Context {
        return $this->contexts[$contextName] ?? new Context($contextName, new DefaultContextDataProvider($contextName));
    }

    public function getAllContexts(): array {
        return $this->contexts;
    }

    public function getMissingContexts(): array {
        return array_keys(array_filter($this->contexts, fn($context) => $context->isDefault()));
    }
}
