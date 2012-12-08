<?php
namespace Kbrw\RiakBundle\Tests\Model;

use Kbrw\RiakBundle\Model\KV\Transmutable;
use Kbrw\RiakBundle\Tests\BaseTestCase;

abstract class ModelTestCase extends BaseTestCase {
    
    protected $serializarionMethod          = "xml";
    protected $systemUnderTestFullClassName = null;
    protected $testedModels                 = array();
    protected $isSerializationTestable      = true;
    protected $isUnserializationTestable    = true;
    protected $isTransmutationTestable      = true;
    protected $serializer;
    
    public function setup()
    {
        parent::setup();
        $this->serializer = $this->getService("jms_serializer");
    }
    
    /**
     * @test
     * @dataProvider getObjectsAndFeedsAndTransmutedObjects
     */
    public function serialization($object, $expectedFeed, $expectedTransmutedObject)
    {
        if ($this->isSerializationTestable) {
            $this->assertEquals($this->serializer->serialize($object, $this->serializarionMethod), $expectedFeed);
        }
    }
    
    /**
     * @test
     * @dataProvider getObjectsAndFeedsAndTransmutedObjects
     */
    public function unserialization($expectedObject, $feed, $expectedTransmutedObject)
    {
        if ($this->isUnserializationTestable) {
            $unserializedObject = $this->serializer->deserialize($feed, $this->systemUnderTestFullClassName, $this->serializarionMethod);
            $this->assertNotNull($unserializedObject);
            $this->assertTrue($unserializedObject instanceof $this->systemUnderTestFullClassName);
            $this->assertEquals($unserializedObject, $expectedObject);
        }
    }
    
    /**
     * @test
     * @dataProvider getObjectsAndFeedsAndTransmutedObjects
     */
    public function transmute($expectedObject, $feed, $expectedTransmutedObject)
    {
        if ($this->isTransmutationTestable && $expectedObject instanceof Transmutable) {
            $this->assertEquals($expectedObject->transmute(), $expectedTransmutedObject);
        }
    }
    
    protected function getTextFeedFromFile($file)
    {
        $path = $this->getTestFileBasePath();
        $fileName = $path . "/" . $file . "." . $this->serializarionMethod;
        if (is_file($fileName)) {
            return file_get_contents($fileName);
        }
        throw new \RuntimeException($this->serializarionMethod . " File not found : $fileName");
    }
    
    protected function getSourceFeedFromFile($file, $throwException = true)
    {
        $path = $this->getTestFileBasePath();
        $fileName = $path . "/" . $file;
        if (is_file($fileName)) {
            include($fileName);
            return $object;
        }
        if ($throwException) throw new \RuntimeException("PHP file not found : $fileName");
        return null;
    }

    protected function getPhpVarFromFile($file)
    {
        return $this->getSourceFeedFromFile($file . ".php");
    }

    protected function getTransmutedPhpVarFromFile($file)
    {
        return $this->getSourceFeedFromFile($file . ".transmuted.php");
    }
    
    /*****************
     * DATA PROVIDERS
     *****************/
    
    public function getObjectsAndFeedsAndTransmutedObjects()
    {
        $objectsAndJsonFeeds = array();
        foreach ($this->testedModels as $testedModel) {
            $object = $this->getPhpVarFromFile($testedModel);
            $objectsAndJsonFeeds[] = array(
                $object,
                $this->getTextFeedFromFile($testedModel),
                ($object instanceof Transmutable) ? $this->getTransmutedPhpVarFromFile($testedModel) : null
            );
        }
        return $objectsAndJsonFeeds;
    }
}