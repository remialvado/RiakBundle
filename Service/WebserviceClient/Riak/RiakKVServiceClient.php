<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Guzzle\Http\Message\RequestInterface;
use Kbrw\RiakBundle\Model\KV\Transmutable;
use Kbrw\RiakBundle\Service\WebserviceClient\BaseServiceClient;
use Kbrw\RiakBundle\Model\KV\Data;
use Kbrw\RiakBundle\Model\KV\Datas;

class RiakKVServiceClient extends BaseServiceClient
{

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster                                                                 $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket                                                                   $bucket
     * @param  mixed | array<string, mixed> | array<\Kbrw\RiakBundle\Model\KV\Data> | \Kbrw\RiakBundle\Model\KV\Datas $datas
     * @return boolean
     */
    public function put($cluster, $bucket, $datas)
    {
        // normalize $datas parameter
        $datas = $this->normalizeDatas($datas, $bucket->getFormat(), $cluster->getClientId());

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        $chunks = $datas->chunk($cluster->getMaxParallelCalls());
        $result = true;
        foreach ($chunks as $chunk) {
            $tmpResult = $this->doPut($cluster, $bucket, $chunk);
            $result = $result && $tmpResult;
        }

        return $result;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  \Kbrw\RiakBundle\Model\KV\Datas        $datas
     * @return boolean
     */
    public function doPut($cluster, $bucket, $datas)
    {
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, $bucket->getProps()->getR(), $bucket->getProps()->getW(), $bucket->getProps()->getDw()));
        $curlMulti = $client->getCurlMulti();
        $requests = $this->prepareRequests(RequestInterface::PUT, $datas, $curlMulti, $client);

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to put an object into Riak. Full message is : \n" . $e->getMessage() . "");

