<?php

namespace ContentGenerator\Domain\Template;

use Mustache_Engine;
use Mustache_Loader_StringLoader;

class TemplateParser {
    public static function extractVariables(string $templateContent): array {
        $mustache = new Mustache_Engine(['loader' => new Mustache_Loader_StringLoader()]);
        $tokens = $mustache->getTokenizer()->scan($templateContent);
        $variables = [];

        foreach ($tokens as $token) {
            if (array_key_exists('index', $token)) {
                $variables[] = $token['name'];
            }
        }
        return array_unique($variables);
    }
}
