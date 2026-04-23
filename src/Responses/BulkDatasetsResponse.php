<?php

namespace RadicalDreamers\UsptoClient\Responses;

class BulkDatasetsResponse
{
    public function __construct(
        public int $count,
        public array $bulk_data_products,
        public ?array $facets,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(
            $json['count'],
            self::extractBulkDataProducts($json['bulkDataProductBag'] ?? []),
            self::extractFacets($json['facets'] ?? null),
        );
    }

    private static function extractBulkDataProducts(array $products): array
    {
        return array_map(static fn (array $product): array => [
            'identifier' => $product['productIdentifier'] ?? null,
            'description' => $product['productDescriptionText'] ?? null,
            'title' => $product['productTitleText'] ?? null,
            'frequency' => $product['productFrequencyText'] ?? null,
            'days_of_week' => $product['daysOfWeekText'] ?? null,
            'labels' => $product['productLabelArrayText'] ?? [],
            'datasets' => $product['productDataSetArrayText'] ?? [],
            'categories' => $product['productDataSetCategoryArrayText'] ?? [],
            'from_date' => $product['productFromDate'] ?? null,
            'to_date' => $product['productToDate'] ?? null,
            'total_file_size' => $product['productTotalFileSize'] ?? null,
            'file_total_quantity' => $product['productFileTotalQuantity'] ?? null,
            'modified_date_time' => $product['modifiedDateTime'] ?? null,
            'mime_types' => $product['mimeTypeIdentifierArrayText'] ?? [],
            'files' => self::extractFiles($product['productFileBag']['fileDataBag'] ?? []),
        ], $products);
    }

    private static function extractFiles(array $files): array
    {
        return array_map(static fn (array $file): array => [
            'name' => $file['fileName'] ?? null,
            'size' => $file['fileSize'] ?? null,
            'from_date' => $file['fileDataFromDate'] ?? null,
            'to_date' => $file['fileDataToDate'] ?? null,
            'type' => $file['fileTypeText'] ?? null,
            'download_uri' => $file['fileDownloadURI'] ?? null,
            'release_date' => $file['fileReleaseDate'] ?? null,
        ], $files);
    }

    private static function extractFacets(?array $facets): ?array
    {
        if (! $facets) {
            return null;
        }

        return [
            'labels' => self::extractFacetBag($facets['productLabelBag'] ?? []),
            'datasets' => self::extractFacetBag($facets['productDataSetBag'] ?? []),
            'categories' => self::extractFacetBag($facets['productCategoryBag'] ?? []),
            'mime_types' => self::extractFacetBag($facets['productMimeTypeBag'] ?? []),
        ];
    }

    private static function extractFacetBag(array $bag): array
    {
        return array_map(static fn (array $item): array => [
            'value' => $item['value'] ?? null,
            'count' => $item['count'] ?? null,
        ], $bag);
    }
}
