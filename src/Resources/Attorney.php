<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\AttorneyResponse;

class Attorney extends BaseResource
{
    public function find(string $applicationNumber): AttorneyResponse
    {
        return AttorneyResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/attorney")->json());
    }
}
