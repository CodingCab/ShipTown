<?php

namespace App\Modules\DpdUk\src\Api;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class ApiResponse
{
    /**
     * @var ResponseInterface
     */
    public ResponseInterface $http_response;
    /**
     * @var string
     */
    public string $content;

    public function __construct(ResponseInterface $response)
    {
        $this->http_response = $response;
        $this->content = $response->getBody()->getContents();
    }

    public function toArray(): array
    {
        return json_decode($this->content, true);
    }

    public function toCollection(): Collection
    {
        return collect($this->toArray());
    }
}
