<?php

namespace RadicalDreamers\UsptoClient\Responses;

use JsonSerializable;

class ApplicationDataResponse implements JsonSerializable
{
    public function __construct(
        public string $application_number,
        public ?string $docket_number,
        public string $filing_date,
        public string $invention_title,
        public string $first_inventor_name,
        public string $application_status,
        public string $application_status_date,
        public ?string $patent_number,
        public array $cpc_classifications,
        public array $inventors,
        public int $customer_number,
        public int $confirmation_number,
        public string $examiner_name,
        public string $art_unit_number,
        public string $first_inventor_to_file_indicator,
        public int $application_status_code,
        public string $application_type_code,
        public string $application_type_label_name,
        public string $application_type_category,
        public string $effective_filing_date,
        public array $publication_dates,
        public array $publication_sequence_numbers,
        public ?string $earliest_publication_date,
        public ?string $earliest_publication_number,
        public ?string $pct_publication_number,
        public ?string $pct_publication_date,
        public ?string $international_registration_publication_date,
        public ?string $international_registration_number,
        public string $class,
        public string $subclass,
        public string $uspc_symbol_text,
        public bool $national_stage_indicator,
        public array $entity_status_data,
        public array $applicants,
        public array $publication_categories,
        public ?string $first_applicant_name,
        public ?string $grant_date,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];
        $data = $wrapper['applicationMetaData'];

        return new self(
            $wrapper['applicationNumberText'],
            $data['docketNumber'] ?? null,
            $data['filingDate'],
            $data['inventionTitle'],
            $data['firstInventorName'],
            $data['applicationStatusDescriptionText'],
            $data['applicationStatusDate'],
            $data['patentNumber'] ?? null,
            $data['cpcClassificationBag'] ?? [],
            self::extractInventors($data['inventorBag'] ?? []),
            $data['customerNumber'],
            $data['applicationConfirmationNumber'],
            $data['examinerNameText'],
            $data['groupArtUnitNumber'],
            $data['firstInventorToFileIndicator'],
            $data['applicationStatusCode'],
            $data['applicationTypeCode'],
            $data['applicationTypeLabelName'],
            $data['applicationTypeCategory'],
            $data['effectiveFilingDate'],
            $data['publicationDateBag'] ?? [],
            $data['publicationSequenceNumberBag'] ?? [],
            $data['earliestPublicationDate'] ?? null,
            $data['earliestPublicationNumber'] ?? null,
            $data['pctPublicationNumber'] ?? null,
            $data['pctPublicationDate'] ?? null,
            $data['internationalRegistrationPublicationDate'] ?? null,
            $data['internationalRegistrationNumber'] ?? null,
            $data['class'],
            $data['subclass'],
            $data['uspcSymbolText'],
            $data['nationalStageIndicator'],
            self::extractEntityStatusData($data['entityStatusData'] ?? []),
            self::extractApplicants($data['applicantBag'] ?? []),
            $data['publicationCategoryBag'] ?? [],
            $data['firstApplicantName'] ?? null,
            $data['grantDate'] ?? null,
        );
    }

    private static function extractInventors(array $inventorBag): array
    {
        return array_map(static fn (array $inventor): array => [
            'first_name' => $inventor['firstName'] ?? null,
            'last_name' => $inventor['lastName'] ?? null,
            'name' => $inventor['inventorNameText'] ?? null,
            'address' => self::extractAddress($inventor['correspondenceAddressBag'][0] ?? []),
        ], $inventorBag);
    }

    private static function extractAddress(array $address): array
    {
        return [
            'line1' => $address['addressLineOneText'] ?? '',
            'line2' => $address['addressLineTwoText'] ?? '',
            'city' => $address['cityName'] ?? '',
            'region' => $address['geographicRegionName'] ?? '',
            'postal_code' => $address['postalCode'] ?? '',
            'country' => $address['countryName'] ?? '',
            'country_code' => $address['countryCode'] ?? '',
        ];
    }

    private static function extractEntityStatusData(array $entityStatusData): array
    {
        return [
            'small_entity_status_indicator' => $entityStatusData['smallEntityStatusIndicator'] ?? false,
            'business_entity_status_category' => $entityStatusData['businessEntityStatusCategory'] ?? null,
        ];
    }

    private static function extractApplicants(array $applicantBag): array
    {
        return array_map(static fn (array $applicant): array => [
            'name' => $applicant['applicantNameText'] ?? null,
            'address' => self::extractAddress($applicant['correspondenceAddressBag'][0] ?? []),
        ], $applicantBag);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
