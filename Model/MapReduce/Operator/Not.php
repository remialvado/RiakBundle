<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Operator;

class Not extends Operator
{
    public function __construct($parent = null, $children = array())
    {
        parent::__construct($parent, "not", $children);
    }
}
