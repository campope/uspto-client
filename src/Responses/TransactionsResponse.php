<?php

namespace RadicalDreamers\UsptoClient\Responses;

use Carbon\Carbon;

class TransactionsResponse
{
    public function __construct(
        public int $count,
        public string $application_number,
        public array $events,
    ) {}

    public static function fromJson(array $json): self
    {
        $wrapper = $json['patentFileWrapperDataBag'][0];

        return new self(
            $json['count'],
            $wrapper['applicationNumberText'],
            self::extractEvents($wrapper['eventDataBag'] ?? []),
        );
    }

    private static function extractEvents(array $eventDataBag): array
    {
        return array_map(static fn (array $event): array => [
            'event_code' => $event['eventCode'] ?? null,
            'event_description' => $event['eventDescriptionText'] ?? null,
            'event_date' => $event['eventDate'] ?? null,
        ], $eventDataBag);
    }

    public function getOfficeActionMailingDate(): ?Carbon
    {
        $officeActionEvents = $this->getOfficeActionEvents();

        if ($officeActionEvents === []) {
            return null;
        }

        usort($officeActionEvents, static fn (array $left, array $right): int => strcmp((string) $right['event_date'], (string) $left['event_date']));

        return isset($officeActionEvents[0]['event_date']) ? Carbon::parse($officeActionEvents[0]['event_date']) : null;
    }

    public function getOfficeActionResponseDeadline(): ?Carbon
    {
        $mailingDate = $this->getOfficeActionMailingDate();

        return $mailingDate?->copy()->addMonths(3);
    }

    public function getOfficeActionEvents(): array
    {
        $officeActionKeywords = ['office action', 'rejection', 'restriction', 'advisory', 'quayle', 'final', 'non-final', 'nonfinal'];
        $officeActionCodes = ['ctnf', 'ctfr', 'ctrs', 'ctap', 'ctav', 'cteq', 'ctqf'];

        return array_values(array_filter($this->events, static function (array $event) use ($officeActionKeywords, $officeActionCodes): bool {
            $description = strtolower((string) ($event['event_description'] ?? ''));
            $code = strtolower((string) ($event['event_code'] ?? ''));

            foreach ($officeActionKeywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    return true;
                }
            }

            return in_array($code, $officeActionCodes, true);
        }));
    }

    public function getDeadlineEvents(): array
    {
        $deadlineKeywords = ['deadline', 'due', 'response period', 'statutory period', 'extension', 'time limit'];

        return array_values(array_filter($this->events, static function (array $event) use ($deadlineKeywords): bool {
            $description = strtolower((string) ($event['event_description'] ?? ''));

            foreach ($deadlineKeywords as $keyword) {
                if (str_contains($description, $keyword)) {
                    return true;
                }
            }

            return false;
        }));
    }
}
