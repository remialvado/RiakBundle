<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

class AbstractKeyFilter implements KeyFilter
{
    /**
     * @var \Kbrw\RiakBundle\Model\MapReduce\KeyFilter
     */
    protected $parent;
    
    /**
     * @var string
     */
    protected $name;
    
    function __construct($parent = null, $name = null)
    {
        $this->setParent($parent);
        $this->setName($name);
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\KeyFilter
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function toArray()
    {
        return array($this->name);
    }
}