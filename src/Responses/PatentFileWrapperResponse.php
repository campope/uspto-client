<?php

namespace RadicalDreamers\UsptoClient\Responses;

class PatentFileWrapperResponse
{
    public function __construct(
        public array $patentFileWrapperData,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(self::extractPatentFileWrapperData($json['patentFileWrapperDataBag'] ?? []));
    }

    private static function extractPatentFileWrapperData(array $patentFileWrapperDataBag): array
    {
        return array_map(static fn (array $data): array => [
            'application_number' => $data['applicationNumberText'] ?? null,
            'grant_document_metadata' => self::extractDocumentMetadata($data['grantDocumentMetaData'] ?? []),
            'pgpub_document_metadata' => self::extractDocumentMetadata($data['pgpubDocumentMetaData'] ?? []),
        ], $patentFileWrapperDataBag);
    }

    private static function extractDocumentMetadata(array $metadata): array
    {
        return [
            'product_identifier' => $metadata['productIdentifier'] ?? null,
            'zip_file_name' => $metadata['zipFileName'] ?? null,
            'file_create_date_time' => $metadata['fileCreateDateTime'] ?? null,
            'xml_file_name' => $metadata['xmlFileName'] ?? null,
            'file_location_uri' => $metadata['fileLocationURI'] ?? null,
        ];
    }

    public function getApplicationNumbers(): array
    {
        return array_map(static fn (array $data): mixed => $data['application_number'], $this->patentFileWrapperData);
    }

    public function getGrantDocumentMetadata(string $applicationNumber): ?array
    {
        foreach ($this->patentFileWrapperData as $data) {
            if ($data['application_number'] === $applicationNumber) {
                return $data['grant_document_metadata'];
            }
        }

        return null;
    }

    public function getPgPubDocumentMetadata(string $applicationNumber): ?array
    {
        foreach ($this->patentFileWrapperData as $data) {
            if ($data['application_number'] === $applicationNumber) {
                return $data['pgpub_document_metadata'];
            }
        }

        return null;
    }
}
