<?php

namespace Kbrw\RiakBundle\Model\KV;

/**
 * @author remi
 */
class Datas
{
    /**
     * @var array<\Kbrw\RiakBundle\Model\KV\Data>
     */
    protected $datas;
    
    function __construct($datas = array())
    {
        $this->setDatas($datas);
    }
    
    /**
     * @return array
     */
    public function getStructuredObjects() {
        $objects = array();
        array_walk($this->datas, function($data) use (&$objects) {
            if (isset($data))
            {
                $objects[] = $data->getStructuredContent();
            }
        });
        return $objects;
    }
    
    public function getDatas()
    {
        return $this->datas;
    }

    public function setDatas($datas)
    {
        $this->datas = $datas;
    }
    
    public function add($data)
    {
        $this->datas[] = $data;
    }
    
    public function addAll($datas)
    {
        $this->datas = array_merge($this->datas, $datas->getDatas());
    }
}