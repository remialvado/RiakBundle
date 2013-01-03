<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class NotEquals extends Predicate
{
    public function __construct($parent = null, $compareTo = null)
    {
        parent::__construct($parent, "neq", array($compareTo));
    }
}
