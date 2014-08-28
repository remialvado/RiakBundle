<?php

namespace Kbrw\RiakBundle\Model\KV;

class Link
{
    /**
     * @var string
     */
    protected $kv;

    /**
     * @var string
     */
    protected $riakTag;

    /**
     * @param string $kv
     * @param string $riakTag
     */
    function __construct($kv = null, $riakTag = null)
    {
        $this->kv = $kv;
        $this->riakTag = $riakTag;
    }

    /**
     * @param string $kv
     */
    public function setKv($kv)
    {
        $this->kv = $kv;
    }

    /**
     * @return string
     */
    public function getKv()
    {
        return $this->kv;
    }

    /**
     * @param string $riakTag
     */
    public function setRiakTag($riakTag)
    {
        $this->riakTag = $riakTag;
    }

    /**
     * @return string
     */
    public function getRiakTag()
    {
        return $this->riakTag;
    }
}