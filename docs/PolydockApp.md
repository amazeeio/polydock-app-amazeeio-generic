# PolydockApp

The `PolydockApp` class is a generic Lagoon application implementation for Polydock. It provides the foundation for deploying and managing applications on the Lagoon platform.

## Overview

```php
namespace FreedomtechHosting\PolydockAppAmazeeioGeneric;

#[PolydockAppTitle('Generic Lagoon App')]
class PolydockApp extends PolydockAppBase
```

## Attributes

| Attribute | Value | Description |
|-----------|-------|-------------|
| `#[PolydockAppTitle]` | `'Generic Lagoon App'` | Human-readable title displayed in admin UI dropdowns |

## Required Variables

The following variables must be set for the app to function:

| Variable Name | Description |
|---------------|-------------|
| `lagoon-deploy-git` | Git repository URL for deployment |
| `lagoon-deploy-branch` | Git branch to deploy |
| `lagoon-deploy-region-id` | Lagoon region identifier |
| `lagoon-deploy-private-key` | SSH private key for Git access |
| `lagoon-deploy-organization-id` | Lagoon organization ID |
| `lagoon-deploy-project-prefix` | Prefix for generated project names |
| `lagoon-project-name` | The Lagoon project name |
| `lagoon-deploy-group-name` | Lagoon group name |

## Lifecycle Methods

The `PolydockApp` class implements the full Polydock lifecycle through traits:

### Create Phase

| Trait | Method | Description |
|-------|--------|-------------|
| `PreCreateAppInstanceTrait` | `preCreateAppInstance()` | Prepares the app instance before creation |
| `CreateAppInstanceTrait` | `createAppInstance()` | Creates the Lagoon project |
| `PostCreateAppInstanceTrait` | `postCreateAppInstance()` | Post-creation setup and configuration |

### Deploy Phase

| Trait | Method | Description |
|-------|--------|-------------|
| `PreDeployAppInstanceTrait` | `preDeployAppInstance()` | Pre-deployment validation |
| `DeployAppInstanceTrait` | `deployAppInstance()` | Triggers Lagoon deployment |
| `PostDeployAppInstanceTrait` | `postDeployAppInstance()` | Post-deployment configuration |
| `PollDeployProgressAppInstanceTrait` | `pollAppInstanceDeploymentProgress()` | Monitors deployment status |

### Upgrade Phase

| Trait | Method | Description |
|-------|--------|-------------|
| `PreUpgradeAppInstanceTrait` | `preUpgradeAppInstance()` | Pre-upgrade preparation |
| `UpgradeAppInstanceTrait` | `upgradeAppInstance()` | Performs the upgrade |
| `PostUpgradeAppInstanceTrait` | `postUpgradeAppInstance()` | Post-upgrade cleanup |
| `PollUpgradeProgressAppInstanceTrait` | `pollAppInstanceUpgradeProgress()` | Monitors upgrade status |

### Remove Phase

| Trait | Method | Description |
|-------|--------|-------------|
| `PreRemoveAppInstanceTrait` | `preRemoveAppInstance()` | Pre-removal preparation |
| `RemoveAppInstanceTrait` | `removeAppInstance()` | Removes the Lagoon project |
| `PostRemoveAppInstanceTrait` | `postRemoveAppInstance()` | Post-removal cleanup |

### Health Monitoring

| Trait | Method | Description |
|-------|--------|-------------|
| `PollHealthProgressAppInstanceTrait` | `pollAppInstanceHealthStatus()` | Monitors app health |

### Claim

| Trait | Method | Description |
|-------|--------|-------------|
| `ClaimAppInstanceTrait` | `claimAppInstance()` | Assigns instance to a user |

## Key Methods

### Lagoon Client Management

```php
// Set up the Lagoon client from an app instance
public function setLagoonClientFromAppInstance(PolydockAppInstanceInterface $appInstance): void

// Ping the Lagoon API to verify connectivity
public function pingLagoonAPI(): bool
```

### Validation Methods

```php
// Verify all required Lagoon values are set
public function verifyLagoonValuesAreAvailable(
    PolydockAppInstanceInterface $appInstance, 
    $logContext = []
): bool

// Verify project name is available
public function verifyLagoonProjectNameIsAvailable(
    PolydockAppInstanceInterface $appInstance, 
    $logContext = []
): bool

// Verify project ID is available
public function verifyLagoonProjectIdIsAvailable(
    PolydockAppInstanceInterface $appInstance, 
    $logContext = []
): bool

// Combined validation helper
public function validateAppInstanceStatusIsExpectedAndConfigureLagoonClientAndVerifyLagoonValues(
    PolydockAppInstanceInterface $appInstance,
    PolydockAppInstanceStatus $expectedStatus,
    $logContext = [],
    bool $testLagoonPing = true,
    bool $verifyLagoonValuesAreAvailable = true,
    bool $verifyLagoonProjectNameIsAvailable = true,
    bool $verifyLagoonProjectIdIsAvailable = true
): void
```

### Variable Management

```php
// Add or update a Lagoon project variable
public function addOrUpdateLagoonProjectVariable(
    PolydockAppInstanceInterface $appInstance, 
    $variableName, 
    $variableValue, 
    $variableScope
): void
```

## Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$version` | `string` | `'0.0.1'` | App version |
| `$requiresAiInfrastructure` | `bool` | `false` | Whether AI infrastructure is required |
| `$lagoonClient` | `LagoonClient` | - | Lagoon API client instance |
| `$engine` | `PolydockEngineInterface` | - | Polydock engine reference |

## Usage Example

```php
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockApp;

// The PolydockApp is typically instantiated by the Polydock engine
// based on the polydock_app_class stored in the PolydockStoreApp model

// Example of extending PolydockApp for a custom implementation
class MyCustomApp extends PolydockApp
{
    public static string $version = '1.0.0';
    
    public static function getAppVersion(): string
    {
        return self::$version;
    }
    
    // Override lifecycle methods as needed
    public function postDeployAppInstance(
        PolydockAppInstanceInterface $appInstance
    ): PolydockAppInstanceInterface {
        // Call parent implementation
        $appInstance = parent::postDeployAppInstance($appInstance);
        
        // Add custom post-deploy logic
        $this->info('Custom post-deploy logic executed');
        
        return $appInstance;
    }
}
```

## See Also

- [PolydockAiApp](./PolydockAiApp.md) - Extended version with AI infrastructure support
- [polydock-app-lib README](../../polydock-app-lib/README.md) - Core library documentation
