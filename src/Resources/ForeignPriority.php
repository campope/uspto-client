<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\ForeignPriorityResponse;

class ForeignPriority extends BaseResource
{
    public function find(string $applicationNumber): ForeignPriorityResponse
    {
        return ForeignPriorityResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/foreign-priority")->json());
    }
}
