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
}