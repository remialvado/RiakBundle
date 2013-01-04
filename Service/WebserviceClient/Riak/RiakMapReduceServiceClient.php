<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Kbrw\RiakBundle\Service\WebserviceClient\BaseServiceClient;

class RiakMapReduceServiceClient extends BaseServiceClient
{
    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  \Kbrw\RiakBundle\Model\MapReduce\Query $query
     * @return mixed
     */
    public function mapReduce($cluster, $query)
    {
        try {
            $request = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster))->post();
            $body = $this->getSerializer()->serialize($query, "json");
            $request->setBody($body, "application/json");
            $this->logger->debug("[POST] '" . $request->getUrl() . "'. Request body is : $body");
            $response = $request->send();
            if ($response->getStatusCode() === 200) {
                $fqcn = $query->getResponseFullyQualifiedClassName();
                if (!empty($fqcn)) {
                    return $this->getSerializer()->deserialize($response->getBody(true), $fqcn, "json");
                }
                return $response->getBody(true);
            }
        } catch (\Exception $e) {
            $this->logger->err("Unable to execute a mapreduce query. Full message is : \n" . $e->getMessage() . "");
        }

        return null;
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
