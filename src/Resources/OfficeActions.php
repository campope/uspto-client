<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\USPTOClient;
use RuntimeException;

class OfficeActions extends BaseResource
{
    public function __construct(USPTOClient $client, array $options = [])
    {
        parent::__construct($client, $options);

        $this->client = $this->client->withBaseUrl('https://developer.uspto.gov/ds-api');
    }

    public function findActionTextByCriteria(string $criteria = '*:*', int $start = 0, int $rows = 100): array
    {
        try {
            return $this->client->asForm()->post('/oa_actions/v1/records', [
                'criteria' => $criteria,
                'start' => $start,
                'rows' => $rows,
            ])->json();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Failed to fetch office action data from USPTO: '.$exception->getMessage(), previous: $exception);
        }
    }

    public function findCitationByCriteria(string $criteria = '*:*', int $start = 0, int $rows = 100): array
    {
        try {
            return $this->client->asForm()->post('/oa_citations/v2/records', [
                'criteria' => $criteria,
                'start' => $start,
                'rows' => $rows,
            ])->json();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Failed to fetch citation data from USPTO: '.$exception->getMessage(), previous: $exception);
        }
    }

    public function findByApplicationNumber(string $applicationNumber): array
    {
        return $this->formatData($this->findActionTextByCriteria("patentApplicationNumber:{$applicationNumber}"));
    }

    public function findByOfficeActionId(string $officeActionId): array
    {
        return $this->formatData($this->findActionTextByCriteria("id:{$officeActionId}"));
    }

    protected function formatData(array $actions): array
    {
        $formattedData = [];

        if (! isset($actions['response']['docs']) || ! is_array($actions['response']['docs'])) {
            return $formattedData;
        }

        foreach ($actions['response']['docs'] as $action) {
            $bodyText = $action['bodyText'] ?? null;
            $formattedData[] = [
                'uspto_office_action_id' => $action['id'] ?? null,
                'action_type' => $this->determineActionType($action),
                'action_status' => $this->determineStatus($action),
                'action_date' => $action['submissionDate'] ?? $action['createDateTime'] ?? null,
                'response_due_date' => null,
                'action_summary' => is_array($bodyText) ? ($bodyText[0] ?? null) : $bodyText,
                'response_summary' => null,
                'examiner_name' => $this->getExaminerName($action),
                'is_final' => $this->isFinalAction($action),
            ];
        }

        return $formattedData;
    }

    protected function determineActionType(array $action): string
    {
        if (isset($action['legacyDocumentCodeIdentifier'])) {
            $code = is_array($action['legacyDocumentCodeIdentifier']) ? $action['legacyDocumentCodeIdentifier'][0] : $action['legacyDocumentCodeIdentifier'];

            return match ($code) {
                'CTNF' => 'Non-Final Rejection',
                'CTFR' => 'Final Rejection',
                'NOA' => 'Notice of Allowance',
                'CTAV' => 'Advisory Action',
                'CTRS' => 'Restriction Requirement',
                'CTEQ' => 'Ex Parte Quayle Action',
                default => 'Unknown',
            };
        }

        if (isset($action['actionTypeCategory'])) {
            return match ($action['actionTypeCategory']) {
                'rejected' => 'Rejection',
                'objected' => 'Objection',
                default => 'Unknown',
            };
        }

        return 'Unknown';
    }

    protected function determineStatus(array $action): string
    {
        if (! isset($action['documentActiveIndicator'])) {
            return 'Closed';
        }

        $status = is_array($action['documentActiveIndicator']) ? $action['documentActiveIndicator'][0] : $action['documentActiveIndicator'];

        return $status === '1' ? 'Active' : 'Closed';
    }

    protected function isFinalAction(array $action): bool
    {
        if (isset($action['legacyDocumentCodeIdentifier'])) {
            $legacyCode = is_array($action['legacyDocumentCodeIdentifier']) ? $action['legacyDocumentCodeIdentifier'][0] : $action['legacyDocumentCodeIdentifier'];

            return $legacyCode === 'CTFR';
        }

        return isset($action['actionTypeCategory'], $action['bodyText'])
            && $action['actionTypeCategory'] === 'rejected'
            && stripos(is_array($action['bodyText']) ? implode(' ', $action['bodyText']) : (string) $action['bodyText'], 'final rejection') !== false;
    }

    protected function getExaminerName(array $action): ?string
    {
        foreach (['sections.examinerEmployeeNumber', 'examinerEmployeeNumber'] as $field) {
            if (isset($action[$field])) {
                return is_array($action[$field]) ? ($action[$field][0] ?? null) : $action[$field];
            }
        }

        return null;
    }
}
