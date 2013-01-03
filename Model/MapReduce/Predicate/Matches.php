<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class Matches extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "matches", array($compareTo));
    }
}