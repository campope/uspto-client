<?php

namespace RadicalDreamers\UsptoClient\Resources;

class PatentTrialsProceedings extends BaseResource
{
    public function search(array $payload = []): array
    {
        return $this->ensureSuccessful(
            $this->client->post('/api/v1/patent/trials/proceedings/search', $payload),
            'Failed to search patent trial proceedings',
        )->json();
    }

    public function query(array $query = []): array
    {
        return $this->ensureSuccessful(
            $this->client->get('/api/v1/patent/trials/proceedings/search', $query),
            'Failed to query patent trial proceedings',
        )->json();
    }

    public function download(array $payload = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->post('/api/v1/patent/trials/proceedings/search/download', $payload),
                'Failed to download patent trial proceedings',
            ),
        );
    }

    public function downloadQuery(array $query = []): mixed
    {
        return $this->decodeJsonOrBody(
            $this->ensureSuccessful(
                $this->client->get('/api/v1/patent/trials/proceedings/search/download', $query),
                'Failed to download patent trial proceedings query',
            ),
        );
    }

    public function find(string $trialNumber): array
    {
        return $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/trials/proceedings/{$trialNumber}"),
            'Failed to fetch patent trial proceeding',
        )->json();
    }
}
