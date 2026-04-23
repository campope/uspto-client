<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\AssignmentResponse;

class Assignment extends BaseResource
{
    public function find(string $applicationNumber): AssignmentResponse
    {
        return AssignmentResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/assignment")->json());
    }
}
