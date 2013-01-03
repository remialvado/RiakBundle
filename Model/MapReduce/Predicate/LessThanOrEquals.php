<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class LessThanOrEquals extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "less_than_eq", array($compareTo));
    }
}
