<?php
use Kbrw\RiakBundle\Model\MapReduce\Inputs;

$object = new Inputs();
$object
  ->setBucket("invoices")
  ->tokenize("-", 2)
  ->logicalAnd()
    ->endsWith("0603")
    ->startsWith("basho")
  ->end();