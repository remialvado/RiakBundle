<?php
use Kbrw\RiakBundle\Model\MapReduce\Inputs;

$object = new Inputs();
$object
  ->setBucket("invoices")
  ->logicalAnd()
    ->endsWith("0603")
    ->startsWith("basho")
  ->end();