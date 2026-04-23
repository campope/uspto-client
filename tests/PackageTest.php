<?php

namespace RadicalDreamers\UsptoClient\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use RadicalDreamers\UsptoClient\Exceptions\USPTORequestException;
use RadicalDreamers\UsptoClient\Resources\PatentApplications;
use RadicalDreamers\UsptoClient\USPTO;
use RadicalDreamers\UsptoClient\USPTOClient;
use RadicalDreamers\UsptoClient\USPTOService;

class PackageTest extends TestCase
{
    public function test_it_registers_package_services(): void
    {
        $this->assertInstanceOf(USPTOClient::class, $this->app->make(USPTOClient::class));
        $this->assertInstanceOf(USPTOService::class, $this->app->make(USPTOService::class));
    }

    public function test_it_resolves_resources_through_service(): void
    {
        $resource = $this->app->make(USPTOService::class)->patentApplications();

        $this->assertInstanceOf(PatentApplications::class, $resource);
    }

    public function test_it_resolves_resources_through_facade(): void
    {
        $this->assertInstanceOf(PatentApplications::class, USPTO::patentApplications());
    }

    public function test_failed_requests_include_debug_details(): void
    {
        Http::fake([
            'https://api.uspto.gov/api/v1/patent/applications/*' => Http::response([
                'error' => 'Application not accessible',
            ], 403),
        ]);

        try {
            USPTO::patentApplications()->find('17912345');
            $this->fail('Expected USPTORequestException was not thrown.');
        } catch (USPTORequestException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
            $this->assertStringContainsString('Failed to fetch patent application data', $exception->getMessage());
            $this->assertStringContainsString('HTTP 403', $exception->getMessage());
            $this->assertStringContainsString('/api/v1/patent/applications/17912345', $exception->getMessage());
            $this->assertStringContainsString('Application not accessible', $exception->getMessage());
        }

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.uspto.gov/api/v1/patent/applications/17912345');
    }
}
