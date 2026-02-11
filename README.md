# Polydock App - amazee.io Generic

A PHP library providing generic Lagoon application implementations for Polydock. This package includes base classes for deploying and managing applications on the amazee.io Lagoon platform.

## Classes

### PolydockApp

The base class for generic Lagoon applications. Implements the full Polydock lifecycle for creating, deploying, upgrading, and removing applications on Lagoon.

```php
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockApp;

#[PolydockAppTitle('Generic Lagoon App')]
class PolydockApp extends PolydockAppBase
```

**Features:**
- Full lifecycle management (create, deploy, upgrade, remove)
- Lagoon API integration
- Health monitoring
- Instance claiming

[Full documentation →](./docs/PolydockApp.md)

### PolydockAiApp

Extends `PolydockApp` with AI infrastructure support. Use this as a base class for applications that require AI backend services.

```php
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockAiApp;

#[PolydockAppTitle('Generic Lagoon AI App')]
#[PolydockAppStoreFields]
class PolydockAiApp extends PolydockApp implements HasStoreAppFormFields
```

**Features:**
- Everything from `PolydockApp`
- AI backend client integration
- Custom Store App form fields support
- Encrypted field storage

[Full documentation →](./docs/PolydockAiApp.md)


## Extending

### Basic Extension

```php
<?php

namespace MyVendor\MyApp;

use FreedomtechHosting\PolydockApp\Attributes\PolydockAppTitle;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockApp;

#[PolydockAppTitle('My Custom App')]
class MyCustomApp extends PolydockApp
{
    public static string $version = '1.0.0';

    public static function getAppVersion(): string
    {
        return self::$version;
    }

    // Override lifecycle methods as needed
    public function postDeployAppInstance($appInstance)
    {
        $appInstance = parent::postDeployAppInstance($appInstance);
        
        // Custom post-deploy logic
        $this->info('Custom deployment complete');
        
        return $appInstance;
    }
}
```

### AI App with Custom Form Fields

```php
<?php

namespace MyVendor\MyAiApp;

use Filament\Forms;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppTitle;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppStoreFields;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockAiApp;

#[PolydockAppTitle('My AI App')]
#[PolydockAppStoreFields]
class MyAiApp extends PolydockAiApp
{
    public static function getStoreAppFormSchema(): array
    {
        return [
            Forms\Components\Section::make('AI Configuration')
                ->schema([
                    Forms\Components\TextInput::make('api_endpoint')
                        ->url()
                        ->required(),
                    Forms\Components\TextInput::make('api_key')
                        ->password()
                        ->extraAttributes(['encrypted' => true]),
                ]),
        ];
    }

    public static function getStoreAppInfolistSchema(): array
    {
        return [
            // Infolist components for the View page
        ];
    }
}
```

## Additional Documentation

- [PolydockApp Class](./docs/PolydockApp.md)
- [PolydockAiApp Class](./docs/PolydockAiApp.md)

