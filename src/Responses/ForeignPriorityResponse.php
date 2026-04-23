<?php

namespace RadicalDreamers\UsptoClient\Responses;

class ForeignPriorityResponse
{
    public function __construct(
        public int $count,
        public string $application_number,
        public array $foreign_priorities,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];

        return new self(
            $json['count'],
            $wrapper['applicationNumberText'],
            self::extractForeignPriorities($wrapper['foreignPriorityBag'] ?? []),
        );
    }

    private static function extractForeignPriorities(array $foreignPriorityBag): array
    {
        return array_map(static fn (array $priority): array => [
            'ip_office_name' => $priority['ipOfficeName'] ?? null,
            'filing_date' => $priority['filingDate'] ?? null,
            'application_number' => $priority['applicationNumberText'] ?? null,
        ], $foreignPriorityBag);
    }
}
