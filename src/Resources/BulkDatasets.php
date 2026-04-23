<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\BulkDatasetsResponse;

class BulkDatasets extends BaseResource
{
    public const PARAM_Q = 'q';

    public const PARAM_PRODUCT_TITLE = 'productTitle';

    public const PARAM_PRODUCT_DESCRIPTION = 'productDescription';

    public const PARAM_PRODUCT_SHORT_NAME = 'productShortName';

    public const PARAM_OFFSET = 'offset';

    public const PARAM_LIMIT = 'limit';

    public const PARAM_FACETS = 'facets';

    public const PARAM_INCLUDE_FILES = 'includeFiles';

    public const PARAM_LATEST = 'latest';

    public const PARAM_LABELS = 'labels';

    public const PARAM_CATEGORIES = 'categories';

    public const PARAM_DATASETS = 'datasets';

    public const PARAM_FILE_TYPES = 'fileTypes';

    public static array $validParams = [
        self::PARAM_Q,
        self::PARAM_PRODUCT_TITLE,
        self::PARAM_PRODUCT_DESCRIPTION,
        self::PARAM_PRODUCT_SHORT_NAME,
        self::PARAM_OFFSET,
        self::PARAM_LIMIT,
        self::PARAM_FACETS,
        self::PARAM_INCLUDE_FILES,
        self::PARAM_LATEST,
        self::PARAM_LABELS,
        self::PARAM_CATEGORIES,
        self::PARAM_DATASETS,
        self::PARAM_FILE_TYPES,
    ];

    public static array $paramDescriptions = [
        self::PARAM_Q => 'A search query to return products along with their associated files by product title or description.',
        self::PARAM_PRODUCT_TITLE => 'Specific product title',
        self::PARAM_PRODUCT_DESCRIPTION => 'Specific product description',
        self::PARAM_PRODUCT_SHORT_NAME => 'Product identifier',
        self::PARAM_OFFSET => 'Number of product records to skip (default: 0)',
        self::PARAM_LIMIT => 'Number of product records to collect (default: 10)',
        self::PARAM_FACETS => 'Set to true if facets need to be enabled in the response',
        self::PARAM_INCLUDE_FILES => 'Set to true if product files should be included',
        self::PARAM_LATEST => 'Set to true if only the latest product file should be returned',
        self::PARAM_LABELS => 'List of tags separated by comma to filter with',
        self::PARAM_CATEGORIES => 'List of categories separated by comma to filter with',
        self::PARAM_DATASETS => 'List of datasets separated by comma to filter with',
        self::PARAM_FILE_TYPES => 'List of file types separated by comma to filter with',
    ];

    public function search(array $params = []): BulkDatasetsResponse
    {
        return BulkDatasetsResponse::fromJson($this->client->get('/api/v1/datasets/products/search', $this->validateParams($params))->json());
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
