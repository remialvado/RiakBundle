<?php
use Kbrw\RiakBundle\Model\MapReduce\MapReducePhase;

$object = new MapReducePhase();
$object->setLanguage("erlang")
       ->setModule("riak_mapreduce")
       ->setFunction("map_object_value");