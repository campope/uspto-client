<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\AdjustmentResponse;

class Adjustment extends BaseResource
{
    public function find(string $applicationNumber): AdjustmentResponse
    {
        return AdjustmentResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/adjustment")->json());
    }
}
