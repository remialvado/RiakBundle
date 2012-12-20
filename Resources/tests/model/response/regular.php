<?php
use Kbrw\RiakBundle\Model\Search\Response;
use Kbrw\RiakBundle\Model\Search\Result;
use Kbrw\RiakBundle\Model\Search\Document;
use Kbrw\RiakBundle\Model\Search\Lst;
use Kbrw\RiakBundle\Model\Search\Integer;
use Kbrw\RiakBundle\Model\Search\String;

$object = new Response(
            new Result(
                "response",
                1,
                0,
                0.353553,
                array(
                    new Document(array(new String("id", "remi")))
                )
            ),
            array(
              new Lst(
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
              )
            )
        );