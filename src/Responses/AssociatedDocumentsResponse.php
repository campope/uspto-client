<?php

namespace RadicalDreamers\UsptoClient\Responses;

class AssociatedDocumentsResponse
{
    public function __construct(
        public array $associatedDocuments,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(self::extractAssociatedDocuments($json['patentFileWrapperDataBag'] ?? []));
    }

    private static function extractAssociatedDocuments(array $associatedDocuments): array
    {
        return array_map(static fn (array $document): array => [
            'application_number' => $document['applicationNumberText'] ?? null,
            'grant_document_meta_data' => isset($document['grantDocumentMetaData']) ? [
                'product_identifier' => $document['grantDocumentMetaData']['productIdentifier'] ?? null,
                'zip_file_name' => $document['grantDocumentMetaData']['zipFileName'] ?? null,
                'file_create_date_time' => $document['grantDocumentMetaData']['fileCreateDateTime'] ?? null,
                'xml_file_name' => $document['grantDocumentMetaData']['xmlFileName'] ?? null,
                'file_location_uri' => $document['grantDocumentMetaData']['fileLocationURI'] ?? null,
            ] : null,
            'pgpub_document_meta_data' => isset($document['pgpubDocumentMetaData']) ? [
                'product_identifier' => $document['pgpubDocumentMetaData']['productIdentifier'] ?? null,
                'zip_file_name' => $document['pgpubDocumentMetaData']['zipFileName'] ?? null,
                'file_create_date_time' => $document['pgpubDocumentMetaData']['fileCreateDateTime'] ?? null,
                'xml_file_name' => $document['pgpubDocumentMetaData']['xmlFileName'] ?? null,
                'file_location_uri' => $document['pgpubDocumentMetaData']['fileLocationURI'] ?? null,
            ] : null,
        ], $associatedDocuments);
    }

    public function first(): ?array
    {
        return $this->associatedDocuments[0] ?? null;
    }

    public function getApplicationNumber(): ?string
    {
        return $this->first()['application_number'] ?? null;
    }

    public function getGrantDocumentMetadata(): ?array
    {
        return $this->first()['grant_document_meta_data'] ?? null;
    }

    public function getPgpubDocumentMetadata(): ?array
    {
        return $this->first()['pgpub_document_meta_data'] ?? null;
    }

    public function hasDocuments(): bool
    {
        return $this->associatedDocuments !== [];
    }
}
