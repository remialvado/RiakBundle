<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce;

use Kbrw\RiakBundle\Tests\BaseTestCase;

abstract class KeyFilterTestCase extends BaseTestCase
{
    protected $serializer;

    public function setup()
    {
        parent::setup();
        $this->serializer = $this->getService("jms_serializer");
    }

    /**
     * @dataProvider getExpectations
     */
    public function testArrayTransformation($expectedJson, $keyFilter)
    {
        $root = new \Kbrw\RiakBundle\Model\MapReduce\Operator\Root();
        $root->addKeyFilter($keyFilter);
        $this->assertEquals($expectedJson, $this->serializer->serialize($root->toArray(), "json"));
    }

    abstract public function getExpectations();
}
