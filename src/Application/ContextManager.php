<?php

namespace ContentGenerator\Application;

use ContentGenerator\Domain\Context\Context;
use ContentGenerator\Domain\Context\ContextRepositoryInterface;

class ContextManager implements ContextRepositoryInterface
{
    /**
     * @var array<string, Context> $contexts
     */
    private array $contexts = [];

    /**
     * @var array<string> $missingContexts
     */
    private array $missingContexts = [];

    public function addContext(Context $context): void
    {
        $this->contexts[$context->getName()] = $context;
    }

    public function getContext(string $contextName): ?Context
    {
        return $this->contexts[$contextName] ?? null;
    }

    /**
     * @return array<string, Context>
     */
    public function getAllContexts(): array
    {
        return $this->contexts;
    }

    public function addMissingContext(string $contextName): void
    {
        $this->missingContexts[] = $contextName;
    }

    /**
     * @return array<string>
     */
    public function getMissingContexts(): array
    {
        return $this->missingContexts;
    }

    public function removeContext(string $contextName): void
    {
        unset($this->contexts[$contextName]);
    }

    public function removeMissingContext(string $contextName): void
    {
        $this->missingContexts = array_filter($this->missingContexts, fn($context) => $context !== $contextName);
    }
}
