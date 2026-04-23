<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\USPTOClient;
use RuntimeException;

class OACitations extends BaseResource
{
    public function __construct(USPTOClient $client, array $options = [])
    {
        parent::__construct($client, $options);

        $this->client = $this->client->withBaseUrl('https://developer.uspto.gov/ds-api/oa_citations/v2');
    }

    public function find(string $criteria = '*:*', int $start = 0, int $rows = 100): array
    {
        try {
            return $this->client->asForm()->post('/records', [
                'criteria' => $criteria,
                'start' => $start,
                'rows' => $rows,
            ])->json();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Failed to fetch citation data from USPTO: '.$exception->getMessage(), previous: $exception);
        }
    }

    public function findByApplicationNumber(string $applicationNumber, int $start = 0, int $rows = 100): array
    {
        return $this->find("patentApplicationNumber:{$applicationNumber}", $start, $rows);
    }
}
