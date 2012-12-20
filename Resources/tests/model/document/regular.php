<?php
use Kbrw\RiakBundle\Model\Search\Document;
use Kbrw\RiakBundle\Model\Search\String;

$object = new Document(
            array(
                new String("id", "remi")
            )
);