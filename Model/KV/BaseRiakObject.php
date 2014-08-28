<?php

namespace Kbrw\RiakBundle\Model\KV;

use JMS\Serializer\Annotation as Ser;

class BaseRiakObject
{
    /**
     * @var string
     * @Ser\Exclude()
     */
    protected $riakVectorClock;

    /**
     * @var \Kbrw\RiakBundle\Model\KV\Link[]
     * @Ser\Exclude()
     */
    protected $riakLinks;

    function __construct($riakVectorClock = null, $riakLinks = array())
    {
        $this->riakVectorClock = $riakVectorClock;
        $this->riakLinks = $riakLinks;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\KV\Link[] $riakLinks
     */
    public function setRiakLinks($riakLinks)
    {
        $this->riakLinks = $riakLinks;
    }

    /**
     * @return \Kbrw\RiakBundle\Model\KV\Link[]
     */
    public function getRiakLinks()
    {
        return $this->riakLinks;
    }

    /**
     * @param \Kbrw\RiakBundle\Model\KV\Link $riakLink
     */
    public function addRiakLink($riakLink)
    {
        $this->riakLinks[] = $riakLink;
    }

    /**
     * @param string $riakVectorClock
     */
    public function setRiakVectorClock($riakVectorClock)
    {
        $this->riakVectorClock = $riakVectorClock;
    }

    /**
     * @return string
     */
    public function getRiakVectorClock()
    {
        return $this->riakVectorClock;
    }
}