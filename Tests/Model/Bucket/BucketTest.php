<?php
namespace Kbrw\RiakBundle\Tests\Model\Bucket;

use Kbrw\RiakBundle\Tests\Model\ModelTestCase;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak\MockedRiakKVServiceClient;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak\MockedRiakBucketServiceClient;
use Kbrw\RiakBundle\Tests\Service\WebserviceClient\Guzzle\SimpleGuzzleClientProviderTest;
use Kbrw\RiakBundle\Model\Cluster\Cluster;
use Kbrw\RiakBundle\Model\Bucket\Bucket;

use JMS\Serializer\Annotation as Ser;

/**
 * All calls to Riak*ServiceClients are mocked.
 * Setup() method defines what is inside the bucket.
 * Usually, buckets are retrieved by their service id (riak.bucket.<buckketName>)
 * but you can also create a Cluster and thus Buckets the same way it is done on the
 * setup method, allowing this bundle to work outside of Symfony Dependency Injector.
 */
class BucketTest extends ModelTestCase
{
    protected $serializarionMethod          = "json";
    protected $systemUnderTestFullClassName = "Kbrw\RiakBundle\Model\Bucket\Bucket";
    protected $testedModels                 = array("regular");
    
    protected function getTestFileBasePath()
    {
        return dirname(__FILE__) . "/../../../Resources/tests/model/bucket";
    }
    
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
        $this->bucket = new Bucket("some_classes");
        $this->bucket->setFormat("json");
        $this->bucket->setFullyQualifiedClassName("Kbrw\RiakBundle\Tests\Model\Bucket\SomeClass");
        
        // insert bucket into a cluster
        $riakKVServiceClient = new MockedRiakKVServiceClient($this->getContainer());
        $riakBucketServiceClient = new MockedRiakBucketServiceClient($this->getContainer());
        $guzzleClientProviderTest = new SimpleGuzzleClientProviderTest();
        $guzzleClientProvider = $guzzleClientProviderTest->getGuzzleClientProvider();
        $this->cluster = new Cluster("backend", "http", "localhost", "1234", "frontend", 50, array(), $guzzleClientProvider, $riakBucketServiceClient, $riakKVServiceClient);
        $this->cluster->addBucket($this->bucket);
        
