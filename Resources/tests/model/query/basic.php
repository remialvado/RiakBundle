<?php
$object = new \Kbrw\RiakBundle\Model\MapReduce\Query();
$object->on("invoices")
       ->map("function(v){return [v];}")
       ->reduce("function() {return [[v]];}");