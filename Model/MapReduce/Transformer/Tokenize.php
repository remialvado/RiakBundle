<?php
namespace Kbrw\RiakBundle\Model\MapReduce\Transformer;

class Tokenize extends Transformer
{
    public function __construct($parent = null, $separator = "-", $position = 1)
    {
        parent::__construct($parent, "tokenize", array($separator, $position));
    }
}