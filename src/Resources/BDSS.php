<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\USPTOClient;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use ZipArchive;

class BDSS extends BaseResource
{
    protected string $baseUrl = 'https://bulkdata.uspto.gov:443/BDSS-API';

    protected PendingRequest $bdssClient;

    public function __construct(USPTOClient $client, array $options = [])
    {
        parent::__construct($client, $options);

        $this->bdssClient = Http::acceptJson()->baseUrl($this->baseUrl);
    }

    public function getProducts(): array
    {
        $response = $this->bdssClient->get('/products/all/latest');

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch products. HTTP Status: '.$response->status());
        }

        return $response->json();
    }

    public function getPopularProducts(): array
    {
        $response = $this->bdssClient->get('/products/popular');

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch products. HTTP Status: '.$response->status());
        }

        return $response->json();
    }

    public function getProductByShortName(string $shortName, array $filters = []): array
    {
        $validFilters = ['fromYear', 'toYear', 'fromMonth', 'toMonth', 'fromDay', 'toDay', 'fromDate', 'toDate', 'hierarchy'];

        $queryParams = array_filter($filters, static fn (string $key): bool => in_array($key, $validFilters, true), ARRAY_FILTER_USE_KEY);
        $response = $this->bdssClient->get("/products/{$shortName}", $queryParams);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch product. HTTP Status: '.$response->status());
        }

        return $response->json();
    }

    public function getProductFiles(string $shortName): array
    {
        $response = $this->bdssClient->get("/products/{$shortName}/files");

        if (! $response->successful()) {
            throw new RuntimeException("Failed to fetch files for product {$shortName}. HTTP Status: ".$response->status());
        }

        return $response->json();
    }

    public function downloadFile(string $url): string
    {
        $response = Http::get($url);

        if (! $response->successful()) {
            throw new RuntimeException("Failed to download file from {$url}. HTTP Status: ".$response->status());
        }

        return $response->body();
    }

    public function extractArchive(string $archivePath, string $extractTo): void
    {
        $zip = new ZipArchive;

        if ($zip->open($archivePath) !== true) {
            throw new RuntimeException("Failed to open archive {$archivePath}");
        }

        $zip->extractTo($extractTo);
        $zip->close();
    }

    public function extractClaims(string $xmlFilePath): array
    {
        $claims = [];
        $xml = simplexml_load_file($xmlFilePath, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            throw new RuntimeException("Failed to parse XML file {$xmlFilePath}");
        }

        foreach ($xml->claims->claim as $claim) {
            $claims[] = (string) $claim;
        }

        return $claims;
    }
}
