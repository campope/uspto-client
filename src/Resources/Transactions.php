<?php

namespace RadicalDreamers\UsptoClient\Resources;

use RadicalDreamers\UsptoClient\Responses\TransactionsResponse;

class Transactions extends BaseResource
{
    public function find(string $applicationNumber): TransactionsResponse
    {
        return TransactionsResponse::fromJson($this->client->get("/api/v1/patent/applications/{$applicationNumber}/transactions")->json());
    }
}
