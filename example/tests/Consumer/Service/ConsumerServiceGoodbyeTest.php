<?php

namespace Consumer\Service;

use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\Exception\MissingEnvVariableException;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class ConsumerServiceGoodbyeTest extends TestCase
{
    /**
     * @throws MissingEnvVariableException
     */
    public function testGetGoodbyeString()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/goodbye/Bob')
            ->addHeader('Content-Type', 'application/json');

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'application/json')
            ->setBody([
                'message' => 'Goodbye, Bob'
            ]);

        $config      = new MockServerEnvConfig();
        $builder     = new InteractionBuilder($config);
        $builder
            ->given('Get Goodbye')
            ->uponReceiving('A get request to /goodbye/{name}')
            ->with($request)
            ->willRespondWith($response);
        $builder->createMockServer();

        $service = new HttpClientService($config->getBaseUri());
        $result  = $service->getGoodbyeString('Bob');

        $this->assertTrue($builder->verify());

        $this->assertEquals('Goodbye, Bob', $result);
    }
}
