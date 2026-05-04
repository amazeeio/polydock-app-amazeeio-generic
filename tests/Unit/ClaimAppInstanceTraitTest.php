<?php

declare(strict_types=1);

namespace Tests\Unit;

use FreedomtechHosting\FtLagoonPhp\Client as LagoonClient;
use FreedomtechHosting\PolydockApp\Enums\PolydockAppInstanceStatus;
use FreedomtechHosting\PolydockApp\PolydockAppInterface;
use FreedomtechHosting\PolydockApp\PolydockAppInstanceInterface;
use FreedomtechHosting\PolydockApp\PolydockAppLoggerInterface;
use FreedomtechHosting\PolydockApp\PolydockEngineInterface;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockApp;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClaimAppInstanceTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_claim_uses_frontend_route_when_no_claim_script_exists(): void
    {
        $lagoonClient = Mockery::mock(LagoonClient::class);
        $lagoonClient->shouldReceive('getProjectEnvironmentByName')
            ->once()
            ->with('example-project', 'main')
            ->andReturn([
                'name' => 'main',
                'route' => 'https://nginx.example.com',
                'routes' => 'nginx.example.com,frontend.example.com',
            ]);

        $app = new TestPolydockApp('Test App', 'Test description', 'Test author', 'https://example.com', 'test@example.com');
        $app->setTestLagoonClient($lagoonClient);
        $appInstance = new TestPolydockAppInstance([
            'lagoon-project-name' => 'example-project',
            'lagoon-deploy-branch' => 'main',
            'lagoon-project-id' => '123',
            'lagoon-claim-script' => '',
        ]);

        $app->claimAppInstance($appInstance);

        $this->assertSame(PolydockAppInstanceStatus::POLYDOCK_CLAIM_COMPLETED, $appInstance->getStatus());
        $this->assertSame('https://frontend.example.com', $appInstance->appUrl);
        $this->assertSame('https://frontend.example.com', $appInstance->oneTimeLoginUrl);
        $this->assertSame('https://frontend.example.com', $appInstance->getKeyValue('claim-command-output'));
    }

    public function test_claim_uses_primary_route_when_no_frontend_route_exists(): void
    {
        $lagoonClient = Mockery::mock(LagoonClient::class);
        $lagoonClient->shouldReceive('getProjectEnvironmentByName')
            ->once()
            ->with('plain-nginx-project', 'main')
            ->andReturn([
                'name' => 'main',
                'route' => 'plain-nginx-project-main.example.com',
                'routes' => 'plain-nginx-project-main.example.com',
            ]);

        $app = new TestPolydockApp('Test App', 'Test description', 'Test author', 'https://example.com', 'test@example.com');
        $app->setTestLagoonClient($lagoonClient);
        $appInstance = new TestPolydockAppInstance([
            'lagoon-project-name' => 'plain-nginx-project',
            'lagoon-deploy-branch' => 'main',
            'lagoon-project-id' => '456',
            'lagoon-claim-script' => null,
        ]);

        $app->claimAppInstance($appInstance);

        $this->assertSame(PolydockAppInstanceStatus::POLYDOCK_CLAIM_COMPLETED, $appInstance->getStatus());
        $this->assertSame('https://plain-nginx-project-main.example.com', $appInstance->appUrl);
        $this->assertSame('https://plain-nginx-project-main.example.com', $appInstance->getKeyValue('claim-command-output'));
    }
}

class TestPolydockApp extends PolydockApp
{
    private LagoonClient $testLagoonClient;

    public function setTestLagoonClient(LagoonClient $testLagoonClient): self
    {
        $this->testLagoonClient = $testLagoonClient;

        return $this;
    }

