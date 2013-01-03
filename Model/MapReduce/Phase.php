<?php
namespace Kbrw\RiakBundle\Model\MapReduce;

use JMS\Serializer\Annotation as Ser;

abstract class Phase
{
    /**
     * @Ser\Exclude
     * @var \Kbrw\RiakBundle\Model\MapReduce\Query
     */
    protected $query;
    
    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery(\Kbrw\RiakBundle\Model\MapReduce\Query $query)
    {
        $this->query = $query;
    }
    
    /**
     * @return \Kbrw\RiakBundle\Model\MapReduce\Query 
     */
    public function done()
    {
        return $this->query;
    }
}