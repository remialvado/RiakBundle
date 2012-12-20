<?php
use Kbrw\RiakBundle\Model\Search\Integer;
use Kbrw\RiakBundle\Model\Search\String;
use Kbrw\RiakBundle\Model\Search\Lst;

$object = new Lst(
        "responseHeader",
        array(
            new Integer("status", 0),
            new Integer("QTime", 2)
        ),
        array(),
        array(
            new Lst(
                "params",
                array(),
                array(
                    new String("indent", "on"),
                    new String("start", "0"),
                    new String("q", "id:remi"),
                ),
                array()
            )
        )
);