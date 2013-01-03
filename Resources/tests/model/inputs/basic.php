<?php
use Kbrw\RiakBundle\Model\MapReduce\Inputs;

$object = new Inputs();
$object
  ->setBucket("invoices")
  ->startsWith("foo");