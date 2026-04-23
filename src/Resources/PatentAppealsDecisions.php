<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PatentAppealsDecisions extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/appeals/decisions/search', $payload),
            'Failed to search patent appeal decisions',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/appeals/decisions/search', $query),
            'Failed to query patent appeal decisions',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/patent/appeals/decisions/search/download', $payload),
                'Failed to download patent appeal decisions',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/patent/appeals/decisions/search/download', $query),
                'Failed to download patent appeal decisions query',
            ),
        );
    }

    public function find(string $documentIdentifier): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/appeals/decisions/{$documentIdentifier}"),
            'Failed to fetch patent appeal decision',
        )->json();
    }

    public function byAppealNumber(string $appealNumber): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/appeals/{$appealNumber}/decisions"),
            'Failed to fetch patent appeal decisions by appeal number',
        )->json();
    }
}
