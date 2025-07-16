# Polydock App for amazee.io Lagoon

A generic Polydock application for deploying and managing applications on the amazee.io Lagoon hosting platform.

It provides a robust, lifecycle-based approach to orchestrating application instances, from creation and deployment to removal and upgrades.

The application is designed to be extensible, with a core `PolydockApp` that handles standard Lagoon operations and an optional `PolydockAiApp` for integrations requiring an AI backend infrastructure.


## Core Features

- **Full Application Lifecycle Management:** Handles the entire lifecycle of an application instance on Lagoon, including:
  - **Create:** Provisions a new project in Lagoon.
  - **Deploy:** Deploys a specific Git branch to an environment.
  - **Remove:** Decommissions a project environment.
  - **Upgrade:** Updates project-level configurations.
  - **Claim:** Executes a custom script to "claim" an instance and retrieve a URL.
- **Asynchronous Operation Support:** Includes logic for polling the progress of long-running tasks like deployments and upgrades.
- **Extensible Hooks:** Provides `Pre` and `Post` operation hooks (e.g., `preCreateAppInstance`, `postDeployAppInstance`) for custom logic at each stage of the lifecycle.
- **AI Backend Integration (Optional):** The `PolydockAiApp` class seamlessly integrates with the amazee AI Backend to provision dedicated AI resources, including database credentials and LLM API tokens, for your application.
- **Configuration via Variables:** All Lagoon and application settings are managed via Polydock key-value variables.


## Architecture Overview

The project follows a modular, trait-based architecture to promote code reuse and separation of concerns.

- **`PolydockApp.php`:** The main application class that composes the various traits to define the complete application lifecycle workflow. It manages the connection to the Lagoon API and orchestrates all operations.
- **`PolydockAiApp.php`:** Extends `PolydockApp` and mixes in the `UsesamazeeAiBackend` trait, adding the capability to communicate with and provision resources from the amazee AI Backend.
- **`Traits`:** The core logic is encapsulated in PHP traits, with each set of traits corresponding to a specific lifecycle operation. This makes the system easy to understand and extend.


### Lifecycle Operations and Traits

The application instance lifecycle is broken down into the following stages, each implemented with one or more traits:

- **Claim:**
  - `ClaimAppInstanceTrait`: Runs a claim script and sets the application URL.
- **Create:**
  - `PreCreateAppInstanceTrait`: Validates configuration before creation.
  - `CreateAppInstanceTrait`: Creates the project in Lagoon.
  - `PostCreateAppInstanceTrait`: Configures the project after creation (e.g., adds user groups, sets environment variables).
- **Deploy:**
  - `PreDeployAppInstanceTrait`: Validates the project exists before deploying.
  - `DeployAppInstanceTrait`: Triggers a new deployment in Lagoon.
  - `PollDeployProgressAppInstanceTrait`: Polls the Lagoon API for the status of an ongoing deployment.
  - `PostDeployAppInstanceTrait`: Runs post-deployment scripts.
- **Remove:**
  - `PreRemoveAppInstanceTrait`: Validates the project before removal.
  - `RemoveAppInstanceTrait`: Deletes the environment from Lagoon.
  - `PostRemoveAppInstanceTrait`: Performs cleanup tasks after removal.
- **Upgrade:**
  - `PreUpgradeAppInstanceTrait`: Validates the project before upgrading.
  - `UpgradeAppInstanceTrait`: Applies updates to the project configuration.
  - `PollUpgradeProgressAppInstanceTrait`: (TODO) Polls for upgrade completion.
  - `PostUpgradeAppInstanceTrait`: Performs tasks after an upgrade is complete.
- **Health:**
  - `PollHealthProgressAppInstanceTrait`: (TODO) Implements application health checks.


## Configuration

To use this Polydock app, you must provide several key-value pairs that define the connection to Lagoon and the specifics of the application to be deployed.


### Required Variables

- `lagoon-deploy-git`: The Git repository URL for the application.
- `lagoon-deploy-branch`: The Git branch to deploy.
- `lagoon-deploy-region-id`: The numeric ID of the target Lagoon region.
- `lagoon-deploy-private-key`: The SSH private key for accessing the Git repository.
- `lagoon-deploy-organization-id`: The numeric ID of the Lagoon organization.
- `lagoon-deploy-project-prefix`: A prefix for the Lagoon project name.
- `lagoon-project-name`: The name of the project in Lagoon.
- `lagoon-deploy-group-name`: The Lagoon group to associate with the project.


### Optional AI Backend Variables

When using `PolydockAiApp`, the following variables are required:

- `amazee-ai-backend-region-id`: The ID of the AI backend region.
- `amazee-ai-backend-user-email`: (Optional) An email to associate with the AI backend user.


## Usage

This application is intended to be used within a Polydock environment. You would typically define a new App Type that uses either `FreedomtechHosting\PolydockAppamazeeioGeneric\PolydockApp` or `FreedomtechHosting\PolydockAppamazeeioGeneric\PolydockAiApp` as its handler class.

The Polydock engine will then call the appropriate methods on the app class instance (e.g., `createAppInstance`, `deployAppInstance`) based on the status of the `PolydockAppInstance`.

