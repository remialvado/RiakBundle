<?php

namespace Kbrw\RiakBundle\Model\Bucket;

use JMS\Serializer\Annotation as Ser;

/** 
 * @Ser\AccessType("public_method") 
 * @Ser\XmlRoot("bucket")
 */
class Bucket
{
    
    /** 
     * @var \Kbrw\RiakBundle\Model\Bucket\Props
     * @Ser\Type("Kbrw\RiakBundle\Model\Bucket\Props") 
     * @Ser\SerializedName("props")
     * @Ser\Since("1")
     */
    protected $props;
    
    /**
     * @Ser\Exclude()
     * @var string
     */
    protected $fullyQualifiedClassName;
    
    /**
     * @Ser\Exclude()
     * @var string
     */
    protected $format;
    
    /**
     * @Ser\Exclude()
     * @var \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    protected $cluster;
    
    /**
     * @Ser\Exclude()
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakBucketServiceClient
     */
    public $riakBucketServiceClient;
    
    /**
     * @Ser\Exclude()
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient
     */
    public $riakKVServiceClient;
    
    /**
     * @Ser\Exclude()
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakSearchServiceClient
     */
    public $riakSearchServiceClient;
 
    /**
     * @param string $name
     * @param \Kbrw\RiakBundle\Model\Bucket\Props $props
     */
    function __construct($name = null, $props = null)
    {
        if (!isset($props)) $props = new Props();
        $props->setName($name);
        $this->setProps($props);
    }
    
    /**
     * @param array<string> | string $keys
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($keys)
    {
        return $this->riakKVServiceClient->fetch($this->cluster, $this, $keys);
    }
    
    /**
     * @param string $keys
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($key)
    {
        return $this->riakKVServiceClient->uniq($this->cluster, $this, $key);
    }
    
    /**
     * @param array<string, mixed> $objects
     * @return boolean
     */
    public function put($objects)
    {
        return $this->riakKVServiceClient->put($this->cluster, $this, $objects);
    }
    
    /**
     * @param array<string> $keys
     * @return boolean
     */
    public function delete($keys)
    {
        return $this->riakKVServiceClient->delete($this->cluster, $this, $keys);
    }
    
    /**
     * WARNING : this function may be slow and not suited to production uses. 
     * See Riak documentation for closer details.
     * @return array<string>
     */
    public function keys()
    {
        return $this->riakBucketServiceClient->keys($this->cluster, $this);
    }
    
    /**
     * WARNING : this function may be slow and not suited to production uses. 
     * See Riak documentation for closer details.
     * @return integer
     */
    public function count()
    {
        return $this->riakBucketServiceClient->count($this->cluster, $this);
    }
    
    /**
     * @return boolean
     */
    public function save()
    {
        return $this->riakBucketServiceClient->save($this->cluster, $this);
    }
    
    public function search($query)
    {
        return $this->riakSearchServiceClient->search($this->cluster, $this, $query);
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->props->getName();
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->props->setName($name);
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\Bucket\Props
     */
    public function getProps()
    {
        return $this->props;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\Bucket\Props $props
     */
    public function setProps($props)
    {
        $this->props = $props;
    }
    
    public function enableSearchIndexing()
    {
        $searchIndexingHook = ErlangCall::getErlangCallUsedToIndexData();
        if (!$this->props->hasPreCommitHook($searchIndexingHook))
        {
            $this->props->addPrecommit($searchIndexingHook);
        }
    }
    
    public function disableSearchIndexing()
    {
        $searchIndexingHook = ErlangCall::getErlangCallUsedToIndexData();
        if ($this->props->hasPreCommitHook($searchIndexingHook))
        {
            $this->props->removePrecommit($searchIndexingHook);
        }
        
    }
    
    /**
     * @return boolean
     */
    public function isSearchIndexingEnabled()
    {
        return $this->props->hasPreCommitHook(ErlangCall::getErlangCallUsedToIndexData());
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
    
    public function getFullyQualifiedClassName()
    {
        return $this->fullyQualifiedClassName;
    }

    public function setFullyQualifiedClassName($fullyQualifiedClassName)
    {
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    public function setCluster($cluster)
    {
        $this->cluster = $cluster;
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
}