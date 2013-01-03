<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Predicate;

class SimilarTo extends Predicate
{
    public function __construct($parent = null, $compareTo = null, $distance = 1)
    {
        parent::__construct($parent, "similar_to", array($compareTo, $distance));
    }
}