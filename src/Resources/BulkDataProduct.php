<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\BulkDataProductResponse;

class BulkDataProduct extends BaseResource
{
    public const PARAM_FILE_DATA_FROM_DATE = 'fileDataFromDate';

    public const PARAM_FILE_DATA_TO_DATE = 'fileDataToDate';

    public const PARAM_OFFSET = 'offset';

    public const PARAM_LIMIT = 'limit';

    public const PARAM_INCLUDE_FILES = 'includeFiles';

    public const PARAM_LATEST = 'latest';

    public static array $validParams = [
        self::PARAM_FILE_DATA_FROM_DATE,
        self::PARAM_FILE_DATA_TO_DATE,
        self::PARAM_OFFSET,
        self::PARAM_LIMIT,
        self::PARAM_INCLUDE_FILES,
        self::PARAM_LATEST,
    ];

    public static array $paramDescriptions = [
        self::PARAM_FILE_DATA_FROM_DATE => 'Filter files by start date (format: yyyy-MM-dd)',
        self::PARAM_FILE_DATA_TO_DATE => 'Filter files by end date (format: yyyy-MM-dd)',
        self::PARAM_OFFSET => 'Number of product file records to skip',
        self::PARAM_LIMIT => 'Number of product file records to collect',
        self::PARAM_INCLUDE_FILES => 'Include product files in the response (true/false)',
        self::PARAM_LATEST => 'Return only the latest product file (true/false)',
    ];

    public function find(string $productIdentifier, array $params = []): BulkDataProductResponse
    {
        return BulkDataProductResponse::fromJson($this->client->get("/api/v1/datasets/products/{$productIdentifier}", $this->validateParams($params))->json());
    }

    public function getDownloadLocation(string $productIdentifier, string $fileName): ?string
    {
        $response = $this->client
            ->withOptions(['allow_redirects' => false])
            ->get("/api/v1/datasets/products/files/{$productIdentifier}/{$fileName}");

        if (! in_array($response->status(), [200, 302], true)) {
            throw new \RuntimeException('Failed to fetch bulk data file download location', $response->status());
        }

        return $response->header('Location');
    }

    public function downloadFile(string $productIdentifier, string $fileName): string
    {
        $location = $this->getDownloadLocation($productIdentifier, $fileName);

        if (! $location) {
            return '';
        }

        return $this->ensureSuccessful($this->client->get($location), 'Failed to download bulk data product file')->body();
    }

    private function validateParams(array $params): array
    {
        return array_filter($params, static fn (string $key): bool => in_array($key, self::$validParams, true), ARRAY_FILTER_USE_KEY);
    }

    public static function getValidParams(): array
    {
        return self::$validParams;
    }

    public static function getParamDescriptions(): array
    {
        return self::$paramDescriptions;
    }
}
