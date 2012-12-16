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
    
    /**
     * Post requests are really slow using Guzzle. I don't know why but using curl 
     * directly is the only solution we found so far.
     * @return \Guzzle\Http\Message\Response
     */
    public function executePostRequest($url, $data, $headers = null)
    {
        $ch = curl_init();
        $this->logger->debug("[POST] $url");
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        if (is_array($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return new Response($httpCode, null, $result);
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