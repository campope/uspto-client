<?php

namespace RadicalDreamers\UsptoClient;

use RadicalDreamers\UsptoClient\Resources\BaseResource;
use InvalidArgumentException;

class USPTOService
{
    public function __construct(
        protected ?USPTOClient $client = null
    ) {
        $this->client ??= app(USPTOClient::class);
    }

    public function __call(string $name, array $arguments): BaseResource
    {
        return $this->createResource($name, $arguments);
    }

    public static function __callStatic(string $name, array $arguments): BaseResource
    {
        return (new self)->createResource($name, $arguments);
    }

    protected function createResource(string $name, array $arguments): BaseResource
    {
        $class = __NAMESPACE__.'\\Resources\\'.ucfirst($name);

        if (! class_exists($class)) {
            throw new InvalidArgumentException('Invalid USPTO resource: '.ucfirst($name));
        }

        return new $class($this->client, $arguments[0] ?? []);
    }
}
