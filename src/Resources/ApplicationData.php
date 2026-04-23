<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Exceptions\USPTOApplicationNotFoundException;
use RadicalDreamers\UsptoClient\Responses\ApplicationDataResponse;

class ApplicationData extends BaseResource
{
    public function find(string $applicationNumber): ApplicationDataResponse
    {
        $response = $this->client->get("/api/v1/patent/applications/{$applicationNumber}/meta-data");

        if ($response->notFound()) {
            throw new USPTOApplicationNotFoundException($applicationNumber, $response->status());
        }

        $this->ensureSuccessful($response, 'Failed to fetch application data');

        return ApplicationDataResponse::fromJson($response->json());
    }
}
