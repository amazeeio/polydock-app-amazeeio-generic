# PolydockAiApp

The `PolydockAiApp` class extends `PolydockApp` to add AI infrastructure support. It's designed for applications that require AI backend services.

## Overview

```php
namespace FreedomtechHosting\PolydockAppAmazeeioGeneric;

#[PolydockAppTitle('Generic Lagoon AI App')]
#[PolydockAppStoreFields]
class PolydockAiApp extends PolydockApp implements HasStoreAppFormFields
```

## Attributes

| Attribute                   | Value                     | Description                                |
| --------------------------- | ------------------------- | ------------------------------------------ |
| `#[PolydockAppTitle]`       | `'Generic Lagoon AI App'` | Human-readable title displayed in admin UI |
| `#[PolydockAppStoreFields]` | -                         | Enables custom Store App form fields       |

## Interfaces

| Interface               | Description                                                       |
| ----------------------- | ----------------------------------------------------------------- |
| `HasStoreAppFormFields` | Provides custom form/infolist schemas for Store App configuration |

## Properties

| Property                    | Type                    | Default | Description                                   |
| --------------------------- | ----------------------- | ------- | --------------------------------------------- |
| `$requiresAiInfrastructure` | `bool`                  | `true`  | Indicates this app requires AI infrastructure |
| `$amazeeAiBackendClient`    | `AmazeeAiBackendClient` | -       | AI backend client instance                    |

## Traits

In addition to all traits from `PolydockApp`, this class uses:

| Trait                 | Description                            |
| --------------------- | -------------------------------------- |
| `UsesAmazeeAiBackend` | Provides AI backend client integration |

## Custom Store App Form Fields

The `PolydockAiApp` implements `HasStoreAppFormFields` interface, allowing it to define custom configuration fields that appear in the admin panel when creating/editing a Store App.

### Implementation

```php
public static function getStoreFormSchema(): array
{
    return [];
}

public static function getStoreInfolistSchema(): array
{
    return [];
}
```

By default, these methods return empty arrays. Subclasses should override them to provide app-specific configuration fields.

### Example Implementation

Here's an example of how to implement custom form fields for an AI app:

```php
use Filament\Forms;
use Filament\Infolists;

class MyAiApp extends PolydockAiApp
{
    public static function getStoreFormSchema(): array
    {
        return [
            Forms\Components\Section::make('AI Infrastructure Settings')
                ->description('Configure the AI backend connection for this app.')
                ->schema([
                    Forms\Components\TextInput::make('ai_backend_endpoint')
                        ->label('AI Backend Endpoint')
                        ->placeholder('https://ai-backend.example.com')
                        ->url()
                        ->required()
                        ->helperText('The URL of the Amazee AI backend service.'),

                    Forms\Components\TextInput::make('ai_backend_api_key')
                        ->label('AI Backend API Key')
                        ->password()
                        ->revealable()
                        ->extraAttributes(['encrypted' => true])
                        ->helperText('API key for authenticating with the AI backend. Stored encrypted.'),

                    Forms\Components\Select::make('ai_model_tier')
                        ->label('Model Tier')
                        ->options([
                            'basic' => 'Basic (GPT-3.5 equivalent)',
                            'standard' => 'Standard (GPT-4 equivalent)',
                            'premium' => 'Premium (Latest models)',
                        ])
                        ->default('standard')
                        ->helperText('Select the AI model tier for this app.'),

                    Forms\Components\Toggle::make('ai_enable_streaming')
                        ->label('Enable Streaming Responses')
                        ->default(true)
                        ->helperText('Allow AI responses to stream in real-time.'),

                    Forms\Components\TextInput::make('ai_max_tokens')
                        ->label('Max Tokens per Request')
                        ->numeric()
                        ->default(4096)
                        ->minValue(100)
                        ->maxValue(128000)
                        ->helperText('Maximum number of tokens allowed per AI request.'),
                ])
                ->collapsible(),
        ];
    }

    public static function getStoreInfolistSchema(): array
    {
        return [
            Infolists\Components\Section::make('AI Infrastructure Settings')
                ->schema([
                    Infolists\Components\TextEntry::make('ai_backend_endpoint')
                        ->label('AI Backend Endpoint')
                        ->url()
                        ->openUrlInNewTab(),

                    Infolists\Components\TextEntry::make('ai_backend_api_key')
                        ->label('AI Backend API Key')
                        ->formatStateUsing(fn ($state) => $state ? '••••••••' : 'Not configured')
                        ->badge()
                        ->color(fn ($state) => $state ? 'success' : 'warning'),

                    Infolists\Components\TextEntry::make('ai_model_tier')
                        ->label('Model Tier')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            'basic' => 'Basic',
                            'standard' => 'Standard',
                            'premium' => 'Premium',
                            default => $state ?? 'Not set',
                        }),

                    Infolists\Components\IconEntry::make('ai_enable_streaming')
                        ->label('Streaming Enabled')
                        ->boolean(),

                    Infolists\Components\TextEntry::make('ai_max_tokens')
                        ->label('Max Tokens')
                        ->numeric(),
                ])
                ->columns(2)
                ->collapsible(),
        ];
    }
}
```

