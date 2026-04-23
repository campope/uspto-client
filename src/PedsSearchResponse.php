<?php

namespace RadicalDreamers\UsptoClient;

use ArrayAccess;
use JsonSerializable;
use RuntimeException;

class PedsSearchResponse implements ArrayAccess, JsonSerializable
{
    public function __construct(
        private array $response
    ) {
        if (! isset($response['queryResults'])) {
            throw new RuntimeException('Invalid PEDS response');
        }

        $this->response = $response['queryResults'];
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function getAllRelevantFields(): array
    {
        return [
            'firstInventorName' => $this->getFirstInventor(),
            'assigneeName' => $this->getAssigneeName(),
            'filingDate' => $this->getFilingDate(),
            'actualPatentTitle' => $this->getActualPatentTitle(),
            'dockNumber' => $this->getDockNumber(),
        ];
    }

    public function toArray(): array
    {
        return $this->getAllRelevantFields();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('PedsSearchResponse is read-only');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('PedsSearchResponse is read-only');
    }

    private function getFirstInventor(): string
    {
        return $this->response['searchResponse']['response']['docs'][0]['inventors'][0]['nameLineOne'].' '.$this->response['searchResponse']['response']['docs'][0]['inventors'][0]['nameLineTwo'];
    }

    private function getAssigneeName(): string
    {
        return $this->response['searchResponse']['response']['docs'][0]['assignments'][0]['assignee'][0]['assigneeName'];
    }

    private function getFilingDate(): string
    {
        return $this->response['searchResponse']['response']['docs'][0]['appFilingDate'];
    }

    private function getActualPatentTitle(): string
    {
        return $this->response['searchResponse']['response']['docs'][0]['patentTitle'];
    }

    private function getDockNumber(): string
    {
        return $this->response['searchResponse']['response']['docs'][0]['appAttrDockNumber'];
    }
}
