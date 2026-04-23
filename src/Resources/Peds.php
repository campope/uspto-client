<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\PedsSearchResponse;
use RadicalDreamers\UsptoClient\USPTOClient;
use RuntimeException;

class Peds extends BaseResource
{
    public function __construct(USPTOClient $client, array $options = [])
    {
        parent::__construct($client, $options);

        $this->client = $this->client->withBaseUrl('https://ped.uspto.gov/api');
    }

    public function findClaims(string $applicationNumber): array
    {
        return array_values(array_filter($this->findByApplicationNumber($applicationNumber), static fn (array $item): bool => ($item['documentCode'] ?? null) === 'CLM'));
    }

    public function query(string $applicationNumber): PedsSearchResponse
    {
        try {
            return new PedsSearchResponse($this->client->post('/queries', [
                'df' => 'patentTitle',
                'facet' => 'false',
                'fl' => '*',
                'fq' => [],
                'mm' => '0%',
                'qf' => 'appEarlyPubNumber applId appLocation appType appStatus_txt appConfrNumber appCustNumber appGrpArtNumber appCls appSubCls appEntityStatus_txt patentNumber patentTitle inventorName firstNamedApplicant appExamName appExamPrefrdName appAttrDockNumber appPCTNumber appIntlPubNumber wipoEarlyPubNumber pctAppType firstInventorFile appClsSubCls rankAndInventorsList',
                'searchText' => "applId:({$applicationNumber})",
                'sort' => 'applId asc',
                'start' => '0',
            ])->json());
        } catch (\Throwable $exception) {
            throw new RuntimeException('Failed to fetch data from USPTO: '.$exception->getMessage(), previous: $exception);
        }
    }

    public function findByApplicationNumber(string $applicationNumber): array
    {
        try {
            return $this->client->get("/queries/cms/public/{$applicationNumber}")->json();
        } catch (\Throwable $exception) {
            throw new RuntimeException('Failed to fetch data from USPTO: '.$exception->getMessage(), previous: $exception);
        }
    }

    public function findCitations(string $applicationNumber): array
    {
        return array_values(array_filter($this->findByApplicationNumber($applicationNumber), static fn (array $item): bool => str_starts_with((string) ($item['documentCode'] ?? ''), 'CT')));
    }

    public function mostRecentCitation(string $applicationNumber): ?array
    {
        $citations = $this->findCitations($applicationNumber);

        return $citations[0] ?? null;
    }

    public function downloadPdf(string $pdfUrl): string
    {
        $response = $this->client->withHeaders(['Accept' => 'application/pdf'])->get("/queries/cms/{$pdfUrl}");

        if (! $response->successful()) {
            throw new RuntimeException('Failed to download PDF. HTTP Status: '.$response->status());
        }

        return $response->body();
    }
}
