<?php
namespace Kbrw\RiakBundle\Service\WebserviceClient;

use Guzzle\Http\Message\Response;

abstract class BaseServiceClient
{
    /**
     * @return \Guzzle\Service\Client
     */
    public function getClient($guzzleClientProvider, $config)
    {
        return $guzzleClientProvider->getClient($this->route, $config);
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * All dependencies needs to be injected by services.{xml|yml} or by an annotation on child class
     */

    /**
     * @var string
     */
    public $route;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    public $logger;
}
