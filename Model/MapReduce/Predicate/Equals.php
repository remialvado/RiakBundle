<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class Equals extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "eq", array($compareTo));
    }
}