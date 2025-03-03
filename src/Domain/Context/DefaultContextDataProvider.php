<?php

namespace ContentGenerator\Domain\Context;

class DefaultContextDataProvider implements ContextDataProvider {
    private string $contextName;
    
    public function __construct(string $contextName) {
        $this->contextName = $contextName;
    }
    
    public function getData(array $parameters = []): string {
        return "{{ {$this->contextName} }}";
    }
}
