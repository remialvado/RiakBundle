<?php
$object = new \Kbrw\RiakBundle\Model\MapReduce\Query();
$object->on("invoices")
       ->map("function(v){return [v];}")->done()
       ->reduce("function() {return [[v]];}")->done();