<?php

namespace RadicalDreamers\UsptoClient\Responses;

class AttorneyResponse
{
    public function __construct(
        public int $count,
        public string $application_number,
        public array $record_attorney,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];

        return new self(
            $json['count'],
            $wrapper['applicationNumberText'],
            self::extractRecordAttorney($wrapper['recordAttorney'] ?? []),
        );
    }

    private static function extractRecordAttorney(array $data): array
    {
        return [
            'customer_numbers' => self::extractCustomerNumbers($data['customerNumber'] ?? []),
            'power_of_attorneys' => self::extractPowerOfAttorneys($data['powerOfAttorneyBag'] ?? []),
            'attorneys' => self::extractAttorneys($data['attorneyBag'] ?? []),
        ];
    }

    private static function extractCustomerNumbers(array $customerNumbers): array
    {
        return array_map(static fn (array $customer): array => [
            'patron_identifier' => $customer['patronIdentifier'] ?? null,
            'organization_name' => $customer['organizationStandardName'] ?? null,
            'addresses' => self::extractAddresses($customer['powerOfAttorneyAddressBag'] ?? []),
            'telecom_addresses' => self::extractTelecomAddresses($customer['telecommunicationAddressBag'] ?? []),
        ], $customerNumbers);
    }

    private static function extractPowerOfAttorneys(array $powerOfAttorneys): array
    {
        return array_map(static fn (array $poa): array => [
            'first_name' => $poa['firstName'] ?? null,
            'middle_name' => $poa['middleName'] ?? null,
            'last_name' => $poa['lastName'] ?? null,
            'name_prefix' => $poa['namePrefix'] ?? null,
            'name_suffix' => $poa['nameSuffix'] ?? null,
            'preferred_name' => $poa['preferredName'] ?? null,
            'country_code' => $poa['countryCode'] ?? null,
            'registration_number' => $poa['registrationNumber'] ?? null,
            'active_indicator' => $poa['activeIndicator'] ?? null,
            'registered_practitioner_category' => $poa['registeredPractitionerCategory'] ?? null,
            'addresses' => self::extractAddresses($poa['attorneyAddressBag'] ?? []),
            'telecom_addresses' => self::extractTelecomAddresses($poa['telecommunicationAddressBag'] ?? []),
        ], $powerOfAttorneys);
    }

    private static function extractAttorneys(array $attorneys): array
    {
        return array_map(static fn (array $attorney): array => [
            'first_name' => $attorney['firstName'] ?? null,
            'middle_name' => $attorney['middleName'] ?? null,
            'last_name' => $attorney['lastName'] ?? null,
            'name_prefix' => $attorney['namePrefix'] ?? null,
            'name_suffix' => $attorney['nameSuffix'] ?? null,
            'registration_number' => $attorney['registrationNumber'] ?? null,
            'active_indicator' => $attorney['activeIndicator'] ?? null,
            'registered_practitioner_category' => $attorney['registeredPractitionerCategory'] ?? null,
            'addresses' => self::extractAddresses($attorney['attorneyAddressBag'] ?? []),
            'telecom_addresses' => self::extractTelecomAddresses($attorney['telecommunicationAddressBag'] ?? []),
        ], $attorneys);
    }

    private static function extractAddresses(array $addresses): array
    {
        return array_map(static fn (array $address): array => [
            'name_line_one' => $address['nameLineOneText'] ?? null,
            'name_line_two' => $address['nameLineTwoText'] ?? null,
            'address_line_one' => $address['addressLineOneText'] ?? null,
            'address_line_two' => $address['addressLineTwoText'] ?? null,
            'geographic_region_name' => $address['geographicRegionName'] ?? null,
            'geographic_region_code' => $address['geographicRegionCode'] ?? null,
            'postal_code' => $address['postalCode'] ?? null,
            'city_name' => $address['cityName'] ?? null,
            'country_code' => $address['countryCode'] ?? null,
            'country_name' => $address['countryName'] ?? null,
        ], $addresses);
    }

    private static function extractTelecomAddresses(array $telecomAddresses): array
    {
        return array_map(static fn (array $telecom): array => [
            'number' => $telecom['telecommunicationNumber'] ?? null,
            'extension' => $telecom['extensionNumber'] ?? null,
            'type_code' => $telecom['telecomTypeCode'] ?? null,
        ], $telecomAddresses);
    }
}
