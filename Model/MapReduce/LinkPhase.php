<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

use JMS\Serializer\Annotation as Ser;

/**
 * @Ser\AccessType("public_method")
 * @Ser\XmlRoot("link_phase")
 */
class LinkPhase extends Phase
{
    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("bucket")
     */
    protected $bucket;
    
    /**
     * @var string
     * @Ser\Type("string")
     * @Ser\SerializedName("tag")
     */
    protected $tag;
    
    /**
     * @var string
     * @Ser\Type("boolean")
     * @Ser\SerializedName("keep")
     */
    protected $keep;
    
    function __construct($bucket = null, $tag = null, $keep = false)
    {
        $this->bucket = $bucket;
        $this->tag = $tag;
        $this->keep = $keep;
    }
    
    public function getBucket()
    {
        return $this->bucket;
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    public function getKeep()
    {
        return $this->keep;
    }

    public function setKeep($keep)
    {
        $this->keep = $keep;
        return $this;
    }
}