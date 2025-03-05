<?php

namespace ContentGenerator\Domain\Context;

class Context
{
    protected string $contextName;
    private ContextDataProvider $contextData;
    private mixed $renderData;

    public function __construct(string $contextName, ContextDataProvider $contextData)
    {
        $this->contextName = $contextName;
        $this->contextData = $contextData;
        $this->renderData = null;
    }

    public function getName(): string
    {
        return $this->contextName;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function render(array $parameters = []): mixed
    {
        if ($this->renderData !== null) {
            return $this->renderData;
        }

        $this->renderData = $this->contextData->getData($parameters);

        if (is_array($this->renderData)) {
            $this->renderData = array_map(
                fn($value) => is_string($value) ? $value : json_encode($value),
                $this->renderData
            );
        }

        return $this->renderData;
    }
}