            return false;
        }
        foreach ($requests as $request) {
            if ($request->getState() !== RequestInterface::STATE_COMPLETE ||
                    $request->getResponse()->getStatusCode() < 200 ||
                    $request->getResponse()->getStatusCode() >= 300) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster                                                                                            $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket                                                                                              $bucket
     * @param  string | array<string> | \Kbrw\RiakBundle\Model\KV\Data | array<\Kbrw\RiakBundle\Model\KV\Data> | \Kbrw\RiakBundle\Model\KV\Datas $datas
     * @return boolean
     */
    public function delete($cluster, $bucket, $datas)
    {
        // normalize $datas parameter
        $datas = $this->normalizeDatas($datas, $bucket->getFormat(), $cluster->getClientId(), true);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        $chunks = $datas->chunk($cluster->getMaxParallelCalls());
        $result = true;
        foreach ($chunks as $chunk) {
            $tmpResult = $this->doDelete($cluster, $bucket, $chunk);
            $result = $result && $tmpResult;
        }

        return $result;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  \Kbrw\RiakBundle\Model\KV\Datas        $datas
     * @return boolean
     */
    public function doDelete($cluster, $bucket, $datas)
    {
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, null, $bucket->getProps()->getW(), $bucket->getProps()->getDw()));
        $curlMulti = $client->getCurlMulti();
        $requests = $this->prepareRequests(RequestInterface::DELETE, $datas, $curlMulti, $client);

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to delete an object in Riak. Full message is : \n" . $e->getMessage() . "");

            return false;
        }
        foreach ($requests as $request) {
            if ($request->getState() !== RequestInterface::STATE_COMPLETE || $request->getResponse()->getStatusCode() !==  204) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster                                                                                            $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket                                                                                              $bucket
     * @param  string | array<string> | \Kbrw\RiakBundle\Model\KV\Data | array<\Kbrw\RiakBundle\Model\KV\Data> | \Kbrw\RiakBundle\Model\KV\Datas $datas
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($cluster, $bucket, $datas)
    {
        // normalize $datas parameter
        $datas = $this->normalizeDatas($datas, $bucket->getFormat(), $cluster->getClientId(), true);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        $result = new Datas();
        $chunks = $datas->chunk($cluster->getMaxParallelCalls());
        foreach ($chunks as $chunk) {
            $result->addAll($this->doFetch($cluster, $bucket, $chunk));
        }

        return $result;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  \Kbrw\RiakBundle\Model\KV\Datas        $datas
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function doFetch($cluster, $bucket, $datas)
    {
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, $bucket->getProps()->getR()));
        $curlMulti = $client->getCurlMulti();
        $requests = $this->prepareRequests(RequestInterface::GET, $datas, $curlMulti, $client);

        $result = new Datas();
        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to get an object from Riak. Full message is : \n" . $e->getMessage() . "");
        }

        foreach ($requests as $key => $request) {
            $data = new Data($key);
            try {
                if ($request->getState() === RequestInterface::STATE_COMPLETE && $request->getResponse()->getStatusCode() === 200 ) {
                    $response = $request->getResponse();
                    $data->setStringContent($response->getBody(true));
                    if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($bucket->getFormat())) {
                        $riakKVObject = $this->serializer->deserialize($data->getContent(true), $bucket->getFullyQualifiedClassName(), $this->contentTypeNormalizer->getNormalizedContentType($response->getContentType()));
                        if ($riakKVObject !== false) {
                            if ($riakKVObject instanceof Transmutable) {
                                $riakKVObject = $riakKVObject->transmute();
                            }
                            $data->setContent($riakKVObject);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->err("Unable to create the Data object for key '$key'. Full message is : \n" . $e->getMessage() . "");
            }
            $result->add($data);
        }

        return $result;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster                                                                                            $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket                                                                                              $bucket
     * @param  string | array<string> | \Kbrw\RiakBundle\Model\KV\Data | array<\Kbrw\RiakBundle\Model\KV\Data> | \Kbrw\RiakBundle\Model\KV\Datas $datas
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($cluster, $bucket, $datas)
    {
        return $this->fetch($cluster, $bucket, array($datas))->first();
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  string                                 $key
     * @return string
     */
    public function getUri($cluster, $bucket, $key)
    {
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket));

        return $client->get($key)->getUrl();
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @return array<string,string>
     */
    public function getConfig($cluster, $bucket, $r = null, $w = null, $dw = null)
    {
        $config = array();
        $config["protocol"] = $cluster->getProtocol();
        $config["domain"]   = $cluster->getDomain();
        $config["port"]     = $cluster->getPort();
        $config["bucket"]   = $bucket->getName();
        $config["r"]        = $r;
        $config["w"]        = $w;
        $config["dw"]       = $dw;

        return $config;
    }

    /**
     * @param  mixed | \Kbrw\RiakBundle\Model\KV\Data | array<string, mixed> | array<\Kbrw\RiakBundle\Model\KV\Data> | \Kbrw\RiakBundle\Model\KV\Datas $objects
     * @param  string                                                                                                                                  $format
     * @param  string                                                                                                                                  $clientId
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function normalizeDatas($objects, $format, $clientId, $objectsAreKeys = false)
    {
        $datas = new Datas();

        /* Normalize $objects to become an array<Data>*/
        // $objects is already a Datas instance.
        if ($objects instanceof \Kbrw\RiakBundle\Model\KV\Datas) $objects = $objects->getDatas();
        elseif ($objects instanceof \Kbrw\RiakBundle\Model\KV\Data) $objects = array($objects);
        elseif (is_string($objects) && $objectsAreKeys) $objects = array(new Data($objects));
        elseif (is_object($objects) && !$objectsAreKeys) $objects = array(new Data(null, $objects));
        else {
            $tmp = array();
            // $objects is an array<string, mixed>
            foreach ($objects as $key => $value) {
                if ($value instanceof \Kbrw\RiakBundle\Model\KV\Data) $tmp[] = $value;
                else {
                    if ($objectsAreKeys) $data = new Data(trim($value));
                    else $data = new Data(trim($key), $value);
                    $tmp[] = $data;
                }
            }
            $objects = $tmp;
        }

        foreach ($objects as $data) {
            // prepare headers
            $data->getHeaderBag()->set("X-Riak-ClientId", $clientId);
            $data->getHeaderBag()->set("Content-Type",    $this->contentTypeNormalizer->getContentType($format));

            // prepare string representation
            if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($format)) {
                $data->setStringContent($this->serializer->serialize($data->getContent(), $format));
            } elseif ($data->getContent() !== null) {
                $data->setStringContent($data->getContent());
            }

            $datas->add($data);
        }

        return $datas;
    }

    /**
     * @param  string                                       $method
     * @param  \Kbrw\RiakBundle\Model\KV\Datas              $datas
     * @param  \Guzzle\Http\Curl\CurlMultiInterface         $curlMulti
     * @param  \Guzzle\Service\Client                       $client
     * @return array<\Guzzle\Http\Message\RequestInterface>
     */
    public function prepareRequests($method, $datas, &$curlMulti, $client)
    {
        $requests = array();
        foreach ($datas->getDatas() as $data) {
            $request = $client->createRequest($method, $data->getKey(), $data->getHeaderBag()->all(), $data->getContent(true));
            $this->logger->debug("[$method] '" . $request->getUrl() . "'");
            $curlMulti->add($request);
            $requests[$data->getKey()] = $request;
        }

        return $requests;
    }

    public function getContentTypeNormalizer()
    {
        return $this->contentTypeNormalizer;
    }

    public function setContentTypeNormalizer($contentTypeNormalizer)
    {
        $this->contentTypeNormalizer = $contentTypeNormalizer;
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
     * @var \Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer
     */
    public $contentTypeNormalizer;

    /**
     * @var \JMS\Serializer\Serializer
     */
    public $serializer;
}
