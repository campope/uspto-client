<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\ContinuityResponse;

class Continuity extends BaseResource
{
    public function find(string $applicationNumber): ContinuityResponse
    {
        return ContinuityResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/continuity")->json());
    }
}
