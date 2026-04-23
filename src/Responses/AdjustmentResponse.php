<?php

namespace RadicalDreamers\UsptoClient\Responses;

class AdjustmentResponse
{
    public function __construct(
        public int $count,
        public string $application_number,
        public array $patent_term_adjustment_data,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];

        return new self(
            $json['count'],
            $wrapper['applicationNumberText'],
            self::extractPatentTermAdjustmentData($wrapper['patentTermAdjustmentData'] ?? []),
        );
    }

    private static function extractPatentTermAdjustmentData(array $data): array
    {
        return [
            'a_delay_quantity' => $data['aDelayQuantity'] ?? null,
            'adjustment_total_quantity' => $data['adjustmentTotalQuantity'] ?? null,
            'applicant_day_delay_quantity' => $data['applicantDayDelayQuantity'] ?? null,
            'b_delay_quantity' => $data['bDelayQuantity'] ?? null,
            'c_delay_quantity' => $data['cDelayQuantity'] ?? null,
            'filing_date' => $data['filingDate'] ?? null,
            'grant_date' => $data['grantDate'] ?? null,
            'non_overlapping_day_quantity' => $data['nonOverlappingDayQuantity'] ?? null,
            'overlapping_day_quantity' => $data['overlappingDayQuantity'] ?? null,
            'ip_office_day_delay_quantity' => $data['ipOfficeDayDelayQuantity'] ?? null,
            'history' => self::extractAdjustmentHistory($data['patentTermAdjustmentHistoryDataBag'] ?? []),
        ];
    }

    private static function extractAdjustmentHistory(array $historyBag): array
    {
        return array_map(static fn (array $event): array => [
            'event_date' => $event['eventDate'] ?? null,
            'applicant_day_delay_quantity' => $event['applicantDayDelayQuantity'] ?? null,
            'event_description' => $event['eventDescriptionText'] ?? null,
            'event_sequence_number' => $event['eventSequenceNumber'] ?? null,
            'ip_office_day_delay_quantity' => $event['ipOfficeDayDelayQuantity'] ?? null,
            'originating_event_sequence_number' => $event['originatingEventSequenceNumber'] ?? null,
            'pta_pte_code' => $event['ptaPteCode'] ?? null,
        ], $historyBag);
    }
}