    #[\Override]
    public function validateAppInstanceStatusIsExpectedAndConfigureLagoonClientAndVerifyLagoonValues(
        PolydockAppInstanceInterface $appInstance,
        PolydockAppInstanceStatus $expectedStatus,
        $logContext = [],
        bool $testLagoonPing = true,
        bool $verifyLagoonValuesAreAvailable = true,
        bool $verifyLagoonProjectNameIsAvailable = true,
        bool $verifyLagoonProjectIdIsAvailable = true
    ): void {
        $this->validateAppInstanceStatusIsExpected($appInstance, $expectedStatus);
        $this->lagoonClient = $this->testLagoonClient;
    }

    #[\Override]
    public function addOrUpdateLagoonProjectVariable(PolydockAppInstanceInterface $appInstance, $variableName, $variableValue, $variableScope): void
    {
        $appInstance->storeKeyValue($variableName, $variableValue);
    }
}

class TestPolydockAppInstance implements PolydockAppInstanceInterface
{
    private ?PolydockAppInterface $app = null;

    private string $name = 'test-instance';

    private string $appType = TestPolydockApp::class;

    private PolydockAppInstanceStatus $status = PolydockAppInstanceStatus::PENDING_POLYDOCK_CLAIM;

    private string $statusMessage = '';

    private array $data = [];

    public ?string $appUrl = null;

    public ?string $oneTimeLoginUrl = null;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setApp(PolydockAppInterface $app): self
    {
        $this->app = $app;

        return $this;
    }

    public function getApp(): PolydockAppInterface
    {
        if (! $this->app instanceof PolydockAppInterface) {
            throw new \RuntimeException('App not set');
        }

        return $this->app;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAppType(string $appType): self
    {
        $this->appType = $appType;

        return $this;
    }

    public function getAppType(): string
    {
        return $this->appType;
    }

    public function getStatus(): PolydockAppInstanceStatus
    {
        return $this->status;
    }

    public function setStatus(PolydockAppInstanceStatus $status, string $statusMessage = ''): self
    {
        $this->status = $status;
        if ($statusMessage !== '') {
            $this->statusMessage = $statusMessage;
        }

        return $this;
    }

    public function setStatusMessage(string $statusMessage): self
    {
        $this->statusMessage = $statusMessage;

        return $this;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function storeKeyValue(string $key, mixed $value): PolydockAppInstanceInterface
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function getKeyValue(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function deleteKeyValue(string $key): self
    {
        unset($this->data[$key]);

        return $this;
    }

    public function info(string $message, array $context = []): self
    {
        return $this;
    }

    public function error(string $message, array $context = []): self
    {
        return $this;
    }

    public function warning(string $message, array $context = []): self
    {
        return $this;
    }

    public function debug(string $message, array $context = []): self
    {
        return $this;
    }

    public function getLogger(): PolydockAppLoggerInterface
    {
        throw new \BadMethodCallException('Not required for this test');
    }

    public function setLogger(PolydockAppLoggerInterface $logger): self
    {
        return $this;
    }

    public function setEngine(PolydockEngineInterface $engine): self
    {
        return $this;
    }

    public function getEngine(): PolydockEngineInterface
    {
        throw new \BadMethodCallException('Not required for this test');
    }

    public function generateUniqueProjectName(string $prefix): string
    {
        return $prefix.'-test';
    }

    public function save(array $options = [])
    {
        return true;
    }

    public function setAppUrl(string $url, ?string $oneTimeLoginUrl = null, ?int $numberOfHoursForOneTimeLoginUrl = 24): self
    {
        $this->appUrl = trim($url);

        if ($oneTimeLoginUrl !== null) {
            $this->setOneTimeLoginUrl($oneTimeLoginUrl, $numberOfHoursForOneTimeLoginUrl ?? 24, true);
        }

        return $this;
    }

    public function setOneTimeLoginUrl(string $url, int $numberOfHours = 24, bool $setOnlyDontSave = false): self
    {
        $this->oneTimeLoginUrl = trim($url);

        return $this;
    }

    public function getGeneratedAppAdminUsername(): string
    {
        return '';
    }

    public function getGeneratedAppAdminPassword(): string
    {
        return '';
    }
}
