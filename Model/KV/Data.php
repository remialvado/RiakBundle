<?php

namespace Kbrw\RiakBundle\Model\KV;

use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * @author remi
 */
class Data 
{
    /**
     * @var string 
     */
    protected $key;
    
    /**
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    protected $headerBag;
    
    /**
     * @var string
     */
    protected $stringContent;
    
    /**
     * @var mixed
     */
    protected $content;
    
    function __construct($key = null, $headerBag = null, $stringContent = null, $content = null)
    {
        $this->setKey($key);
        if (!isset($headerBag)) $headerBag = new HeaderBag();
        $this->setHeaderBag($headerBag);
        $this->setStringContent($stringContent);
        $this->setContent($content);
    }
    
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\HeaderBag
     */
    public function getHeaderBag()
    {
        return $this->headerBag;
    }

    public function setHeaderBag($headerBag)
    {
        $this->headerBag = $headerBag;
    }

    /**
     * @return mixed
     */
    public function getContent($asString = false)
    {
        return (!$asString && isset($this->content)) ? $this->content : $this->stringContent;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setStringContent($stringContent)
    {
        $this->stringContent = $stringContent;
    }
}