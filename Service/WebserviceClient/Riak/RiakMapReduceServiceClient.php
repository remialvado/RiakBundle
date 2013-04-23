<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Guzzle\Http\Exception\CurlException;
use Kbrw\RiakBundle\Exception\RiakUnavailableException;
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
            $extra = array("method" => "POST");
            $ts = microtime(true);
            $body = $this->getSerializer()->serialize($query, "json");
            $extra["serialization_time"] = microtime(true) - $ts;
            $request->setBody($body, "application/json");
            $response = $request->send();
            if ($response->getStatusCode() === 200) {
                $fqcn = $query->getResponseFullyQualifiedClassName();
                if (!empty($fqcn)) {
                    $ts = microtime(true);
                    $content = $this->getSerializer()->deserialize($response->getBody(true), $fqcn, "json");
                    $extra["serialization_time"] = microtime(true) - $ts;
                    $this->logResponse($response, $extra);
                    return $content;
                }
                $this->logResponse($response, $extra);
                return $response->getBody(true);
            }
            $this->logResponse($response, $extra);
        } catch (CurlException $e) {
            $this->logger->err("Riak is unavailable" . $e->getMessage());
            throw new RiakUnavailableException();
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
