<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PatentTrialsDocuments extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/trials/documents/search', $payload),
            'Failed to search patent trial documents',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/trials/documents/search', $query),
            'Failed to query patent trial documents',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/patent/trials/documents/search/download', $payload),
                'Failed to download patent trial documents',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/patent/trials/documents/search/download', $query),
                'Failed to download patent trial documents query',
            ),
        );
    }

    public function find(string $documentIdentifier): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/trials/documents/{$documentIdentifier}"),
            'Failed to fetch patent trial document',
        )->json();
    }

    public function byTrialNumber(string $trialNumber): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/trials/{$trialNumber}/documents"),
            'Failed to fetch patent trial documents by trial number',
        )->json();
    }
}
