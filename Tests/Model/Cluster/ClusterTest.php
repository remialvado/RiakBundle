<?php
namespace Kbrw\RiakBundle\Tests\Model\Cluster;

use Kbrw\RiakBundle\Model\Cluster\Cluster;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak\MockedRiakKVServiceClient;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak\MockedRiakBucketServiceClient;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Guzzle\SimpleGuzzleClientProviderTest;
use Kbrw\RiakBundle\Model\Bucket\Bucket;
use Kbrw\RiakBundle\Tests\BaseTestCase;

class BucketTest extends BaseTestCase
{
    
    /**
     * @var \Kbrw\RiakBundle\Model\Bucket\Bucket
     */
    protected $bucket;
    
    /**
     * @var \Kbrw\RiakBundle\Model\Cluster\Cluster
     */
    protected $cluster;
    
    public function setup()
    {
        parent::setup();
        
        // create bucket
        $this->bucket = new Bucket("foo");
        $this->bucket->getProps()->setNVal(5);
        
        // insert bucket into a cluster
        $riakKVServiceClient = new MockedRiakKVServiceClient($this->getContainer());
        $riakBucketServiceClient = new MockedRiakBucketServiceClient($this->getContainer());
        $guzzleClientProviderTest = new SimpleGuzzleClientProviderTest();
        $guzzleClientProvider = $guzzleClientProviderTest->getGuzzleClientProvider();
        $eventDispatcher = $this->getService("event_dispatcher");
        $this->cluster = new Cluster("backend", "http", "localhost", "1234", "frontend", 50, array(), $guzzleClientProvider, $eventDispatcher, $riakBucketServiceClient, $riakKVServiceClient);
        $this->cluster->addBucket($this->bucket);
    }
    
    /**
     * @test
     */
    public function connectToLocalhostByDefault()
    {
        $cluster = new Cluster();
        $this->assertEquals("http", $cluster->getProtocol());
        $this->assertEquals("localhost", $cluster->getDomain());
        $this->assertEquals("8098", $cluster->getPort());
    }
    
    /**
     * @test
     */
    public function createBucketsAccordingToConfiguration()
    {
        $bucketConfigurations = array(
            "users" => array(
                "fqcn" => "MyCompanyBundle\Model\User",
                "format" => "json"
            ),
            "cities" => array(
                "fqcn" => "MyCompanyBundle\Model\City",
                "format" => "xml"
            )
        );
        $cluster = new Cluster(null,null,null,null,null,null,$bucketConfigurations);
        $this->assertEquals("MyCompanyBundle\Model\User", $cluster->getBucket("users")->getFullyQualifiedClassName());
        $this->assertEquals("json", $cluster->getBucket("users")->getFormat());
        $this->assertEquals("MyCompanyBundle\Model\City", $cluster->getBucket("cities")->getFullyQualifiedClassName());
        $this->assertEquals("xml", $cluster->getBucket("cities")->getFormat());
    }
    
    /**
     * @test
     */
    public function assertBucketExistUsingBucketName()
    {
        $this->assertTrue($this->cluster->hasBucket("foo"));
    }
    
    /**
     * @test
     */
    public function assertBucketExistUsingBucketInstance()
    {
        $this->assertTrue($this->cluster->hasBucket(new Bucket("foo")));
    }
    
    /**
     * @test
     */
    public function addBucketUsingBucketName()
    {
        $bucketName = "bar";
        $bucket = $this->cluster->addBucket($bucketName);
        $this->assertEquals($bucketName, $bucket->getName());
        $this->assertEquals($this->cluster, $bucket->getCluster());
        $this->assertEquals($this->cluster->getRiakBucketServiceClient(), $bucket->getRiakBucketServiceClient());
        $this->assertEquals($this->cluster->getRiakKVServiceClient(), $bucket->getRiakKVServiceClient());
    }
    
    /**
     * @test
     */
    public function addBucketUsingBucketInstance()
    {
        $bucket = new Bucket("bar");
        $this->cluster->addBucket($bucket);
        $this->assertEquals("bar", $bucket->getName());
        $this->assertEquals($this->cluster, $bucket->getCluster());
        $this->assertEquals($this->cluster->getRiakBucketServiceClient(), $bucket->getRiakBucketServiceClient());
        $this->assertEquals($this->cluster->getRiakKVServiceClient(), $bucket->getRiakKVServiceClient());
    }
    
    /**
     * @test
     */
    public function getExistingBucket()
    {
        $this->assertEquals($this->bucket, $this->cluster->getBucket("foo"));
        $this->assertEquals($this->bucket, $this->cluster->getBucket(new Bucket("foo")));
    }
    
    /**
     * @test
     */
    public function getNonExistingBucket()
    {
        // check that a default bucket has been created
        $this->assertEquals(3, $this->cluster->getBucket("bar")->getProps()->getNVal());
        // check that already known buckets are kept untouched
        $this->assertEquals(5, $this->cluster->getBucket("foo")->getProps()->getNVal());
    }
}
