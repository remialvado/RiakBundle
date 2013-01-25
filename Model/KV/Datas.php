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

    public function __construct($datas = array())
    {
        $this->setDatas($datas);
    }

    /**
     * @return array<mixed>
     */
    public function getContents($asString = false)
    {
        $objects = array();
        array_walk($this->datas, function($data) use (&$objects, $asString) {
            if (isset($data) && $data->hasContent() )  {
                $objects[] = $data->getContent($asString);
            }
        });

        return $objects;
    }

    /**
     * @return array<\Kbrw\RiakBundle\Model\KV\Data>
     */
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

    /**
     * @return \Kbrw\RiakBundle\Model\KV\Data
     */
    public function first()
    {
        return (is_array($this->datas) && count($this->datas) > 0) ? $this->datas[0] : null;
    }

    public function chunk($size)
    {
        $chunks = array();
        foreach (array_chunk($this->datas, $size, true) as $datasAsArray) {
            $chunks[] = new Datas($datasAsArray);
        }

        return $chunks;
    }
}
