<?php

namespace ContentGenerator\Domain\Context;

interface ContextDataProvider {
    public function getData(array $parameters = []): mixed;
}
