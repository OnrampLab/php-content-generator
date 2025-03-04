<?php

namespace ContentGenerator\Domain\Context;

interface ContextRepositoryInterface
{
    public function addContext(Context $context): void;
    public function getContext(string $contextName): ?Context;
    public function getAllContexts(): array;
    public function addMissingContext(string $contextName): void;
    public function getMissingContexts(): array;
}
