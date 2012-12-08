<?php
namespace Kbrw\RiakBundle\Service\WebserviceClient;

use Guzzle\Http\Message\Response;

abstract class BaseServiceClient 
{   
    /**
     * @return \Guzzle\Service\Client 
     */
    public function getClient($config = null)
    {
        return $this->guzzleClientProvider->getClient($this->route, isset($config) ? $config : $this->getConfig());
    }
    
    public function getConfig()
    {
        return array(
                "domain" => $this->getDomain(),
                "port"   => $this->getPort()
               );
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

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    public function getGuzzleClientProvider()
    {
        return $this->guzzleClientProvider;
    }

    public function setGuzzleClientProvider($guzzleClientProvider)
    {
        $this->guzzleClientProvider = $guzzleClientProvider;
    }
    
    /**
     * All dependencies needs to be injected by services.{xml|yml} or by an annotation on child class
     */
    
    /**
     * @var string
     */
    public $route;
    
    /**
     * @var string
     */
    public $domain;
    
    /**
     * @var string
     */
    public $port;
    
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    public $logger;
    
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    public $serializer;
    
    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\GuzzleClientProviderInterface
     */
    public $guzzleClientProvider;
}