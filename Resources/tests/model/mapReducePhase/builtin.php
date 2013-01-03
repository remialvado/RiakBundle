<?php
use Kbrw\RiakBundle\Model\MapReduce\MapReducePhase;

$object = new MapReducePhase();
$object->setLanguage("javascript")
       ->setName("Riak.mapValuesJson");