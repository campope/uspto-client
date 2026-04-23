<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\SearchResponse;

class PatentApplications extends BaseResource
{
    public function find(string $applicationNumber): SearchResponse
    {
        $response = $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/applications/{$applicationNumber}"),
            'Failed to fetch patent application data',
        );

        return SearchResponse::fromJson($response->json());
    }

    public function search(array $payload = []): SearchResponse
    {
        $response = $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/applications/search', $payload),
            'Failed to search patent applications',
        );

        return SearchResponse::fromJson($response->json());
    }

    public function query(array $query = []): SearchResponse
    {
        $response = $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/applications/search', $query),
            'Failed to query patent applications',
        );

        return SearchResponse::fromJson($response->json());
    }

    public function download(array $payload = []): mixed
    {
        $response = $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/applications/search/download', $payload),
            'Failed to download patent application search results',
        );

        $decoded = $this->decodeJsonOrBody($response);

        return is_array($decoded) ? SearchResponse::fromJson($decoded) : $decoded;
    }

    public function downloadQuery(array $query = []): mixed
    {
        $response = $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/applications/search/download', $query),
            'Failed to download patent application query results',
        );

        $decoded = $this->decodeJsonOrBody($response);

        return is_array($decoded) ? SearchResponse::fromJson($decoded) : $decoded;
    }
}
