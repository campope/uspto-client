<?php

namespace RadicalDreamers\UsptoClient\Responses;

class ContinuityResponse
{
    public function __construct(
        public int $count,
        public string $application_number,
        public array $parent_continuity,
        public array $child_continuity,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];

        return new self(
            $json['count'],
            $wrapper['applicationNumberText'],
            self::extractContinuityData($wrapper['parentContinuityBag'] ?? []),
            self::extractContinuityData($wrapper['childContinuityBag'] ?? []),
        );
    }

    private static function extractContinuityData(array $continuityBag): array
    {
        return array_map(static fn (array $continuity): array => [
            'first_inventor_to_file' => $continuity['firstInventorToFileIndicator'] ?? null,
            'application_status_code' => $continuity['parentApplicationStatusCode'] ?? $continuity['childApplicationStatusCode'] ?? null,
            'patent_number' => $continuity['parentPatentNumber'] ?? $continuity['childPatentNumber'] ?? null,
            'application_status' => $continuity['parentApplicationStatusDescriptionText'] ?? $continuity['childApplicationStatusDescriptionText'] ?? null,
            'filing_date' => $continuity['parentApplicationFilingDate'] ?? $continuity['childApplicationFilingDate'] ?? null,
            'parent_application_number' => $continuity['parentApplicationNumberText'] ?? null,
            'child_application_number' => $continuity['childApplicationNumberText'] ?? null,
            'claim_parentage_type_code' => $continuity['claimParentageTypeCode'] ?? null,
            'claim_parentage_type_description' => $continuity['claimParentageTypeCodeDescription'] ?? null,
        ], $continuityBag);
    }
}
