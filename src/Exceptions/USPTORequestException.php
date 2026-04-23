<?php

namespace RadicalDreamers\UsptoClient\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class USPTORequestException extends RuntimeException
{
    public function __construct(
        string $message,
        protected int $statusCode,
        protected ?string $reasonPhrase = null,
        protected ?string $url = null,
        protected ?string $responseBody = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            self::formatMessage($message, $statusCode, $reasonPhrase, $url, $responseBody),
            $statusCode,
            $previous,
        );
    }

    public static function fromResponse(Response $response, string $message, ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            $response->status(),
            $response->reason(),
            $response->effectiveUri()?->__toString(),
            self::summarizeBody($response),
            $previous,
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): ?string
    {
        return $this->reasonPhrase;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    private static function formatMessage(
        string $message,
        int $statusCode,
        ?string $reasonPhrase,
        ?string $url,
        ?string $responseBody,
    ): string {
        $details = ["HTTP {$statusCode}".($reasonPhrase ? " {$reasonPhrase}" : '')];

        if ($url !== null && $url !== '') {
            $details[] = "URL: {$url}";
        }

        if ($responseBody !== null && $responseBody !== '') {
            $details[] = "Response: {$responseBody}";
        }

        return $message.' ('.implode('; ', $details).')';
    }

    private static function summarizeBody(Response $response): ?string
    {
        $body = trim($response->body());

        if ($body === '') {
            return null;
        }

        $body = preg_replace('/\s+/', ' ', $body) ?? $body;

        if (strlen($body) > 500) {
            $body = substr($body, 0, 497).'...';
        }

        return $body;
    }
}
