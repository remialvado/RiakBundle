<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class StartsWith extends Predicate
{
    public function __construct($parent = null, $start = null)
    {
        parent::__construct($parent, "starts_with", array($start));
    }
}
