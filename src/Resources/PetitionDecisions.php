<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PetitionDecisions extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/petition/decisions/search', $payload),
            'Failed to search petition decisions',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/petition/decisions/search', $query),
            'Failed to query petition decisions',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/petition/decisions/search/download', $payload),
                'Failed to download petition decision search results',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/petition/decisions/search/download', $query),
                'Failed to download petition decision query results',
            ),
        );
    }

    public function find(string $petitionDecisionRecordIdentifier, bool $includeDocuments = false): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/petition/decisions/{$petitionDecisionRecordIdentifier}", [
                'includeDocuments' => $includeDocuments,
            ]),
            'Failed to fetch petition decision record',
        )->json();
    }
}