        // insert fake data into this bucket for test purposes
        $this->insertData("foo1", new SomeClass("bar1"));
        $this->insertData("foo2", new SomeClass("bar2"));
    }
    
    /**
     * @test
     */
    public function fetchExistingData()
    {
        $datas = $this->bucket->fetch(array("foo1", "foo2"));
        $this->assertNotNull($datas);
        $this->assertContainData($datas, "foo1", new SomeClass("bar1"));
        $this->assertContainData($datas, "foo2", new SomeClass("bar2"));
        
        $datas = $this->bucket->fetch("foo2");
        $this->assertNotNull($datas);
        $this->assertContainData($datas, "foo2", new SomeClass("bar2"));
    }
    
    /**
     * @test
     */
    public function fetchNonExistingData()
    {
        $datas = $this->bucket->fetch(array("foo1", "foo3"));
        $this->assertNotNull($datas);
        $this->assertContainData($datas, "foo1", new SomeClass("bar1"));
        $this->assertNotContainData($datas, "foo3");
        
        $datas = $this->bucket->fetch("foo3");
        $this->assertNotNull($datas);
        $this->assertNotContainData($datas, "foo3");
    }
    
    /**
     * @test
     */
    public function uniqExistingData()
    {
        $data = $this->bucket->uniq("foo1");
        $this->assertNotNull($data);
        $this->assertEquals($data->getKey(), "foo1");
        $this->assertEquals($data->getStructuredContent(), new SomeClass("bar1"));
    }
    
    /**
     * @test
     */
    public function uniqNonExistingData()
    {
        $data = $this->bucket->uniq("foo3");
        $this->assertNotNull($data);
        $this->assertEquals($data->getKey(), "foo3");
        $this->assertEquals($data->getStructuredContent(), null);
    }
    
    /**
     * @test
     */
    public function putNewData()
    {
        $newObjects = array(
            "foo3" => new SomeClass("bar3"),
            "foo4" => new SomeClass("bar4"),
        );
        $this->assertTrue($this->bucket->put($newObjects));
        $this->assertContainData($this->bucket->fetch("foo3"), "foo3", new SomeClass("bar3"));
        $this->assertContainData($this->bucket->fetch("foo4"), "foo4", new SomeClass("bar4"));
    }
    
    /**
     * @test
     */
    public function replaceExistingData()
    {
        $replacements = array(
            "foo1" => new SomeClass("bar3"),
            "foo2" => new SomeClass("bar4"),
        );
        $this->assertTrue($this->bucket->put($replacements));
        $this->assertContainData($this->bucket->fetch("foo1"), "foo1", new SomeClass("bar3"));
        $this->assertContainData($this->bucket->fetch("foo2"), "foo2", new SomeClass("bar4"));
    }
    
    /**
     * @test
     */
    public function deleteExistingDatas()
    {
        $keys = array("foo1", "foo2");
        $this->assertTrue($this->bucket->delete($keys));
        $datas = $this->bucket->fetch($keys);
        $this->assertNotContainData($datas, "foo1");
        $this->assertNotContainData($datas, "foo2");
    }
    
    /**
     * @test
     */
    public function deleteNonExistingDatas()
    {
        $keys = array("foo2", "foo3");
        $this->assertFalse($this->bucket->delete($keys));
        $datas = $this->bucket->fetch(array("foo1", "foo2", "foo3"));
        $this->assertContainData($datas, "foo1", new SomeClass("bar1"));
        $this->assertNotContainData($datas, "foo2");
        $this->assertNotContainData($datas, "foo3");
    }
    
    /**
     * @test
     */
    public function getKeys()
    {
        $this->assertCount(2, $this->bucket->keys());
        $this->assertContains("foo1", $this->bucket->keys());
        $this->assertContains("foo2", $this->bucket->keys());
    }
    
    /**
     * @test
     */
    public function countKeys()
    {
        $this->assertEquals(2, $this->bucket->count());
    }
    
    /**
     * @test
     */
    public function saveBucketProperties()
    {
        $this->bucket->getProps()->setNVal(5);
        $this->assertTrue($this->bucket->save());
        $this->assertEquals(5, $this->cluster->bucketProperties($this->bucket->getName())->getProps()->getNVal());
    }
    
    /**
     * @param string $key
     * @param mixed $data
     * @param \Kbrw\RiakBundle\Model\Bucket\Bucket $bucket
     */
    protected function insertData($key, $data)
    {
        $serializedContent = $this->getService("jms_serializer")->serialize($data, $this->bucket->getFormat());
        $this->bucket->riakBucketServiceClient->content[$key] = $serializedContent;
        $this->bucket->riakKVServiceClient->content[$key]     = $serializedContent;
    }
    
    protected function assertContainData($datas, $key, $structuredContent)
    {
        $this->assertNotNull($datas);
        $datas = $datas->getDatas();
        $this->assertNotNull($datas);
        foreach($datas as $data) {
            if ($data->getKey() === $key) {
                $this->assertEquals($data->getStructuredContent(), $structuredContent);
                return;
            }
        }
        $this->fail("can't find key '$key' in datas structure.");
    }
    
    protected function assertNotContainData($datas, $key)
    {
        $this->assertContainData($datas, $key, null);
    }
}

/** 
 * @Ser\XmlRoot("some_class")
 */
class SomeClass
{
    /** 
     * @Ser\Type("string") 
     * @Ser\SerializedName("id")
     */
    public $id;
    
    public function __construct($id)
    {
        $this->id = $id;
    }
}
