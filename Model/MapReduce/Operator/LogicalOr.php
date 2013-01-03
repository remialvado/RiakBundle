<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Operator;

class LogicalOr extends Operator
{
    public function __construct($parent = null, $children = array())
    {
        parent::__construct($parent, "or", $children);
    }
}