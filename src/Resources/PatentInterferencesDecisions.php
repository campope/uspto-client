<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PatentInterferencesDecisions extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/interferences/decisions/search', $payload),
            'Failed to search patent interference decisions',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/interferences/decisions/search', $query),
            'Failed to query patent interference decisions',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/patent/interferences/decisions/search/download', $payload),
                'Failed to download patent interference decisions',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/patent/interferences/decisions/search/download', $query),
                'Failed to download patent interference decisions query',
            ),
        );
    }

    public function find(string $documentIdentifier): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/interferences/decisions/{$documentIdentifier}"),
            'Failed to fetch patent interference decision',
        )->json();
    }

    public function byInterferenceNumber(string $interferenceNumber): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/interferences/{$interferenceNumber}/decisions"),
            'Failed to fetch patent interference decisions by interference number',
        )->json();
    }
}
