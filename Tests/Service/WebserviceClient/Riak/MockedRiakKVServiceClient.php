<?php

namespace Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak;

use Kbrw\RiakBundle\Model\KV\Transmutable;
use Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient;
use Kbrw\RiakBundle\Model\KV\Data;
use Kbrw\RiakBundle\Model\KV\Datas;

class MockedRiakKVServiceClient extends RiakKVServiceClient
{

    public $content = array();

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  array<string, mixed>                   $objects
     * @return boolean
     */
    public function put($cluster, $bucket, $objects)
    {
        if (!is_array($objects)) $objects = array($objects);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($objects) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($objects, $cluster->getMaxParallelCalls(), true);
            $result = true;
            foreach ($chunks as $chunk) {
                $tmpResult = $this->put($cluster, $bucket, $chunk);
                $result = $result && $tmpResult;
            }

            return $result;
        }

        foreach ($objects as $key => $object) {
            // Prepare Key
            if ($object instanceof \Kbrw\RiakBundle\Model\KV\Data) {
                $key = $object->getKey();
            }
            $key = trim($key);

            // Prepare Body
            $content = $object;
            if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($bucket->getFormat())) {
                $content = $this->getSerializer()->serialize($object, $bucket->getFormat());
            }

            $this->content[$key] = $content;
        }

        return true;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  array<string>                          $keys
     * @return boolean
     */
    public function delete($cluster, $bucket, $keys)
    {
        if (!is_array($keys)) $keys = array($keys);

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($keys, $cluster->getMaxParallelCalls());
            $result = true;
            foreach ($chunks as $chunk) {
                $tmpResult = $this->delete($cluster, $bucket, $chunk);
                $result = $result && $tmpResult;
            }

            return $result;
        }

        $done = true;
        foreach ($keys as $key) {
            $key = trim($key);
            if (isset($this->content[$key])) {
                unset($this->content[$key]);
            } else {
                $done = false;
            }
        }

        return $done;
    }

    /**
     * @param  \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param  \Kbrw\RiakBundle\Model\Bucket\Bucket   $bucket
     * @param  array<string>                          $keys
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($cluster, $bucket, $keys)
    {
        if (!is_array($keys)) $keys = array($keys);
        $datas = new Datas();

        // Split work in smaller pieces to avoid exception caused by too many opened connections
        if (count($keys) > $cluster->getMaxParallelCalls()) {
            $chunks = array_chunk($keys, $cluster->getMaxParallelCalls());
            foreach ($chunks as $chunk) {
                $datas->addAll($this->fetch($cluster, $bucket, $chunk));
            }

            return $datas;
        }

        foreach ($keys as $key) {
            $key = trim($key);
            $data = new Data($key);
            if (isset($this->content[$key])) {
                $data->setStringContent($this->content[$key]);
                if ($this->contentTypeNormalizer->isFormatSupportedForSerialization($bucket->getFormat())) {
                    /*var_dump($data->getRawContent());
                    var_dump($bucket->getFullyQualifiedClassName());
                    var_dump($bucket->getFormat());
                    exit;*/
                    $riakKVObject = $this->serializer->deserialize($data->getContent(true), $bucket->getFullyQualifiedClassName(), $bucket->getFormat());
                    if ($riakKVObject !== false) {
                        if ($riakKVObject instanceof Transmutable) {
                            $riakKVObject = $riakKVObject->transmute();
                        }
                        $data->setContent($riakKVObject);
                    }
                }
            }
            $datas->add($data);
        }

        return $datas;
    }

    public function __construct($container)
    {
        $this->contentTypeNormalizer = $container->get("kbrw.content.type.normalizer");
        $this->serializer = $container->get("jms_serializer");
    }
}
