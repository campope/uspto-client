<?php

namespace RadicalDreamers\UsptoClient\Responses;

class SearchResponse
{
    public function __construct(
        public int $count,
        public array $patents,
        public string $requestIdentifier,
        public ?array $facets = null,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(
            $json['count'] ?? 0,
            self::extractPatents($json['patentFileWrapperDataBag'] ?? []),
            $json['requestIdentifier'] ?? '',
            $json['facets'] ?? null,
        );
    }

    private static function extractPatents(array $patentFileWrapperDataBag): array
    {
        return array_map(static fn (array $patent): array => [
            'application_number' => $patent['applicationNumberText'] ?? null,
            'grant_document_metadata' => self::extractDocumentMetadata($patent['grantDocumentMetaData'] ?? []),
            'pgpub_document_metadata' => self::extractDocumentMetadata($patent['pgpubDocumentMetaData'] ?? []),
            'events' => self::extractEvents($patent['eventDataBag'] ?? []),
            'metadata' => self::extractApplicationMetadata($patent['applicationMetaData'] ?? []),
            'continuity' => [
                'parent' => self::extractContinuityData($patent['parentContinuityBag'] ?? []),
                'child' => self::extractContinuityData($patent['childContinuityBag'] ?? []),
            ],
            'patent_term_adjustment' => self::extractPatentTermAdjustment($patent['patentTermAdjustmentData'] ?? []),
            'assignments' => self::extractAssignments($patent['assignmentBag'] ?? []),
            'attorneys' => self::extractAttorneys($patent['recordAttorney']['powerOfAttorneyBag'] ?? []),
            'correspondence_addresses' => self::extractAddresses($patent['correspondenceAddressBag'] ?? []),
            'last_ingestion_date' => $patent['lastIngestionDateTime'] ?? null,
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

    private static function extractEvents(array $events): array
    {
        return array_map(static fn (array $event): array => [
            'code' => $event['eventCode'] ?? null,
            'description' => $event['eventDescriptionText'] ?? null,
            'date' => $event['eventDate'] ?? null,
        ], $events);
    }

    private static function extractApplicationMetadata(array $metadata): array
    {
        return [
            'first_inventor_to_file' => $metadata['firstInventorToFileIndicator'] ?? null,
            'status' => [
                'code' => $metadata['applicationStatusCode'] ?? null,
                'description' => $metadata['applicationStatusDescriptionText'] ?? null,
                'date' => $metadata['applicationStatusDate'] ?? null,
            ],
            'type' => [
                'code' => $metadata['applicationTypeCode'] ?? null,
                'label' => $metadata['applicationTypeLabelName'] ?? null,
                'category' => $metadata['applicationTypeCategory'] ?? null,
            ],
            'entity_status' => $metadata['entityStatusData'] ?? [],
            'filing_date' => $metadata['filingDate'] ?? null,
            'uspc_symbol' => $metadata['uspcSymbolText'] ?? null,
            'national_stage' => $metadata['nationalStageIndicator'] ?? false,
            'first_inventor_name' => $metadata['firstInventorName'] ?? null,
            'cpc_classifications' => $metadata['cpcClassificationBag'] ?? [],
            'effective_filing_date' => $metadata['effectiveFilingDate'] ?? null,
            'publication_dates' => $metadata['publicationDateBag'] ?? [],
            'publication_numbers' => $metadata['publicationSequenceNumberBag'] ?? [],
            'earliest_publication' => [
                'date' => $metadata['earliestPublicationDate'] ?? null,
                'number' => $metadata['earliestPublicationNumber'] ?? null,
            ],
            'class' => $metadata['class'] ?? null,
            'subclass' => $metadata['subclass'] ?? null,
            'inventors' => self::extractInventors($metadata['inventorBag'] ?? []),
            'patent_number' => $metadata['patentNumber'] ?? null,
            'grant_date' => $metadata['grantDate'] ?? null,
            'applicants' => self::extractApplicants($metadata['applicantBag'] ?? []),
            'first_applicant_name' => $metadata['firstApplicantName'] ?? null,
            'customer_number' => $metadata['customerNumber'] ?? null,
            'group_art_unit' => $metadata['groupArtUnitNumber'] ?? null,
            'invention_title' => $metadata['inventionTitle'] ?? null,
            'confirmation_number' => $metadata['applicationConfirmationNumber'] ?? null,
            'examiner_name' => $metadata['examinerNameText'] ?? null,
            'publication_categories' => $metadata['publicationCategoryBag'] ?? [],
            'docket_number' => $metadata['docketNumber'] ?? null,
        ];
    }

    private static function extractInventors(array $inventors): array
    {
        return array_map(static fn (array $inventor): array => [
            'first_name' => $inventor['firstName'] ?? null,
            'last_name' => $inventor['lastName'] ?? null,
            'country_code' => $inventor['countryCode'] ?? null,
            'full_name' => $inventor['inventorNameText'] ?? null,
            'addresses' => self::extractAddresses($inventor['correspondenceAddressBag'] ?? []),
        ], $inventors);
    }

    private static function extractApplicants(array $applicants): array
    {
        return array_map(static fn (array $applicant): array => [
            'name' => $applicant['applicantNameText'] ?? null,
            'addresses' => self::extractAddresses($applicant['correspondenceAddressBag'] ?? []),
        ], $applicants);
    }

    private static function extractContinuityData(array $continuity): array
    {
        return array_map(static fn (array $item): array => [
            'application_number' => $item['parentApplicationNumberText'] ?? $item['childApplicationNumberText'] ?? null,
            'filing_date' => $item['parentApplicationFilingDate'] ?? $item['childApplicationFilingDate'] ?? null,
            'type' => [
                'code' => $item['claimParentageTypeCode'] ?? null,
                'description' => $item['claimParentageTypeCodeDescriptionText'] ?? null,
            ],
            'status' => [
                'code' => $item['parentApplicationStatusCode'] ?? $item['childApplicationStatusCode'] ?? null,
                'description' => $item['parentApplicationStatusDescriptionText'] ?? $item['childApplicationStatusDescriptionText'] ?? null,
            ],
            'patent_number' => $item['parentPatentNumber'] ?? null,
            'first_inventor_to_file' => $item['firstInventorToFileIndicator'] ?? null,
        ], $continuity);
    }

    private static function extractPatentTermAdjustment(array $adjustment): array
    {
        return [
            'applicant_delay' => $adjustment['applicantDayDelayQuantity'] ?? 0,
            'overlapping_days' => $adjustment['overlappingDayQuantity'] ?? 0,
            'filing_date' => $adjustment['filingDate'] ?? null,
            'office_adjustment_delay' => $adjustment['ipOfficeAdjustmentDelayQuantity'] ?? 0,
            'c_delay' => $adjustment['cDelayQuantity'] ?? 0,
            'total_adjustment' => $adjustment['adjustmentTotalQuantity'] ?? 0,
            'b_delay' => $adjustment['bDelayQuantity'] ?? 0,
            'grant_date' => $adjustment['grantDate'] ?? null,
            'a_delay' => $adjustment['aDelayQuantity'] ?? 0,
            'office_day_delay' => $adjustment['ipOfficeDayDelayQuantity'] ?? 0,
            'history' => self::extractPatentTermHistory($adjustment['patentTermAdjustmentHistoryDataBag'] ?? []),
        ];
    }

    private static function extractPatentTermHistory(array $history): array
    {
        return array_map(static fn (array $item): array => [
            'description' => $item['eventDescriptionText'] ?? null,
            'sequence_number' => $item['eventSequenceNumber'] ?? null,
            'originating_sequence_number' => $item['originatingEventSequenceNumber'] ?? null,
            'pta_pte_code' => $item['ptaPTECode'] ?? null,
            'date' => $item['eventDate'] ?? null,
        ], $history);
    }

    private static function extractAssignments(array $assignments): array
    {
        return array_map(static fn (array $assignment): array => [
            'received_date' => $assignment['assignmentReceivedDate'] ?? null,
            'recorded_date' => $assignment['assignmentRecordedDate'] ?? null,
            'mailed_date' => $assignment['assignmentMailedDate'] ?? null,
            'reel_frame' => $assignment['reelAndFrameNumber'] ?? null,
            'document_url' => $assignment['assignmentDocumentLocationURI'] ?? null,
            'conveyance_text' => $assignment['conveyanceText'] ?? null,
            'assignors' => array_map(static fn (array $assignor): array => [
                'name' => $assignor['assignorName'] ?? null,
                'execution_date' => $assignor['executionDate'] ?? null,
            ], $assignment['assignorBag'] ?? []),
            'assignees' => array_map(static fn (array $assignee): array => [
                'name' => $assignee['assigneeNameText'] ?? null,
                'address' => self::extractAddress($assignee['assigneeAddress'] ?? []),
            ], $assignment['assigneeBag'] ?? []),
            'correspondence' => self::extractCorrespondence($assignment['correspondenceAddressBag'] ?? []),
        ], $assignments);
    }

    private static function extractAttorneys(array $attorneys): array
    {
        return array_map(static fn (array $attorney): array => [
            'active' => ($attorney['activeIndicator'] ?? null) === 'ACTIVE',
            'first_name' => $attorney['firstName'] ?? null,
            'last_name' => $attorney['lastName'] ?? null,
            'registration_number' => $attorney['registrationNumber'] ?? null,
            'category' => $attorney['registeredPractitionerCategory'] ?? null,
            'addresses' => self::extractAddresses($attorney['attorneyAddressBag'] ?? []),
            'contact' => array_map(static fn (array $contact): array => [
                'type' => $contact['telecomTypeCode'] ?? null,
                'number' => $contact['telecommunicationNumber'] ?? null,
            ], $attorney['telecommunicationAddressBag'] ?? []),
        ], $attorneys);
    }

    private static function extractAddresses(array $addresses): array
    {
        return array_map(static fn (array $address): array => self::extractAddress($address), $addresses);
    }

    private static function extractAddress(array $address): array
    {
        return [
            'name' => $address['nameLineOneText'] ?? null,
            'line1' => $address['addressLineOneText'] ?? null,
            'line2' => $address['addressLineTwoText'] ?? null,
            'city' => $address['cityName'] ?? null,
            'state' => $address['geographicRegionName'] ?? null,
            'state_code' => $address['geographicRegionCode'] ?? null,
            'country' => $address['countryName'] ?? null,
            'country_code' => $address['countryCode'] ?? null,
            'postal_code' => $address['postalCode'] ?? null,
            'type' => $address['postalAddressCategory'] ?? null,
        ];
    }

    private static function extractCorrespondence(array $correspondence): array
    {
        return array_map(static fn (array $item): array => [
            'name' => $item['correspondentNameText'] ?? null,
            'line1' => $item['addressLineOneText'] ?? null,
            'line2' => $item['addressLineTwoText'] ?? null,
        ], $correspondence);
    }
}
