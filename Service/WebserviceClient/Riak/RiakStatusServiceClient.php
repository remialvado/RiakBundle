<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Kbrw\RiakBundle\Service\WebserviceClient\BaseServiceClient;
use Kbrw\RiakBundle\Exception\RiakUnavailableException;

class RiakStatusServiceClient extends BaseServiceClient
{
    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @return array<string,string>
     */
    public function status($cluster)
    {
        $request = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, "true"))->get();
        $this->logger->debug("[GET] '" . $request->getUrl() . "'");
        try {
            $response = $request->send();
            if ($response->getStatusCode() === 200) {
                $content = json_decode($response->getBody(true));
            }
        } catch (\Exception $e) {
            $this->logger->err("Error while getting buckets" . $e->getMessage());
            throw new RiakUnavailableException();
        }

        return $content;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @return array<string,string>
     */
    public function getConfig($cluster)
    {
        $config = array();
        $config["protocol"] = $cluster->getProtocol();
        $config["domain"]   = $cluster->getDomain();
        $config["port"]     = $cluster->getPort();

        return $config;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @var \JMS\Serializer\Serializer
     */
    public $serializer;
}
