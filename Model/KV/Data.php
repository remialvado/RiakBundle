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
     * @var mixed
     */
    protected $content;
    
    /**
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    protected $headerBag;
    
    /**
     * @var string
     */
    protected $stringContent;
    
    function __construct($key = null, $content = null, $headerBag = null, $stringContent = null)
    {
        $this->setKey($key);
        $this->setContent($content);
        if (!isset($headerBag)) $headerBag = new HeaderBag();
        $this->setHeaderBag($headerBag);
        $this->setStringContent($stringContent);
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
    
    public function isDefined()
    {        
        return isset($this->content);
    }
}