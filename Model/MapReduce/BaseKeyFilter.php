<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

class BaseKeyFilter extends AbstractKeyFilter
{
    protected $scalars;

    public function __construct($parent = null, $name = null, $scalars = array())
    {
        parent::__construct($parent, $name);
        $this->setScalars($scalars);
    }

    public function getScalars()
    {
        return $this->scalars;
    }

    public function setScalars($scalars)
    {
        $this->scalars = $scalars;
    }

    public function addScalar($scalar)
    {
        $this->scalars[] = $scalar;
    }

    public function toArray()
    {
        return array_merge(array($this->name), $this->scalars);
    }
}
