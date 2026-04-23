<?php

namespace RadicalDreamers\UsptoClient\Resources;

class StatusCodes extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/status-codes', $payload),
            'Failed to search patent status codes',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/status-codes', $query),
            'Failed to query patent status codes',
        )->json();
    }
}
