<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Guzzle\Http\Message\RequestInterface;
use Kbrw\RiakBundle\Model\Transmutable;
use Kbrw\RiakBundle\Service\WebserviceClient\BaseServiceClient;
use Kbrw\RiakBundle\Model\KV\Data;
use Kbrw\RiakBundle\Model\KV\Datas;

class RiakKVServiceClient extends BaseServiceClient
{
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param array<string, mixed> $objects
     * @return boolean
     */
    public function put($cluster, $bucket, $objects)
    {
        if (!is_array($objects)) $objects = array($objects);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($objects) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($objects, $cluster->getMaxParallelCalls(), true);
            $result = true;
            foreach($chunks as $chunk) {
                $tmpResult = $this->put($cluster, $bucket, $chunk);
                $result = $result && $tmpResult;
            }
            return $result;
        }
               
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, $bucket->getProps()->getR(), $bucket->getProps()->getW(), $bucket->getProps()->getDw()));
        $curlMulti = $client->getCurlMulti();
        $requests = array();

        foreach ($objects as $key => $object) {
            $headers = array();
            if ($object instanceof \Kbrw\RiakBundle\Model\KV\Data) {
                $key = $object->getKey();
                $headers = $object->getHeaderBag()->all();
                $object = $object->getContent();
            }
            
            // Prepare Key 
            $key = trim($key);
            
            // Prepare Body
            $content = $object;
            if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($bucket->getFormat())) {
                $content = $this->getSerializer()->serialize($object, $bucket->getFormat());
            }
            
            // Prepare Headers
            $headers['X-Riak-ClientId'] = $cluster->getClientId();
            $headers['Content-Type'] = $this->contentTypeNormalizer->getContentType($bucket->getFormat());
            
            // Prepare Request
            $request = $client->put($key, $headers, $content);
            $requests[] = $request;
            $curlMulti->add($request, true);
            $this->logger->debug("[PUT] '" . $request->getUrl() . "'");
        }

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to put an object into Riak. Full message is : \n" . $e->getMessage() . "");
            return false;
        }
        foreach ($requests as $request) {
            if ($request->getState() !== RequestInterface::STATE_COMPLETE ||
                    $request->getResponse()->getStatusCode() < "200" ||
                    $request->getResponse()->getStatusCode() >= "300") {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param array<string> $keys
     * @return boolean
     */
    public function delete($cluster, $bucket, $keys)
    {
        if (!is_array($keys)) $keys = array($keys);
        
        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($keys, $cluster->getMaxParallelCalls());
            $result = true;
            foreach($chunks as $chunk) {
                $tmpResult = $this->delete($cluster, $bucket, $chunk);
                $result = $result && $tmpResult;
            }
            return $result;
        }
        
        
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, null, $bucket->getProps()->getW(), $bucket->getProps()->getDw()));
        $curlMulti = $client->getCurlMulti();
        $requests = array();
        $headers = array('X-Riak-ClientId' => $cluster->getClientId());

        foreach ($keys as $key) {
            $key = trim($key);
            $request = $client->delete($key, $headers);
            $this->logger->debug("[DELETE] '" . $request->getUrl() . "'");
            $requests[] = $request;
            $curlMulti->add($request, true);
        }

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to delete an object in Riak. Full message is : \n" . $e->getMessage() . "");
            return false;
        }
        
        foreach($requests as $request) {
            if ($request->getState() !== RequestInterface::STATE_COMPLETE || $request->getResponse()->getStatusCode() !==  204) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param array<string> $keys
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($cluster, $bucket, $keys)
    {
        if (!is_array($keys)) $keys = array($keys);
        $datas = new Datas();
        
        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($keys, $cluster->getMaxParallelCalls());
            foreach($chunks as $chunk) {
                $datas->addAll($this->fetch($cluster, $bucket, $chunk));
            }
            return $datas;
        }
        
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket, $bucket->getProps()->getR()));
        $curlMulti = $client->getCurlMulti();
        $requests = array();
        $headers = array('X-Riak-ClientId' => $cluster->getClientId());
        foreach ($keys as $key) {
            $key = trim($key);
            $request = $client->get($key, $headers);
            $this->logger->debug("[GET] '" . $request->getUrl() . "'");
            $requests[$key] = $request;
            $curlMulti->add($request, true);
        }

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->err("Unable to get an object from Riak. Full message is : \n" . $e->getMessage() . "");
        }

        foreach ($requests as $key => $request) {
            $data = new Data($key);
            try {
                if ($request->getState() === RequestInterface::STATE_COMPLETE && $request->getResponse()->getStatusCode() == "200") {
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
            $datas->add($data);
        }
        return $datas;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param array<string> $key
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($cluster, $bucket, $key)
    {
        return $this->fetch($cluster, $bucket, array($key))->first();
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param string $key
     * @return string
     */
    public function getUri($cluster, $bucket, $key)
    {
        $client = $this->getClient($cluster->getGuzzleClientProviderService(), $this->getConfig($cluster, $bucket));
        return $client->get($key)->getUrl();
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
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