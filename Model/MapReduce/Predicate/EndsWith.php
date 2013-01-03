<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class EndsWith extends Predicate
{
    public function __construct($parent = null, $end = null)
    {
        parent::__construct($parent, "ends_with", array($end));
    }
}