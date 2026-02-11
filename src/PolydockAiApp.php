<?php

namespace FreedomtechHosting\PolydockAppAmazeeioGeneric;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client as AmazeeAiBackendClient;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppStoreFields;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppTitle;
use FreedomtechHosting\PolydockApp\Contracts\HasStoreAppFormFields;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\Traits\UsesAmazeeAiBackend;

#[PolydockAppTitle('Generic Lagoon AI App')]
#[PolydockAppStoreFields]
class PolydockAiApp extends PolydockApp implements HasStoreAppFormFields
{
    use UsesAmazeeAiBackend;

    protected AmazeeAiBackendClient $amazeeAiBackendClient;

    protected bool $requiresAiInfrastructure = true;

    /**
     * Get custom form fields for Store App configuration.
     *
     * Override this method in subclasses to provide app-specific configuration fields.
     * See docs/PolydockAiApp.md for example implementations.
     *
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function getStoreAppFormSchema(): array
    {
        return [];
    }

    /**
     * Get infolist schema for displaying Store App configuration.
     *
     * Override this method in subclasses to provide app-specific display fields.
     * See docs/PolydockAiApp.md for example implementations.
     *
     * @return array<\Filament\Infolists\Components\Component>
     */
    public static function getStoreAppInfolistSchema(): array
    {
        return [];
    }
}
