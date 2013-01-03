<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Operator;

class Root extends Operator
{
    public function toArray()
    {
        if (count($this->children) === 1 && $this->children[0] instanceof Operator) {
            $keyFilter = $this->children[0];
            return $keyFilter->toArray();
        }
        $content = array();
        foreach($this->children as $keyFilter) {
            $content[] = $keyFilter->toArray();
        }
        return $content;
    }
}