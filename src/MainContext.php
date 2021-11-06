<?php

namespace SaaSFormation\Vendor\FunctionalTests;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Psr\Http\Message\ResponseInterface;
use React\Http\Message\ServerRequest;
use SaaSFormation\Vendor\API\Kernel;

abstract class MainContext implements Context
{
    protected ResponseInterface $response;
    protected Kernel $kernel;

    public function __construct()
    {
        $this->kernel = $this->bootKernel();
    }

    protected abstract function bootKernel(): Kernel;

    /**
     * @Given /^I call "([^"]*)" "([^"]*)"$/
     */
    public function iCall(string $method, string $path)
    {
        $this->response = $this->kernel->run(
            new ServerRequest($method, $path)
        );
    }

    /**
     * @Then /^the status code should be (\d+)$/
     */
    public function theStatusCodeShouldBe(int $statusCode)
    {
        return $statusCode === $this->response->getStatusCode();
    }

    /**
     * @Given /^the response should be exactly this JSON:$/
     */
    public function theResponseShouldBeExactlyThisJSON(PyStringNode $JSON)
    {
        $responseJSONBody = (string)$this->response->getBody();
        $expectedJSONBody = json_encode(json_decode($JSON->getRaw()));

        return $responseJSONBody === $expectedJSONBody;
    }
}
