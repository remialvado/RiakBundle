<?php
namespace Kbrw\RiakBundle\Tests\Service\WebserviceClient\Riak;

use Kbrw\RiakBundle\Tests\BaseTestCase;
use Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient;
use Kbrw\RiakBundle\Model\KV\Datas;
use Kbrw\RiakBundle\Model\KV\Data;
use Symfony\Component\HttpFoundation\HeaderBag;
use Kbrw\RiakBundle\Tests\Model\Bucket\SomeClass;
use Kbrw\RiakBundle\Model\Cluster\Cluster;
use Kbrw\RiakBundle\Model\Bucket\Bucket;

/**
 * @author remi
 */
class RiakKVServiceClientTest extends BaseTestCase
{
    /**
     * @var \Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient
     */
    protected $riakKVServiceClient;

    public function setup()
    {
        parent::setup();
        $this->riakKVServiceClient = new RiakKVServiceClient();
        $this->riakKVServiceClient->contentTypeNormalizer = $this->getService("kbrw.content.type.normalizer");
        $this->riakKVServiceClient->serializer = $this->getService("jms_serializer");
        $this->riakKVServiceClient->logger = $this->getService("logger");
    }

    /**
     * @test
     */
    public function normalizeDatas_associativeArrayProvided()
    {
        // prepare expectation
        $expectedDatas = $this->getExpectationsForNormalizeDatasTests();

        // Run test
        $objects = array("foo1" => new SomeClass("bar1"));
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, "json", "test"));
    }

    /**
     * @test
     */
    public function normalizeDatas_dataProvided()
    {
        // prepare expectation
        $expectedDatas = $this->getExpectationsForNormalizeDatasTests();

        // Run test
        $objects = new Data("foo1", new SomeClass("bar1"));
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, "json", "test"));
    }

    /**
     * @test
     */
    public function normalizeDatas_dataArrayProvided()
    {
        // prepare expectation
        $expectedDatas = $this->getExpectationsForNormalizeDatasTests();

        // Run test
        $objects = array(new Data("foo1", new SomeClass("bar1")));
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, "json", "test"));
    }

    /**
     * @test
     */
    public function normalizeDatas_datasProvided()
    {
        // prepare expectation
        $expectedDatas = $this->getExpectationsForNormalizeDatasTests();

        // Run test
        $objects = new Datas(array(new Data("foo1", new SomeClass("bar1"))));
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, "json", "test"));
    }

    /**
     * @test
     */
    public function normalizeDatas_objectProvided()
    {
        // prepare expectation
        $expectedDatas = $this->getExpectationsForNormalizeDatasTests();
        $expectedDatas->first()->setKey(null); // in this configuration, no key is defined.

        // Run test
        $objects = new SomeClass("bar1");
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, "json", "test"));
    }

    /**
     * @test
     */
    public function normalizeDatas_arrayOfKeys()
    {
        // prepare expectation
        $commonHeaderBag = new HeaderBag(array("Content-Type" => "text/plain", "X-Riak-ClientId" => "test"));
        $expectedDatas = new Datas(array(new Data("foo1", null, $commonHeaderBag, null), new Data("foo2", null, $commonHeaderBag, null)));

        // Run test
        $objects = array("foo1", "foo2");
        $this->assertEquals($expectedDatas, $this->riakKVServiceClient->normalizeDatas($objects, null, "test", true));
    }

    /**
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    protected function getExpectationsForNormalizeDatasTests()
    {
        return new Datas(array(
            new Data(
                    "foo1",
                    new SomeClass("bar1"),
                    new HeaderBag(array(
                        "Content-Type"    => "application/json",
                        "X-Riak-ClientId" => "test"
                    )),
                    '{"id":"bar1"}'
            )
        ));
    }

    /**
     * @test
     */
    public function prepareRequests()
    {
        $datas = new Datas(array(
            new Data("foo1", new SomeClass("bar1"), new HeaderBag(array("Content-Type" => "application/json", "X-Riak-ClientId" => "test")), '{"id":"bar1"}'),
            new Data("foo2", new SomeClass("bar2"), new HeaderBag(array("Content-Type" => "application/json", "X-Riak-ClientId" => "test")), '{"id":"bar2"}'),
        ));
        $request = $this->getMock("Guzzle\Http\Message\RequestInterface");
        $client = $this->getMock("Guzzle\Service\ClientInterface");
        $client->expects($this->exactly(2))
               ->method("createRequest")
               ->will($this->returnValue($request));
        $curlMulti = $this->getMock("Guzzle\Http\Curl\CurlMultiInterface");
        $curlMulti->expects($this->exactly(2))
                  ->method("add")
                  ->with($this->equalTo($request));
        $this->riakKVServiceClient->prepareRequests("PUT", $datas, $curlMulti, $client);
    }

    /**
     * @test
     */
    public function splitDeleteRequestsWhenMaxParallelCallsIsReached()
    {
        $riakKVServiceClient = $this->getMockBuilder("Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient")
                                    ->setMethods(array("doDelete"))
                                    ->getMock();
        $riakKVServiceClient->contentTypeNormalizer = $this->getService("kbrw.content.type.normalizer");
        $riakKVServiceClient->serializer = $this->getService("jms_serializer");
        $riakKVServiceClient->logger = $this->getService("logger");
        $riakKVServiceClient->expects($this->exactly(3))
                            ->method("doDelete")
                            ->will($this->returnValue(true));
        $bucket = new Bucket();
        $bucket->setFormat("json");
        $cluster = new Cluster();
        $cluster->setMaxParallelCalls(2);
        // the 5 delete requests will be done using THREE curl_multi requests
        $riakKVServiceClient->delete($cluster, $bucket, array("foo1", "foo2", "foo3", "foo4", "foo5"));
    }

    /**
     * @test
     */
    public function splitPutRequestsWhenMaxParallelCallsIsReached()
    {
        $riakKVServiceClient = $this->getMockBuilder("Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient")
                                    ->setMethods(array("doPut"))
                                    ->getMock();
        $riakKVServiceClient->contentTypeNormalizer = $this->getService("kbrw.content.type.normalizer");
        $riakKVServiceClient->serializer = $this->getService("jms_serializer");
        $riakKVServiceClient->logger = $this->getService("logger");
        $riakKVServiceClient->expects($this->exactly(3))
                            ->method("doPut")
                            ->will($this->returnValue(true));
        $bucket = new Bucket();
        $bucket->setFormat("json");
        $cluster = new Cluster();
        $cluster->setMaxParallelCalls(2);
        // the 5 put requests will be done using THREE curl_multi requests
        $riakKVServiceClient->put($cluster, $bucket, array(
            "foo1" => new SomeClass("bar1"),
            "foo2" => new SomeClass("bar2"),
            "foo3" => new SomeClass("bar3"),
            "foo4" => new SomeClass("bar4"),
            "foo5" => new SomeClass("bar5"),
        ));
    }

    /**
     * @test
     */
    public function splitFetchRequestsWhenMaxParallelCallsIsReached()
    {
        $riakKVServiceClient = $this->getMockBuilder("Kbrw\RiakBundle\Service\WebserviceClient\Riak\RiakKVServiceClient")
                                    ->setMethods(array("doFetch"))
                                    ->getMock();
        $riakKVServiceClient->contentTypeNormalizer = $this->getService("kbrw.content.type.normalizer");
        $riakKVServiceClient->serializer = $this->getService("jms_serializer");
        $riakKVServiceClient->logger = $this->getService("logger");
        $riakKVServiceClient->expects($this->exactly(3))
                            ->method("doFetch")
                            ->will($this->returnValue(new Datas()));
        $bucket = new Bucket();
        $bucket->setFormat("json");
        $cluster = new Cluster();
        $cluster->setMaxParallelCalls(2);
        // the 5 put requests will be done using THREE curl_multi requests
        $riakKVServiceClient->fetch($cluster, $bucket, array("foo1", "foo2", "foo3", "foo4", "foo5"));
    }
}
