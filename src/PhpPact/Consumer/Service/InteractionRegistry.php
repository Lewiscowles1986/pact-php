<?php

namespace PhpPact\Consumer\Service;

use PhpPact\Consumer\Driver\Interaction\DriverInterface;
use PhpPact\Consumer\Driver\Interaction\InteractionDriverInterface;
use PhpPact\Consumer\Model\Interaction;

class InteractionRegistry implements InteractionRegistryInterface
{
    public function __construct(
        private InteractionDriverInterface $driver,
        private MockServerInterface $mockServer
    ) {
    }

    public function verifyInteractions(): bool
    {
        $matched = $this->mockServer->isMatched();

        try {
            if ($matched) {
                $this->writePact();
            }
        } finally {
            $this->cleanUp();
        }

        return $matched;
    }

    public function registerInteraction(Interaction $interaction): bool
    {
        $this
            ->newInteraction($interaction)
            ->given($interaction)
            ->uponReceiving($interaction)
            ->with($interaction)
            ->willRespondWith($interaction)
            ->startMockServer();

        return true;
    }

    private function cleanUp(): void
    {
        $this->mockServer->cleanUp();
    }

    private function writePact(): void
    {
        $this->mockServer->writePact();
    }

    private function newInteraction(Interaction $interaction): self
    {
        $this->driver->newInteraction($interaction->getDescription());

        return $this;
    }

    private function given(Interaction $interaction): self
    {
        $this->driver->given($interaction->getProviderStates());

        return $this;
    }

    private function uponReceiving(Interaction $interaction): self
    {
        $this->driver->uponReceiving($interaction->getDescription());

        return $this;
    }

    private function with(Interaction $interaction): self
    {
        $request = $interaction->getRequest();
        $this->driver->withRequest($request->getMethod(), $request->getPath());
        $this->driver->withHeaders(DriverInterface::REQUEST, $request->getHeaders());
        $this->driver->withQueryParameters($request->getQuery());
        $this->driver->withBody(DriverInterface::REQUEST, null, $request->getBody());

        return $this;
    }

    private function willRespondWith(Interaction $interaction): self
    {
        $response = $interaction->getResponse();
        $this->driver->withResponse($response->getStatus());
        $this->driver->withHeaders(DriverInterface::RESPONSE, $response->getHeaders());
        $this->driver->withBody(DriverInterface::RESPONSE, null, $response->getBody());

        return $this;
    }

    private function startMockServer(): void
    {
        $this->mockServer->start();
    }
}
