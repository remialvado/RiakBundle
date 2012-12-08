<?php

namespace Kbrw\RiakBundle\Service\WebserviceClient\Riak;

use Guzzle\Http\Message\RequestInterface;
use Kbrw\RiakBundle\Model\Transmutable;
use Kbrw\RiakBundle\Service\WebserviceClient\BaseServiceClient;
use Kbrw\RiakBundle\Model\KV\Data;
use Kbrw\RiakBundle\Model\KV\Datas;

class RiakKVServiceClient extends BaseServiceClient
{

    const MAX_PARALLEL_REQUESTS = 50;
    
    /**
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket || string $bucket
     * @param array<string, mixed> $objects
     * @param string $format
     * @param boolean $shouldSerialized
     * @return boolean
     */
    public function put($bucket, $objects, $format = "json", $shouldSerialized = true) {
        if (!is_array($objects)) $objects = array($objects);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($objects) > self::MAX_PARALLEL_REQUESTS) {
            $chunks = array_chunk($objects, self::MAX_PARALLEL_REQUESTS, true);
            $result = true;
            foreach($chunks as $chunk) {
                $tmpResult = $this->put($bucket, $chunk, $format, $shouldSerialized);
                $result = $result && $tmpResult;
            }
            return $result;
        }
        
        $r = null;
        $w = null;
        $dw = null;
        if ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) {
            $r  = $bucket->getProps()->getR();
            $w  = $bucket->getProps()->getW();
            $dw = $bucket->getProps()->getDw();
        }
               
        $client = $this->getClient($this->getConfig($bucket, $r, $w, $dw));
        $curlMulti = $client->getCurlMulti();
        $requests = array();

        foreach ($objects as $key => $object) {
            $headers = array();
            if ($object instanceof \Kbrw\RiakBundle\Model\KV\Data) {
                $key = $object->getKey();
                $headers = $object->getHeaders();
            }
            
            // Prepare Key 
            $key = trim($key);
            
            // Prepare Body
            $content = $object;
            if ($shouldSerialized) {
                if (method_exists($object, "preSerialize"))
                {
                    $object->preSerialize();
                }
                $content = $this->getSerializer()->serialize($object, $format);
            }
            
            // Prepare Headers
            if (!is_array($headers)) $headers = array();
            $headers['X-Riak-ClientId'] = $this->clientId;
            $headers['Content-Type'] = $this->contentTypeNormalizer->getContentType($format);
            
            // Prepare Request
            $request = $client->put($key, $headers, $content);
            $requests[] = $request;
            $curlMulti->add($request, true);
            $this->logger->debug("[PUT] '" . $request->getUrl() . "'");
        }

        try {
            $curlMulti->send();
        } catch (\Exception $e) {
            $this->logger->error("Unable to put an object into Riak. Full message is : \n" . $e->getMessage . "");
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
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket || string $bucket
     * @param array<string> $keys
     * @return boolean
     */
    public function delete($bucket, $keys)
    {
        if (!is_array($keys)) $keys = array($keys);
        
        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > self::MAX_PARALLEL_REQUESTS) {
            $chunks = array_chunk($keys, self::MAX_PARALLEL_REQUESTS);
            $result = true;
            foreach($chunks as $chunk) {
                $tmpResult = $this->delete($bucket, $chunk);
                $result = $result && $tmpResult;
            }
            return $result;
        }
        
        $w = null;
        $dw = null;
        if ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) {
            $w  = $bucket->getProps()->getW();
            $dw = $bucket->getProps()->getDw();
        }
        
        $client = $this->getClient($this->getConfig($bucket, null, $w, $dw));
        $curlMulti = $client->getCurlMulti();
        $requests = array();
        $headers = array('X-Riak-ClientId' => $this->clientId);

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
            $this->logger->error("Unable to delete an object in Riak. Full message is : \n" . $e->getMessage . "");
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
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket || string $bucket $bucket
     * @param array<string> $keys
     * @param string $objectFQCN
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($bucket, $keys, $objectFQCN = null) {
        $datas = $this->get($bucket, $keys, $objectFQCN)->getDatas();
        return (is_array($datas) && count($datas) > 0) ? $datas[0] : null;
    }

    /**
     * 
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket || string $bucket
     * @param array<string> $keys
     * @param string $objectFQCN
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function get($bucket, $keys, $objectFQCN = null) {
        if (!is_array($keys)) $keys = array($keys);
        $datas = new Datas();
        
        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > self::MAX_PARALLEL_REQUESTS) {
            $chunks = array_chunk($keys, self::MAX_PARALLEL_REQUESTS);
            foreach($chunks as $chunk) {
                $datas->addAll($this->get($bucket, $chunk, $objectFQCN));
            }
            return $datas;
        }
        
        $r = null;
        if ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) {
            $r  = $bucket->getProps()->getR();
        }
        
        $client = $this->getClient($this->getConfig($bucket, $r));
        $curlMulti = $client->getCurlMulti();
        $requests = array();
        $headers = array('X-Riak-ClientId' => $this->clientId);
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
            $this->logger->error("Unable to get an object from Riak. Full message is : \n" . $e->getMessage . "");
        }

        foreach ($requests as $key => $request) {
            $data = new Data($key);
            try {
                if ($request->getState() === RequestInterface::STATE_COMPLETE && $request->getResponse()->getStatusCode() == "200") {
                    $response = $request->getResponse();
                    $data->setRawContent($response->getBody(true));
                    if (isset($objectFQCN)) {
                        $riakKVObject = $this->serializer->deserialize($data->getRawContent(), $objectFQCN, $this->contentTypeNormalizer->getNormalizedContentType($response->getContentType()));
                        if ($riakKVObject !== false) {
                            if (method_exists($riakKVObject, "postDeserialize")) {
                                $riakKVObject->postDeserialize($data);
                            }
                            if ($riakKVObject instanceof Transmutable) {
                                $riakKVObject = $riakKVObject->transmute();
                            }
                            $data->setStructuredContent($riakKVObject);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error("Unable to create the Data object for key '$key'. Full message is : \n" . $e->getMessage . "");
            }
            $datas->add($data);
        }
        return $datas;
    }
    
    /**
     * 
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket || string $bucket
     * @param string $key
     * @return string
     */
    public function getUri($bucket, $key) {
        $client = $this->getClient($this->getConfig($bucket));
        return $client->get($key)->getUrl();
    }
    
    public function getKeys($dataStore) {
        $keys = array();
        $client = $this->getClient($this->getConfig($dataStore));
        $request = $client->get("?keys=stream&props=false");
        $request->getCurlOptions()->set(CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$keys) {
            $content = json_decode($data, true);
            if (is_array($content) && array_key_exists("keys", $content)) {
                foreach($content["keys"] as $key) {
                    $keys[] = $key;
                }
            }
            return strlen($data);
        });
        if ($this->debug) error_log("[GET] url : " . $request->getUrl());
        try {
            $request->send();            
        }
        catch(\Exception $e) {
            if ($this->debug) error_log("Error while getting keys" . $e->getMessage());
        }
        return $keys;
    }
    
    public function countKeys($dataStore) {
        $keys = 0;
        $client = $this->getClient($this->getConfig($dataStore));
        $request = $client->get("?keys=stream&props=false");
        $request->getCurlOptions()->set(CURLOPT_WRITEFUNCTION, function($ch, $data) use (&$keys) {
            $content = json_decode($data, true);
            if (is_array($content) && array_key_exists("keys", $content)) {
                $keys += count($content["keys"]);
            }
            return strlen($data);
        });
        if ($this->debug) error_log("[GET] url : " . $request->getUrl());
        try {
            $request->send();            
        }
        catch(\Exception $e) {
            if ($this->debug) error_log("Error while getting keys" . $e->getMessage());
        }
        return $keys;
    }
    
    public function getConfig($bucket = null, $r = null, $w = null, $dw = null)
    {
        $config = parent::getConfig();
        $config["bucket"] = $bucket;
        $config["r"]  = $r;
        $config["w"]  = $w;
        $config["dw"] = $dw;
        return $config;
    }
    
    /**
     * @var \Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer
     */
    public $contentTypeNormalizer;
    
    /**
     * @var string
     */
    public $clientId;
}