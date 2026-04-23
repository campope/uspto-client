<?php

namespace RadicalDreamers\UsptoClient\Tests;

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
}