### Field Name Prefixing

All custom field names are automatically prefixed with `app_config_` when stored. Define fields without the prefix:

```php
// Define as:
Forms\Components\TextInput::make('ai_backend_endpoint')

// Stored as:
// app_config_ai_backend_endpoint
```

### Encrypted Fields

Mark sensitive fields for encrypted storage:

```php
Forms\Components\TextInput::make('api_key')
    ->password()
    ->extraAttributes(['encrypted' => true])
```

### Accessing Custom Field Values

Access custom field values in lifecycle methods:

```php
public function preDeployAppInstance(
    PolydockAppInstanceInterface $appInstance
): PolydockAppInstanceInterface {
    // Get the Store App and access custom field values
    $storeApp = $appInstance->getStore();

    $endpoint = $storeApp->getPolydockVariableValue('app_config_ai_backend_endpoint');
    $apiKey = $storeApp->getPolydockVariableValue('app_config_ai_backend_api_key'); // Auto-decrypted
    $modelTier = $storeApp->getPolydockVariableValue('app_config_ai_model_tier');

    $this->info('Configuring AI with endpoint: ' . $endpoint);

    return parent::preDeployAppInstance($appInstance);
}
```

## Usage Example

```php
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockAiApp;

class MyCustomAiApp extends PolydockAiApp
{
    public static string $version = '1.0.0';

    public static function getAppVersion(): string
    {
        return self::$version;
    }

    // Override to add custom variables
    public static function getAppDefaultVariableDefinitions(): array
    {
        return array_merge(
            parent::getAppDefaultVariableDefinitions(),
            [
                new PolydockAppVariableDefinitionBase('my-custom-variable'),
            ]
        );
    }

    // Implement custom form fields
    public static function getStoreFormSchema(): array
    {
        return [
            // Your custom Filament form components
        ];
    }

    public static function getStoreInfolistSchema(): array
    {
        return [
            // Your custom Filament infolist components
        ];
    }
}
```

## Supported Form Field Types

Any Filament form component can be used:

- `TextInput` - Text, email, URL, password inputs
- `Textarea` - Multi-line text
- `Select` - Dropdown selections
- `Toggle` - Boolean switches
- `Checkbox` / `CheckboxList` - Checkbox inputs
- `Radio` - Radio button groups
- `DatePicker` / `DateTimePicker` - Date inputs
- `FileUpload` - File uploads (stored as paths)
- `KeyValue` - Key-value pair editors
- `Repeater` - Repeatable field groups
- `Section` / `Grid` / `Fieldset` - Layout components

## Validation

Use standard Filament validation:

```php
Forms\Components\TextInput::make('port')
    ->numeric()
    ->minValue(1)
    ->maxValue(65535)
    ->required(),

Forms\Components\TextInput::make('webhook_url')
    ->url()
    ->rules(['starts_with:https://']),
```

## See Also

- [PolydockApp](./PolydockApp.md) - Base class documentation
- [polydock-app-lib README](../../polydock-app-lib/README.md) - Core library documentation
