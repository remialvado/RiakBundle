<?php
use Kbrw\RiakBundle\Model\MapReduce\Inputs;

$object = new Inputs();
$object
  ->setBucket("invoices")
  ->not()
    ->endsWith("0603")
  ->end();