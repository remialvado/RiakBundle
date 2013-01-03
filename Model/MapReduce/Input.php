<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

class Input
{
    protected $bucket;
    protected $key;
    protected $data;
    
    function __construct($bucket, $key = null, $data = null)
    {
        $this->bucket = $bucket;
        $this->key = $key;
        $this->data = $data;
    }
    
    public function toArray()
    {
        $content = array($this->bucket);
        if (isset($this->key))  $content[] = $this->key;
        if (isset($this->data)) $content[] = $this->data;
        return $content;
    }
    
    public function getBucket()
    {
        return $this->bucket;
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}