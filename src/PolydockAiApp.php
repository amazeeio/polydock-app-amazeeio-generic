<?php

declare(strict_types=1);

namespace FreedomtechHosting\PolydockAppAmazeeioGeneric;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client as AmazeeAiBackendClient;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppInstanceFields;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppStoreFields;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppTitle;
use FreedomtechHosting\PolydockApp\Contracts\HasAppInstanceFormFields;
use FreedomtechHosting\PolydockApp\Contracts\HasStoreAppFormFields;
use FreedomtechHosting\PolydockApp\PolydockServiceProviderInterface;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\Traits\UsesAmazeeAiBackend;

#[PolydockAppTitle('Generic Lagoon AI App')]
#[PolydockAppStoreFields]
#[PolydockAppInstanceFields]
class PolydockAiApp extends PolydockApp implements HasAppInstanceFormFields, HasStoreAppFormFields
{
    use UsesAmazeeAiBackend;

    protected AmazeeAiBackendClient $amazeeAiBackendClient;

    protected bool $requiresAiInfrastructure = true;

    private PolydockServiceProviderInterface $amazeeAiBackendClientProvider;

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

    /**
     * Get custom form fields for App Instance configuration.
     *
     * Inherits from parent and adds AI-specific instance fields.
     * Use array_merge(parent::getAppInstanceFormSchema(), [...]) for inheritance.
     *
     * @return array<\Filament\Forms\Components\Component>
     */
    #[\Override]
    public static function getAppInstanceFormSchema(): array
    {
        return [];
    }

    /**
     * Get infolist schema for displaying App Instance configuration.
     *
     * Inherits from parent and adds AI-specific instance display fields.
     * Use array_merge(parent::getAppInstanceInfolistSchema(), [...]) for inheritance.
     *
     * @return array<\Filament\Infolists\Components\Component>
     */
    #[\Override]
    public static function getAppInstanceInfolistSchema(): array
    {
        return [];
    }
}
