<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class GreaterThan extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "greater_than", array($compareTo));
    }
}
