<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class LessThan extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "less_than", array($compareTo));
    }
}
