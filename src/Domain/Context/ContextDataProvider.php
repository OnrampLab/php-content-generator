<?php

namespace ContentGenerator\Domain\Context;

interface ContextDataProvider
{
    /**
     * @param array<mixed> $parameters
     */
    public function getData(array $parameters = []): mixed;
}
