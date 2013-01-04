<?php
$object = new \Kbrw\RiakBundle\Model\MapReduce\Query();
$object->on("invoices")
       ->configureMapPhase()
         ->setLanguage("erlang")
         ->setModule("riak_mapreduce")
         ->setFunction("map_object_value")
       ->done()
       ->timeout(10000);