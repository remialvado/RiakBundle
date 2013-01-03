<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Operator;

class LogicalAnd extends Operator
{
    public function __construct($parent = null, $children = array())
    {
        parent::__construct($parent, "and", $children);
    }
}