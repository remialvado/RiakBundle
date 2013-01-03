<?php
use Kbrw\RiakBundle\Model\MapReduce\MapReducePhase;

$object = new MapReducePhase();
$object->setLanguage("javascript")
       ->setBucket("myjs")
       ->setKey("mymap")
       ->setKeep(false);