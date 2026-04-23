<?php

namespace RadicalDreamers\UsptoClient\Resources;

use Psr\Http\Message\StreamInterface;
use RadicalDreamers\UsptoClient\Responses\AssociatedDocumentsResponse;

class AssociatedDocuments extends BaseResource
{
    public function get(string $applicationNumber): AssociatedDocumentsResponse
    {
        $response = $this->ensureSuccessful(
            $this->client->get("/api/v1/patent/applications/{$applicationNumber}/associated-documents"),
            'Failed to fetch associated documents data',
        );

        return AssociatedDocumentsResponse::fromJson($response->json());
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
                'Failed to stream associated document',
            ),
        );
    }
}
