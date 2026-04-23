<?php

namespace RadicalDreamers\UsptoClient\Responses;

class AssignmentResponse
{
    public function __construct(
        public int $count,
        public array $assignments,
    ) {}

    public static function fromJson(array $json): self
    {
        return new self(
            $json['count'],
            self::extractAssignments($json['patentFileWrapperDataBag'] ?? []),
        );
    }

    private static function extractAssignments(array $assignmentBags): array
    {
        return array_map(static function (array $bag): array {
            $assignment = $bag['assignmentBag'];

            return [
                'reel_number' => $assignment['reelNumber'] ?? null,
                'frame_number' => $assignment['frameNumber'] ?? null,
                'reel_frame' => $assignment['reelNumber/frameNumber'] ?? null,
                'page_number' => $assignment['pageNumber'] ?? null,
                'received_date' => $assignment['assignmentReceivedDate'] ?? null,
                'recorded_date' => $assignment['assignmentRecordedDate'] ?? null,
                'mailed_date' => $assignment['assignmentMailedDate'] ?? null,
                'conveyance_text' => $assignment['conveyanceText'] ?? null,
                'assignors' => self::extractAssignors($assignment['assignorBag'] ?? []),
                'assignees' => self::extractAssignees($assignment['assigneeBag'] ?? []),
                'correspondence_address' => self::extractCorrespondenceAddress($assignment['correspondenceAddressBag'] ?? []),
            ];
        }, $assignmentBags);
    }

    private static function extractAssignors(array $assignorBag): array
    {
        return array_map(static fn (array $assignor): array => [
            'name' => $assignor['assignorName'] ?? null,
            'execution_date' => $assignor['executionDate'] ?? null,
        ], $assignorBag);
    }

    private static function extractAssignees(array $assigneeBag): array
    {
        return array_map(static fn (array $assignee): array => [
            'name' => $assignee['assigneeNameText'] ?? null,
            'address' => self::extractAddress($assignee['assigneeAddress'] ?? []),
        ], $assigneeBag);
    }

    private static function extractAddress(array $address): array
    {
        return [
            'line1' => $address['addressLineOneText'] ?? null,
            'line2' => $address['addressLineTwoText'] ?? null,
            'city' => $address['cityName'] ?? null,
            'region' => $address['geographicRegionName'] ?? null,
            'region_code' => $address['geographicRegionCode'] ?? null,
            'country' => $address['countryName'] ?? null,
            'postal_code' => $address['postalCode'] ?? null,
        ];
    }

    private static function extractCorrespondenceAddress(array $correspondenceAddress): array
    {
        return [
            'name' => $correspondenceAddress['correspondentNameText'] ?? null,
            'line1' => $correspondenceAddress['addressLineOneText'] ?? null,
            'line2' => $correspondenceAddress['addressLineTwoText'] ?? null,
            'line3' => $correspondenceAddress['addressLineThreeText'] ?? null,
            'line4' => $correspondenceAddress['addressLineFourText'] ?? null,
        ];
    }
}
