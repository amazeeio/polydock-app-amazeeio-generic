<?php

namespace FreedomtechHosting\PolydockAppAmazeeioGeneric;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client as AmazeeAiBackendClient;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\Traits\UsesAmazeeAiBackend;

class PolydockAiApp extends PolydockApp
{
    use UsesAmazeeAiBackend;

    protected AmazeeAiBackendClient $amazeeAiBackendClient;

    protected bool $requiresAiInfrastructure = true;
}
