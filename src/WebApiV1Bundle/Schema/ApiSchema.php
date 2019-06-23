<?php declare(strict_types=1);

namespace App\WebApiV1Bundle\Schema;

use App\WebApiV1Bundle\Endpoints\Endpoint;
use App\WebApiV1Bundle\Endpoints\Endpoints;
use App\WebApiV1Bundle\WebApiV1Bundle;

final class ApiSchema
{
    private $endpoints;

    public function __construct(Endpoints $endpoints)
    {
        $this->endpoints = $endpoints;
    }

    public function getEndpoints(): Endpoints
    {
        return $this->endpoints;
    }

    private function getOpenApiV2PathFragments(): array
    {
        $paths = [];
        foreach($this->endpoints->toIterable() as $endpoint) {
            /** @var $endpoint Endpoint */
            $endpointSchema = $endpoint::getSchema();
            $fragment = $endpointSchema->toOpenApiV2Array();
            $paths[$endpointSchema->getOpenApiPath()][$endpointSchema->getOpenApiMethod()] = $fragment;
        }
        return $paths;
    }

    public function toOpenApiV2Array(): array
    {
        return [
            'swagger' => '2.0',
            'info' => [
                'version' => WebApiV1Bundle::getVersion(),
                'title' => WebApiV1Bundle::getTitle(),
            ],
            'host' => getenv('APP_API_HOST_NAME'),
            'basePath' => WebApiV1Bundle::getBasePath(),
            'securityDefinitions' => [
                'ApiKeyAuthentication' => [
                    'type' => 'apiKey',
                    'name' => 'X-API-KEY',
                    'in' => 'header',
                ],
            ],
            'paths' => $this->getOpenApiV2PathFragments(),
        ];
    }
}