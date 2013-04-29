<?php
namespace Kbrw\RiakBundle\Service\WebserviceClient;

abstract class BaseServiceClient
{
    public static $processId;
    
    public static function getProcessId()
    {
        if (empty(self::$processId))
            self::$processId = hash("sha256", rand(0, 100000) . time());
        return self::$processId;
    }
    
    /**
     * @return \Guzzle\Service\Client
     */
    public function getClient($guzzleClientProvider, $config)
    {
        return $guzzleClientProvider->getClient($this->route, $config);
    }
    
    /**
     * @param \Guzzle\Http\Message\Response $response
     * @param array $extra
     */
    public function logResponse($response, $extra = array())
    {
        !array_key_exists("serialization_time", $extra)   && $extra["serialization_time"] = "-";
        !array_key_exists("deserialization_time", $extra) && $extra["deserialization_time"] = "-";
        !array_key_exists("search_time", $extra)          && $extra["search_time"] = "-";
        !array_key_exists("method", $extra)               && $extra["method"] = "-";
        $this->logger->debug(
                self::getProcessId() . ' ' .
                $extra["method"] . ' ' . 
                $response->getInfo("url") . ' ' . 
                $response->getInfo("total_time") . ' ' . 
                $extra["deserialization_time"] . ' ' . 
                $extra["serialization_time"] . ' ' . 
                $extra["search_time"]
        );
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
    
    public function __construct($logger = null)
    {
        $this->setLogger($logger);
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
