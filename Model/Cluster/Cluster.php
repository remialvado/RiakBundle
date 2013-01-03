<?php

namespace Kbrw\RiakBundle\Model\Cluster;

use \Kbrw\RiakBundle\Model\Bucket\Bucket;
use \Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author remi
 */
class Cluster
{
    const DEFAULT_NAME                           = "default";
    const DEFAULT_PROTOCOL                       = "http";
    const DEFAULT_DOMAIN                         = "localhost";
    const DEFAULT_PORT                           = "8098";
    const DEFAULT_CLIENT_ID                      = "demo";
    const DEFAULT_MAX_PARALLEL_CALLS             = 50;
    const DEFAULT_GUZZLE_CLIENT_PROVIDER_SERVICE = "kbrw.guzzle.client.provider";

    protected $name;
    protected $protocol;
    protected $domain;
    protected $port;
    protected $clientId;
    protected $maxParallelCalls;
    protected $guzzleClientProviderService;

    /**
     * @var array<string, \Kbrw\RiakBundle\Model\Bucket\Bucket>
     */
    protected $buckets;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakBucketServiceClient
     */
    protected $riakBucketServiceClient;

    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient
     */
    protected $riakKVServiceClient;

    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakSearchServiceClient
     */
    protected $riakSearchServiceClient;

    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakMapReduceServiceClient
     */
    protected $riakMapReduceServiceClient;

    public function __construct($name = null, $protocol = null, $domain = null, $port = null, $clientId = null, $maxParallelCalls = null, $bucketConfigs = array(), $guzzleClientProviderService = null, $eventDispatcher = null, $riakBucketServiceClient = null, $riakKVServiceClient = null, $riakSearchServiceClient = null, $riakMapReduceServiceClient = null)
    {
        if (!isset($name))                          $name                          = self::DEFAULT_NAME;
        if (!isset($protocol))                      $protocol                      = self::DEFAULT_PROTOCOL;
        if (!isset($domain))                        $domain                        = self::DEFAULT_DOMAIN;
        if (!isset($port))                          $port                          = self::DEFAULT_PORT;
        if (!isset($clientId))                      $clientId                      = self::DEFAULT_CLIENT_ID;
        if (!isset($maxParallelCalls))              $maxParallelCalls              = self::DEFAULT_MAX_PARALLEL_CALLS;
        $this->setName($name);
        $this->setProtocol($protocol);
        $this->setDomain($domain);
        $this->setPort($port);
        $this->setClientId($clientId);
        $this->setMaxParallelCalls($maxParallelCalls);
        $this->setGuzzleClientProviderService($guzzleClientProviderService);
        $this->setEventDispatcher($eventDispatcher);
        $this->setRiakBucketServiceClient($riakBucketServiceClient);
        $this->setRiakKVServiceClient($riakKVServiceClient);
        $this->setRiakSearchServiceClient($riakSearchServiceClient);
        $this->setRiakMapReduceServiceClient($riakMapReduceServiceClient);
        $this->buckets = array();
        foreach ($bucketConfigs as $bucketName => $bucketConfig) {
            $class = isset($bucketConfig["class"]) ? $bucketConfig["class"] : "\Kbrw\RiakBundle\Model\Bucket\Bucket";
            $bucket = new $class();
            $bucket->setName($bucketName);
            $bucket->setFormat($bucketConfig["format"]);
            $bucket->setFullyQualifiedClassName($bucketConfig["fqcn"]);
            $this->addBucket($bucket);
        }
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    public function mapReduce()
    {
        
    }

    /**
     * @param  string | \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @param  boolean                                       $buildFromCluster
     * @return \Kbrw\RiakBundle\Model\Bucket\Bucket
     */
    public function addBucket(&$bucket, $buildFromCluster = false)
    {
        if ($buildFromCluster) {
            $bucketName = ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) ? $bucket->getName() : $bucket;
            $bucket = $this->bucketProperties($bucketName);
        }
        if (! $bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) {
            $bucket = new Bucket($bucket);
        }
        $bucket->setRiakBucketServiceClient($this->riakBucketServiceClient);
        $bucket->setRiakKVServiceClient($this->riakKVServiceClient);
        $bucket->setRiakSearchServiceClient($this->riakSearchServiceClient);
        $bucket->setCluster($this);
        if (isset($this->eventDispatcher)) {
            $event = new GenericEvent("riak.bucket.add");
            $event->setArgument("bucket", $bucket);
            $this->eventDispatcher->dispatch("riak.bucket.add", $event);
        }
        $this->buckets[$bucket->getName()] = $bucket;

        return $bucket;
    }

    /**
     * @param string | \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     */
    public function hasBucket($bucket)
    {
        $bucketName = ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) ? $bucket->getName() : $bucket;

        return isset($this->buckets[$bucketName]);
    }

    /**
     * @param  string | \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @return \Kbrw\RiakBundle\Model\Bucket\Bucket
     */
    public function getBucket($bucket, $buildFromCluster = false)
    {
        $bucketName = ($bucket instanceof \Kbrw\RiakBundle\Model\Bucket\Bucket) ? $bucket->getName() : $bucket;
        if (!$this->hasBucket($bucketName)) {
            $this->addBucket($bucket, $buildFromCluster);
        }

        return $this->buckets[$bucketName];
    }

    /**
     * @param  string                               $bucketName
     * @return \Kbrw\RiakBundle\Model\Bucket\Bucket
     */
    public function bucketProperties($bucketName)
    {
        return $this->riakBucketServiceClient->properties($this, $bucketName);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
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

    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    public function getMaxParallelCalls()
    {
        return $this->maxParallelCalls;
    }

    public function setMaxParallelCalls($maxParallelCalls)
    {
        $this->maxParallelCalls = $maxParallelCalls;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getGuzzleClientProviderService()
    {
        return $this->guzzleClientProviderService;
    }

    public function setGuzzleClientProviderService($guzzleClientProviderService)
    {
        $this->guzzleClientProviderService = $guzzleClientProviderService;
    }

    public function getRiakBucketServiceClient()
    {
        return $this->riakBucketServiceClient;
    }

    public function setRiakBucketServiceClient($riakBucketServiceClient)
    {
        $this->riakBucketServiceClient = $riakBucketServiceClient;
    }

    public function getRiakKVServiceClient()
    {
        return $this->riakKVServiceClient;
    }

    public function setRiakKVServiceClient($riakKVServiceClient)
    {
        $this->riakKVServiceClient = $riakKVServiceClient;
    }

    public function getRiakSearchServiceClient()
    {
        return $this->riakSearchServiceClient;
    }

    public function setRiakSearchServiceClient($riakSearchServiceClient)
    {
        $this->riakSearchServiceClient = $riakSearchServiceClient;
    }
    
    public function getRiakMapReduceServiceClient()
    {
        return $this->riakMapReduceServiceClient;
    }

    public function setRiakMapReduceServiceClient($riakMapReduceServiceClient)
    {
        $this->riakMapReduceServiceClient = $riakMapReduceServiceClient;
    }

    public function getBuckets()
    {
        return $this->buckets;
    }

    public function setBuckets($buckets)
    {
        $this->buckets = $buckets;
    }
}
