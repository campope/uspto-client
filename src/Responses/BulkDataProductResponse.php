<?php

namespace RadicalDreamers\UsptoClient\Responses;

class BulkDataProductResponse
{
    public function __construct(
        public int $count,
        public array $bulk_data_products,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(
            $json['count'],
            self::extractBulkDataProducts($json['bulkDataProductBag'][0] ?? []),
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
            'labels' => $product['productLabelArrayText'][0] ?? [],
            'datasets' => $product['productDataSetArrayText'][0] ?? [],
            'categories' => $product['productDataSetCategoryArrayText'][0] ?? [],
            'from_date' => $product['productFromDate'] ?? null,
            'to_date' => $product['productToDate'] ?? null,
            'total_file_size' => $product['productTotalFileSize'] ?? null,
            'file_total_quantity' => $product['productFileTotalQuantity'] ?? null,
            'modified_date_time' => $product['modifiedDateTime'] ?? null,
            'mime_types' => $product['mimeTypeIdentifierArrayText'][0] ?? [],
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
}
