<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Context\ContextRepositoryInterface;

class ContextManager implements ContextRepositoryInterface {
    private array $contexts = [];
    private array $missingContexts = [];

    public function addContext(Context $context): void {
        $this->contexts[$context->getName()] = $context;
    }

    public function getContext(string $contextName): ?Context {
        return $this->contexts[$contextName] ?? null;
    }

    public function getAllContexts(): array {
        return $this->contexts;
    }

    public function addMissingContext(string $contextName): void {
        $this->missingContexts[] = $contextName;
    }

    public function getMissingContexts(): array {
        return $this->missingContexts;
    }
}
