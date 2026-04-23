<?php

namespace RadicalDreamers\UsptoClient\Resources;

use Psr\Http\Message\StreamInterface;
use RadicalDreamers\UsptoClient\Responses\DocumentsResponse;

class Documents extends BaseResource
{
    public function get(string $applicationNumber, array $filters = []): DocumentsResponse
    {
        $response = $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/applications/{$applicationNumber}/documents", array_filter([
                'documentCodes' => $filters['documentCodes'] ?? null,
                'officialDateFrom' => $filters['officialDateFrom'] ?? null,
                'officialDateTo' => $filters['officialDateTo'] ?? null,
            ], static fn (mixed $value): bool => $value !== null && $value !== '')),
            'Failed to fetch documents data',
        );

        return DocumentsResponse::fromJson($response->json());
    }

    public function downloadDocument(string $url): string
    {
        $response = $this->ensureSuccessful($this->client->get($url), 'Failed to download document');

        return $response->body();
    }

    public function streamDocument(string $url): StreamInterface
    {
        return $this->toStream(
            $this->ensureSuccessful(
                $this->client->withHeaders(['Accept' => 'application/octet-stream'])->get($url),
                'Failed to stream document',
            ),
        );
    }

    public function getActionTextFromLegacyApi(string $applicationNumber): array
    {
        return $this->client->withBaseUrl('https://developer.uspto.gov/ds-api')
            ->asForm()
            ->post('/oa_actions/v1/records', [
                'criteria' => "patentApplicationNumber:{$applicationNumber}",
                'start' => 0,
                'rows' => 100,
            ])
            ->json();
    }
}
