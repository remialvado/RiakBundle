<?php
use Kbrw\RiakBundle\Model\Search\Result;
use Kbrw\RiakBundle\Model\Search\Document;
use Kbrw\RiakBundle\Model\Search\String;

$object = new Result(
        "response",
        1,
        0,
        0.353553,
        array(
            new Document(array(new String("id", "remi")))
        )
);