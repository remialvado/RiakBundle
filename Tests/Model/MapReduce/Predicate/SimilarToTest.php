<?php
namespace Kbrw\RiakBundle\Tests\Model\MapReduce\Predicate;

use Kbrw\RiakBundle\Model\MapReduce\Predicate\SimilarTo;
use Kbrw\RiakBundle\Tests\Model\MapReduce\KeyFilterTestCase;

class SimilarToTest extends KeyFilterTestCase
{
    public function getExpectations()
    {
        return array(
            array('[["similar_to","newyork",3]]', new SimilarTo(null, "newyork", 3)),
        );
    }
}
