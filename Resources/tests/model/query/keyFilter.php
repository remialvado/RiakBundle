<?php
$object = new \Kbrw\RiakBundle\Model\MapReduce\Query();
$object->filter("invoices")
         ->endsWith("0603")
       ->done()
       ->map("function(v){return [v];}")->done()
       ->reduce("function() {return [[v]];}")->done();