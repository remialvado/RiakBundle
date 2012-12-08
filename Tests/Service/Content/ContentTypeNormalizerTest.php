<?php
namespace Kbrw\RiakBundle\Tests\Service\Content;

use Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer;

/**
 * @author remi
 */
class ContentTypeNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Kbrw\RiakBundle\Service\Content\ContentTypeNormalizer
     */
    protected $contentTypeNormalizer;
    
    public function setup()
    {
        $this->contentTypeNormalizer = new ContentTypeNormalizer(array(
            "application/xml" => array("xml", "xsd", "xslt"),
            "application/json" => array("json")
        ));
    }
    
    /**
     * @test
     */
    public function getExistingNormalizedContentType()
    {
        $this->assertEquals($this->contentTypeNormalizer->getNormalizedContentType("application/json"), "json");
        $this->assertEquals($this->contentTypeNormalizer->getNormalizedContentType("application/xml"), "xml");
    }
    
    /**
     * @test
     */
    public function getUnexistingNormalizedContentType()
    {
        $this->assertNull($this->contentTypeNormalizer->getNormalizedContentType("foo/bar", null));
        $this->assertNull($this->contentTypeNormalizer->getNormalizedContentType("foo/bar"));
    }
    
    /**
     * @test
     */
    public function getExistingContentType()
    {
        $this->assertEquals($this->contentTypeNormalizer->getContentType("json"), "application/json");
        $this->assertEquals($this->contentTypeNormalizer->getContentType("xml"),  "application/xml");
        $this->assertEquals($this->contentTypeNormalizer->getContentType("xsd"),  "application/xml");
    }
    
    /**
     * @test
     */
    public function getUnexistingContentType()
    {
        $this->assertNull($this->contentTypeNormalizer->getContentType("foo", null));
        $this->assertEquals($this->contentTypeNormalizer->getContentType("foo"), "text/plain");
    }
}