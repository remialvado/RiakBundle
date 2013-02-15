<?php

namespace Kbrw\RiakBundle\Model\Bucket;

use Kbrw\RiakBundle\Model\KV\Datas;
use Kbrw\RiakBundle\Model\KV\Data;

class InMemoryBucket implements BucketInterface
{
    /**
     * @var array<string, mixed>
     */
    public $content;

    /**
     * @param string               $name               
     * @param array<string, mixed> $content
     */
    public function __construct($content = array())
    {
        $this->content = $content;
    }

    public function count()
    {
        return count($this->content);
    }

    /**
     * @param array<string> $keys
     * @return boolean
     */
    public function delete($keys)
    {
        if (!is_array($keys)) $keys = array($keys);
        $done = true;
        foreach($keys as $key) {
            if (array_key_exists($key, $this->content)) {
                unset($this->content[$key]);
            }
            else {
                $done = false;
            }
        }
        return $done;
    }

    /**
     * @param array<string> $keys
     * @return \Kbrw\RiakBundle\Model\KV\Datas
     */
    public function fetch($keys)
    {
        $datas = new Datas();
        foreach($keys as $key) {
            if (array_key_exists($key, $this->content)) {
                $datas->add(new Data($key, $this->content[$key]));
            }
        }
        return $datas;
    }
    
    /**
     * @return array<string>
     */
    public function keys()
    {
        return array_keys($this->content);
    }

    /**
     * @param array<string, mixed> $objects
     * @param array<string, mixed> $headers Optional headers (not implemented)
     */
    public function put($objects, $headers = null)
    {
        $this->content = array_merge($this->content, $objects);
        return true;
    }

    public function search($query)
    {
        // Not implemented
        return array();
    }

    /**
     * @param string $key
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function uniq($key)
    {
        $data = new Data($key);
        if (array_key_exists($key, $this->content)) {
            $data->setContent($this->content[$key]);
        }
        return $data;
    }
}
