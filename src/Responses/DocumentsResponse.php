<?php

namespace RadicalDreamers\UsptoClient\Responses;

class DocumentsResponse
{
    public function __construct(
        public array $documents,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(self::extractDocuments($json['documentBag'] ?? []));
    }

    private static function extractDocuments(array $documentBag): array
    {
        return array_map(static fn (array $document): array => [
            'application_number' => $document['applicationNumberText'] ?? null,
            'official_date' => $document['officialDate'] ?? null,
            'document_identifier' => $document['documentIdentifier'] ?? null,
            'document_code' => $document['documentCode'] ?? null,
            'document_description' => $document['documentCodeDescriptionText'] ?? null,
            'document_direction_category' => $document['directionCategory'] ?? null,
            'download_options' => self::extractDownloadOptions($document['downloadOptionBag'] ?? []),
        ], $documentBag);
    }

    private static function extractDownloadOptions(array $downloadOptionBag): array
    {
        return array_map(static fn (array $option): array => [
            'mime_type' => $option['mimeTypeIdentifier'] ?? null,
            'download_url' => $option['downloadUrl'] ?? null,
            'page_count' => $option['pageTotalQuantity'] ?? null,
        ], $downloadOptionBag);
    }

    public function filterByDocumentCodes(array|string $codes): array
    {
        $codes = is_array($codes) ? $codes : [$codes];

        return array_values(array_filter(
            $this->documents,
            static fn (array $document): bool => in_array($document['document_code'], $codes, true),
        ));
    }
}
