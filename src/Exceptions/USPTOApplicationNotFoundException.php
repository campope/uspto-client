<?php

namespace RadicalDreamers\UsptoClient\Exceptions;

use RuntimeException;

class USPTOApplicationNotFoundException extends RuntimeException
{
    public function __construct(string $applicationNumber, int $statusCode = 404)
    {
        parent::__construct("USPTO application {$applicationNumber} was not found.", $statusCode);
    }
}
