<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class Between extends Predicate
{
    public function __construct($parent = null, $min = null, $max = null, $inclusive = true)
    {
        parent::__construct($parent, "between", array($min, $max, $inclusive));
    }
}
