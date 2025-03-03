<?php

namespace ContentGenerator\Domain\Context;

interface ContextRepositoryInterface {
    public function addContext(Context $context): void;
    public function getContext(string $contextName): Context;
    public function getAllContexts(): array;
    public function getMissingContexts(): array;
}
