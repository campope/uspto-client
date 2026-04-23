<?php

namespace RadicalDreamers\UsptoClient\Resources;

use Illuminate\Http\Client\Response;
use Psr\Http\Message\StreamInterface;
use RadicalDreamers\UsptoClient\USPTOClient;
use RuntimeException;

abstract class BaseResource
{
    public function __construct(
        protected USPTOClient $client,
        protected array $options = []
    ) {}

    public function getClient(): USPTOClient
    {
        return $this->client;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function ensureSuccessful(Response $response, string $message): Response
    {
        if (! $response->successful()) {
            throw new RuntimeException($message, $response->status());
        }

        return $response;
    }

    protected function decodeJsonOrBody(Response $response): mixed
    {
        $contentType = strtolower($response->header('Content-Type') ?? '');

        if (str_contains($contentType, 'json')) {
            return $response->json();
        }

        return $response->body();
    }

    protected function toStream(Response $response): StreamInterface
    {
        return $response->toPsrResponse()->getBody();
    }
}
