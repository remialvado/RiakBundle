<?php

namespace Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak;

use Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakBucketServiceClient;
use Kbrw\RiakBundle\Model\Bucket\Props;

class MockedRiakBucketServiceClient extends RiakBucketServiceClient
{
    public $content = array();
    public $bucket = null;
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @return array<string>
     */
    public function keys($cluster, $bucket)
    {
        return array_keys($this->content);
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @return integer
     */
    public function count($cluster, $bucket)
    {
        return count($this->content);
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param string $bucketName
     * @return \Kbrw\RiakBundle\Model\Bucket\Bucket
     */
    public function properties($cluster, $bucketName)
    {
        return (isset($this->bucket)) ? $this->bucket : new Bucket($bucketName);
    }
    
    /**
     * @param \Kbrw\RiakBundle\Model\Cluster\Cluster $cluster
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     * @return boolean
     */
    public function save($cluster, $bucket)
    {
        $this->bucket = $bucket;
        return true;
    }
    
    function __construct($container) {}
}