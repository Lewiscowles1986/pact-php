<?php

namespace PhpPact\Standalone\StubService\Service;

use PhpPact\Exception\ConnectionException;
use PhpPact\Http\ClientInterface;
use PhpPact\Standalone\StubService\StubServerConfigInterface;

/**
 * Http Service that interacts with the Ruby Standalone Stub Server.
 *
 * @see https://github.com/pact-foundation/pact-stub_service
 */
class StubServerHttpService implements StubServerHttpServiceInterface
{
    private ClientInterface $client;

    private StubServerConfigInterface $config;

    public function __construct(ClientInterface $client, StubServerConfigInterface $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function healthCheck(): bool
    {
        $uri = $this->config->getBaseUri()->withPath('/');

        $response = $this->client->get($uri, [
            'headers' => [
                'Content-Type'        => 'application/json',
                'X-Pact-Mock-Service' => true,
            ],
        ]);

        $body = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200
            || $body !== "Mock service running\n") {
            throw new ConnectionException('Failed to receive a successful response from the Stub Server.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
     */
    public function getJson(): string
    {
        $uri      = $this->config->getBaseUri()->withPath('/' . $this->config->getEndpoint());
        $response = $this->client->get($uri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        return \json_encode(\json_decode($response->getBody()->getContents(), null, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
    }
}
