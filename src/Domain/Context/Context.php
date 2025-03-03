<?php

namespace ContentGenerator\Domain\Context;

class Context {
    protected string $contextName;
    private ContextDataProvider $contextData;

    public function __construct(string $contextName, ContextDataProvider $contextData) {
        $this->contextName = $contextName;
        $this->contextData = $contextData;
    }

    public function getName(): string {
        return $this->contextName;
    }

    public function render(array $parameters = []): mixed {
        $data = $this->contextData->getData($parameters);

        if (is_array($data)) {
            return array_map(fn($value) => is_string($value) ? $value : json_encode($value), $data);
        }
        return $data;
    }


    public function isDefault(): bool {
        return $this->contextData instanceof DefaultContextDataProvider;
    }
}
