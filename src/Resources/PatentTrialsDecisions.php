<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PatentTrialsDecisions extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/trials/decisions/search', $payload),
            'Failed to search patent trial decisions',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/trials/decisions/search', $query),
            'Failed to query patent trial decisions',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/patent/trials/decisions/search/download', $payload),
                'Failed to download patent trial decisions',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/patent/trials/decisions/search/download', $query),
                'Failed to download patent trial decisions query',
            ),
        );
    }

    public function find(string $documentIdentifier): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/trials/decisions/{$documentIdentifier}"),
            'Failed to fetch patent trial decision',
        )->json();
    }

    public function byTrialNumber(string $trialNumber): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/trials/{$trialNumber}/decisions"),
            'Failed to fetch patent trial decisions by trial number',
        )->json();
    }
}
