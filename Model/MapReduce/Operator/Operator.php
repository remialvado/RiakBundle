<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Operator;

use Kbrw\RiakBundle\Model\MapReduce\AbstractKeyFilter;

class Operator extends AbstractKeyFilter
{
    /**
     * @var array<\Kbrw\RiakBundle\Model\MapReduce\KeyFilter>
     */
    protected $children;

    public function __construct($parent = null, $name = null, $children = array())
    {
        parent::__construct($parent, $name);
        $this->setChildren($children);
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function addKeyFilter($keyFilter)
    {
        $this->children[] = $keyFilter;
    }

    public function toArray()
    {
        $content = array($this->name);
        foreach ($this->children as $keyFilter) {
            $content[] = array($keyFilter->toArray());
        }

        return $content;
    }
}
