<?php

namespace RadicalDreamers\UsptoClient;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class USPTOClient
{
    public function __construct(
        protected ?PendingRequest $httpClient = null
    ) {
        $this->httpClient ??= Http::withHeaders(array_filter(config('uspto-client.headers', []), static fn (mixed $value): bool => filled($value)))
            ->withOptions([
                'verify' => (bool) config('uspto-client.verify', true),
                'timeout' => (int) config('uspto-client.timeout', 120),
                'connect_timeout' => (int) config('uspto-client.connect_timeout', 10),
            ])
            ->acceptJson()
            ->baseUrl((string) config('uspto-client.base_url', 'https://api.uspto.gov'));
    }

    public function getHttpClient(): PendingRequest
    {
        return $this->httpClient;
    }

    public function withBaseUrl(string $baseUrl): self
    {
        return new self($this->httpClient->baseUrl($baseUrl));
    }

    public function withHeaders(array $headers): self
    {
        return new self($this->httpClient->withHeaders($headers));
    }

    public function withOptions(array $options): self
    {
        return new self($this->httpClient->withOptions($options));
    }

    public function asForm(): self
    {
        return new self($this->httpClient->asForm());
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->httpClient->{$method}(...$arguments);
    }
}
