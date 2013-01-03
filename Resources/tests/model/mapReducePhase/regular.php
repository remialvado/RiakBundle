<?php
use Kbrw\RiakBundle\Model\MapReduce\MapReducePhase;

$object = new MapReducePhase();
$object->setLanguage("javascript")
       ->setSource("function(v) { return [v]; }")
       ->setKeep(true);